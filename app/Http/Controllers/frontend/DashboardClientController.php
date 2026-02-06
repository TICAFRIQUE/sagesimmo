<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardClientController extends Controller
{
    /**
     * Afficher le dashboard du client
     */
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer les ventes et locations du client
        $ventes = \App\Models\Vente::with('annonce', 'paiements')->where('client_id', $user->id)->get();
        $locations = \App\Models\Location::with('annonce', 'paiements', 'echeances')->where('locataire_id', $user->id)->get();
        
        // Statistiques générales
        $totalDemandes = $ventes->count() + $locations->count();
        $demandesEnCours = $ventes->whereNotIn('statut', ['annule', 'paiement_valide'])->count() + 
                          $locations->whereNotIn('statut', ['annule', 'actif', 'termine', 'resilie'])->count();
        $demandesFinalisees = $ventes->where('statut', 'paiement_valide')->count() + 
                             $locations->where('statut', 'actif')->count();
        $demandesVisites = $ventes->where('statut', 'visite_planifiee')->count() + 
                          $locations->where('statut', 'visite_planifiee')->count();
        
        // Dernières demandes (fusionner ventes et locations)
        $dernieresDemandes = collect()
            ->merge($ventes->map(function($v) { $v->type_transaction = 'vente'; return $v; }))
            ->merge($locations->map(function($l) { $l->type_transaction = 'location'; return $l; }))
            ->sortByDesc('created_at')
            ->take(5);
        
        // Prochaines visites
        $prochainesVisites = collect()
            ->merge($ventes->where('statut', 'visite_planifiee')->whereNotNull('date_visite'))
            ->merge($locations->where('statut', 'visite_planifiee')->whereNotNull('date_visite'))
            ->filter(function($item) {
                return $item->date_visite >= now();
            })
            ->sortBy('date_visite');
        
        // Biens loués/achetés
        $biensLoues = $locations->where('statut', 'actif');
        $biensAchetes = $ventes->where('statut', 'paiement_valide');
        
        // ========== DONNÉES PROPRIÉTAIRE ==========
        $biensProprio = \App\Models\Annonce::with(['locations.paiements', 'locations.echeances', 'media', 'typeBien', 'ventes.paiements'])
            ->where('proprietaire_id', $user->id)
            ->get();
        
        $nombreBiensProprio = $biensProprio->count();
        $biensDisponibles = $biensProprio->where('statut', 'disponible')->count();
        $biensLouesProprio = $biensProprio->where('statut', 'loue')->count();
        $biensVendusProprio = $biensProprio->where('statut', 'vendu')->count();
        
        // Revenus encaissés (uniquement les paiements validés des locations)
        $revenusEncaisses = 0;
        $loyersImpayes = 0;
        $prochaineEcheanceProprio = null;
        $commissionAgenceTotal = 0;
        $revenuNetTotal = 0;
        
        // Détails par bien pour le propriétaire
        $detailsBiensProprio = [];
        $revenusMensuels = [];
        
        foreach ($biensProprio as $bien) {
            $detailBien = [
                'bien' => $bien,
                'loyers_payes' => 0,
                'loyers_impayes' => 0,
                'commission_agence' => 0,
                'revenu_net' => 0,
                'location_active' => null,
                'vente_finalisee' => null,
            ];
            
            // Locations du bien
            foreach ($bien->locations as $location) {
                // Revenus encaissés (loyers payés)
                $loyersPayes = $location->paiements()
                    ->where('statut', 'paye')
                    ->where('type_paiement', 'loyer')
                    ->sum('montant');
                
                $revenusEncaisses += $loyersPayes;
                $detailBien['loyers_payes'] += $loyersPayes;
                
                // Commission agence sur les loyers
                $commissionLoyers = $location->paiements()
                    ->where('statut', 'paye')
                    ->where('type_paiement', 'loyer')
                    ->sum('commission_agence');
                
                $commissionAgenceTotal += $commissionLoyers;
                $detailBien['commission_agence'] += $commissionLoyers;
                
                // Loyers impayés (échéances en retard)
                $impayesLocation = $location->echeances()
                    ->where('statut', 'impaye')
                    ->where('date_echeance', '<', now())
                    ->get()
                    ->sum(function($echeance) {
                        return $echeance->montant_du - $echeance->montant_paye;
                    });
                
                $loyersImpayes += $impayesLocation;
                $detailBien['loyers_impayes'] += $impayesLocation;
                
                // Location active
                if ($location->statut == 'actif') {
                    $detailBien['location_active'] = $location;
                }
                
                // Prochaine échéance
                $prochaineEch = $location->echeances()
                    ->where('statut', 'a_echeance')
                    ->where('date_echeance', '>=', now())
                    ->orderBy('date_echeance')
                    ->first();
                    
                if ($prochaineEch && (!$prochaineEcheanceProprio || $prochaineEch->date_echeance < $prochaineEcheanceProprio->date_echeance)) {
                    $prochaineEcheanceProprio = $prochaineEch;
                }
                
                // Revenus mensuels (groupés par mois)
                $paiementsLoyers = $location->paiements()
                    ->where('statut', 'paye')
                    ->where('type_paiement', 'loyer')
                    ->get();
                
                foreach ($paiementsLoyers as $paiement) {
                    $mois = \Carbon\Carbon::parse($paiement->date_paiement)->format('Y-m');
                    if (!isset($revenusMensuels[$mois])) {
                        $revenusMensuels[$mois] = [
                            'mois' => $mois,
                            'brut' => 0,
                            'commission' => 0,
                            'net' => 0,
                        ];
                    }
                    $revenusMensuels[$mois]['brut'] += $paiement->montant;
                    $revenusMensuels[$mois]['commission'] += $paiement->commission_agence ?? 0;
                    $revenusMensuels[$mois]['net'] += $paiement->montant - ($paiement->commission_agence ?? 0);
                }
            }
            
            // Ventes du bien
            $venteFinalisee = $bien->ventes()->where('statut', 'paiement_valide')->first();
            if ($venteFinalisee) {
                $detailBien['vente_finalisee'] = $venteFinalisee;
                
                // Montant vente encaissé
                $montantVente = $venteFinalisee->paiements()
                    ->where('statut', 'paye')
                    ->sum('montant');
                
                // Commission sur vente
                $commissionVente = $venteFinalisee->commission_agence ?? 0;
                $commissionAgenceTotal += $commissionVente;
                $detailBien['commission_agence'] += $commissionVente;
            }
            
            // Calculer revenu net du bien
            $detailBien['revenu_net'] = $detailBien['loyers_payes'] - $detailBien['commission_agence'];
            $revenuNetTotal += $detailBien['revenu_net'];
            
            $detailsBiensProprio[] = $detailBien;
        }
        
        // Trier les revenus mensuels par date décroissante
        krsort($revenusMensuels);
        $revenusMensuels = array_values(array_slice($revenusMensuels, 0, 6)); // 6 derniers mois
        
        // ========== DONNÉES LOCATAIRE ==========
        $locationActive = $locations->where('statut', 'actif')->first();
        $prochaineEcheanceLocataire = null;
        $avanceRestante = 0;
        $impayesLocataire = 0;
        $cautionStatut = null;
        
        if ($locationActive) {
            // Prochaine échéance
            $prochaineEcheanceLocataire = $locationActive->echeances()
                ->where('statut', 'a_echeance')
                ->where('date_echeance', '>=', now())
                ->orderBy('date_echeance')
                ->first();
            
            // Avance restante
            if ($locationActive->montant_avance) {
                $totalConsomme = $locationActive->echeances()
                    ->where('statut', 'paye')
                    ->sum('montant_du');
                $avanceRestante = max(0, $locationActive->montant_avance - $totalConsomme);
            }
            
            // Impayés
            $impayesLocataire = $locationActive->echeances()
                ->where('statut', 'impaye')
                ->where('date_echeance', '<', now())
                ->get()
                ->sum(function($echeance) {
                    return $echeance->montant_du - $echeance->montant_paye;
                });
            
            // Statut caution (à définir selon votre logique métier)
            $cautionStatut = $locationActive->caution > 0 ? 'En dépôt' : 'Non requis';
        }
        
        // ========== DONNÉES ACHETEUR ==========
        $venteActive = $ventes->where('statut', 'paiement_valide')->first();
        $montantPaye = 0;
        $montantRestant = 0;
        $documentsVente = [];
        $remiseCles = false;
        
        if ($venteActive) {
            $montantPaye = $venteActive->paiements()
                ->where('statut', 'paye')
                ->sum('montant');
            $montantRestant = max(0, $venteActive->prix_vente - $montantPaye);
            // Remise des clés - à définir selon votre logique
            $remiseCles = ($venteActive->statut == 'paiement_valide' && $montantRestant == 0);
            
            // Documents (si vous avez une collection media)
            if (method_exists($venteActive, 'getMedia')) {
                $documentsVente = $venteActive->getMedia('documents');
            }
        }
        
        // Vérifier les rôles de l'utilisateur
        $estProprietaire = ($user->roles->contains('name', 'proprietaire')) || $nombreBiensProprio > 0;
        $estLocataire = ($user->roles->contains('name', 'locataire')) || $locationActive != null;
        $estAcheteur = ($user->roles->contains('name', 'acheteur')) || $venteActive != null;
        
        return view('frontend.pages.client.dashboard', compact(
            'totalDemandes',
            'demandesEnCours',
            'demandesFinalisees',
            'demandesVisites',
            'dernieresDemandes',
            'prochainesVisites',
            'biensLoues',
            'biensAchetes',
            // Données propriétaire
            'estProprietaire',
            'nombreBiensProprio',
            'biensDisponibles',
            'biensLouesProprio',
            'biensVendusProprio',
            'biensProprio',
            'revenusEncaisses',
            'loyersImpayes',
            'prochaineEcheanceProprio',
            'commissionAgenceTotal',
            'revenuNetTotal',
            'detailsBiensProprio',
            'revenusMensuels',
            // Données locataire
            'estLocataire',
            'locationActive',
            'prochaineEcheanceLocataire',
            'avanceRestante',
            'impayesLocataire',
            'cautionStatut',
            // Données acheteur
            'estAcheteur',
            'venteActive',
            'montantPaye',
            'montantRestant',
            'documentsVente',
            'remiseCles'
        ));
    }

    /**
     * Afficher le profil du client
     */
    public function profil()
    {
        $user = Auth::user();
        return view('frontend.pages.client.profil', compact('user'));
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfil(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);
        
        $user->update([
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);
        
        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Changer le mot de passe
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = User::findOrFail(Auth::id());
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }
        
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        
        return back()->with('success', 'Mot de passe modifié avec succès.');
    }

    /**
     * Afficher la liste des demandes du client
     */
    public function demandes(Request $request)
    {
        $user = Auth::user();
        
        // Récupérer les ventes et locations du client
        $ventes = \App\Models\Vente::with('annonce')
            ->where('client_id', $user->id)
            ->get();
            
        $locations = \App\Models\Location::with('annonce')
            ->where('locataire_id', $user->id)
            ->get();
        
        // Fusionner et trier par date de création
        $demandes = collect()
            ->merge($ventes->map(function($vente) {
                $vente->type_transaction = 'vente';
                return $vente;
            }))
            ->merge($locations->map(function($location) {
                $location->type_transaction = 'location';
                return $location;
            }))
            ->sortByDesc('created_at');
        
        // Filtrer par statut si nécessaire
        if ($request->filled('statut')) {
            $demandes = $demandes->where('statut', $request->statut);
        }
        
        // Paginer manuellement
        $page = $request->get('page', 1);
        $perPage = 10;
        $demandes = new \Illuminate\Pagination\LengthAwarePaginator(
            $demandes->forPage($page, $perPage)->values(),
            $demandes->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        return view('frontend.pages.client.demandes.index', compact('demandes'));
    }

    /**
     * Afficher les détails d'une demande
     */
    public function showDemande($id)
    {
        $user = Auth::user();
        
        // Chercher d'abord dans les ventes
        $demande = \App\Models\Vente::with('annonce')
            ->where('client_id', $user->id)
            ->where('id', $id)
            ->first();
        
        if ($demande) {
            $demande->type_transaction = 'vente';
        } else {
            // Sinon chercher dans les locations
            $demande = \App\Models\Location::with('annonce')
                ->where('locataire_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();
            $demande->type_transaction = 'location';
        }
        
        return view('frontend.pages.client.demandes.show', compact('demande'));
    }

    /**
     * Annuler une demande
     */
    public function cancelDemande($id)
    {
        $user = Auth::user();
        
        // Chercher dans les ventes avec statut demande_client
        $vente = \App\Models\Vente::where('client_id', $user->id)
            ->where('id', $id)
            ->where('statut', 'demande_client')
            ->first();
        
        if ($vente) {
            $vente->update(['statut' => 'annule']);
            return redirect()->route('client.demandes')->with('success', 'Demande annulée avec succès.');
        }
        
        // Sinon chercher dans les locations avec statut demande_client
        $location = \App\Models\Location::where('locataire_id', $user->id)
            ->where('id', $id)
            ->where('statut', 'demande_client')
            ->first();
        
        if ($location) {
            $location->update(['statut' => 'annule']);
            return redirect()->route('client.demandes')->with('success', 'Demande annulée avec succès.');
        }
        
        return redirect()->route('client.demandes')->with('error', 'Demande introuvable ou déjà traitée.');
    }

    /**
     * Espace Propriétaire
     */
    public function espaceProprietaire(Request $request)
    {
        $user = Auth::user();
        
        // Filtre
        $filtre = $request->get('filtre', 'tous');
        
        // Récupérer tous les biens pour les statistiques globales
        $tousLesBiens = \App\Models\Annonce::where('proprietaire_id', $user->id)->get();
        $nombreBiensProprio = $tousLesBiens->count();
        
        // Vérifier si l'utilisateur a des biens
        if ($nombreBiensProprio == 0) {
            return redirect()->route('client.dashboard')->with('info', 'Vous n\'avez pas encore de bien confié à l\'agence.');
        }
        
        // Statistiques des biens
        $biensDisponibles = $tousLesBiens->where('statut', 'disponible')->count();
        $biensLouesProprio = $tousLesBiens->where('statut', 'loue')->count();
        $biensVendusProprio = $tousLesBiens->where('statut', 'vendu')->count();
        
        // Query pour la pagination avec filtre
        $query = \App\Models\Annonce::with(['locations.echeances', 'locations.paiements', 'ventes.paiements', 'typeBien'])
            ->where('proprietaire_id', $user->id);
        
        if ($filtre == 'loues') {
            $query->where('statut', 'loue');
        } elseif ($filtre == 'vendus') {
            $query->where('statut', 'vendu');
        } elseif ($filtre == 'disponibles') {
            $query->where('statut', 'disponible');
        }
        
        $biensProprio = $query->orderBy('created_at', 'desc')->paginate(10);
        $biensProprio->appends(['filtre' => $filtre]);
        
        // Calculs financiers globaux (sur tous les biens)
        $revenusEncaisses = 0;
        $loyersImpayes = 0;
        $commissionAgenceTotal = 0;
        $revenuNetTotal = 0;
        $prochaineEcheanceProprio = null;
        $revenusMensuels = [];
        
        // Calculer les statistiques globales
        foreach ($tousLesBiens as $bien) {
            $locations = \App\Models\Location::with(['echeances', 'paiements'])
                ->where('annonce_id', $bien->id)
                ->whereIn('statut', ['actif', 'termine'])
                ->get();
            
            foreach ($locations as $location) {
                foreach ($location->echeances as $echeance) {
                    if ($echeance->statut == 'paye') {
                        $revenusEncaisses += $echeance->montant_paye;
                        
                        $mois = \Carbon\Carbon::parse($echeance->date_echeance)->format('Y-m');
                        if (!isset($revenusMensuels[$mois])) {
                            $revenusMensuels[$mois] = ['mois' => $mois, 'brut' => 0, 'commission' => 0, 'net' => 0];
                        }
                        $revenusMensuels[$mois]['brut'] += $echeance->montant_paye;
                    }
                    
                    if ($echeance->statut == 'impaye') {
                        $loyersImpayes += ($echeance->montant_du - $echeance->montant_paye);
                    }
                }
                
                foreach ($location->paiements as $paiement) {
                    if ($paiement->statut == 'valide' && isset($paiement->commission_agence)) {
                        $commissionAgenceTotal += $paiement->commission_agence;
                        
                        $mois = \Carbon\Carbon::parse($paiement->date_paiement)->format('Y-m');
                        if (!isset($revenusMensuels[$mois])) {
                            $revenusMensuels[$mois] = ['mois' => $mois, 'brut' => 0, 'commission' => 0, 'net' => 0];
                        }
                        $revenusMensuels[$mois]['commission'] += $paiement->commission_agence;
                    }
                }
            }
            
            // Ventes finalisées
            $ventes = \App\Models\Vente::with('paiements')
                ->where('annonce_id', $bien->id)
                ->where('statut', 'terminee')
                ->get();
            
            foreach ($ventes as $vente) {
                // Obtenir le montant total payé
                $montantVentePaye = $vente->montantTotalPaye();
                $revenusEncaisses += $montantVentePaye;
                
                // Ajouter la commission
                $commissionVente = $vente->calculerCommission();
                $commissionAgenceTotal += $commissionVente;
                
                // Ajouter aux revenus mensuels
                if ($vente->date_finalisation) {
                    $mois = \Carbon\Carbon::parse($vente->date_finalisation)->format('Y-m');
                    if (!isset($revenusMensuels[$mois])) {
                        $revenusMensuels[$mois] = ['mois' => $mois, 'brut' => 0, 'commission' => 0, 'net' => 0];
                    }
                    $revenusMensuels[$mois]['brut'] += $montantVentePaye;
                    $revenusMensuels[$mois]['commission'] += $commissionVente;
                }
            }
        }
        
        $revenuNetTotal = $revenusEncaisses - $commissionAgenceTotal;
        
        // Calculer net mensuel
        foreach ($revenusMensuels as $key => $revenu) {
            $revenusMensuels[$key]['net'] = $revenu['brut'] - $revenu['commission'];
        }
        
        // Trier par mois décroissant et prendre les 6 derniers
        krsort($revenusMensuels);
        $revenusMensuels = array_values(array_slice($revenusMensuels, 0, 6));
        
        // Détails pour chaque bien paginé
        $detailsBiensProprio = [];
        foreach ($biensProprio as $bien) {
            $detailBien = [
                'bien' => $bien,
                'loyers_payes' => 0,
                'loyers_impayes' => 0,
                'commission_agence' => 0,
                'revenu_net' => 0,
                'location_active' => null,
                'vente_finalisee' => null,
                'vente_prix' => 0,
                'vente_montant_paye' => 0,
                'vente_date' => null,
            ];
            
            // Locations actives ou terminées
            foreach ($bien->locations as $location) {
                if (in_array($location->statut, ['actif', 'termine'])) {
                    $detailBien['location_active'] = $location;
                    
                    foreach ($location->echeances as $echeance) {
                        if ($echeance->statut == 'paye') {
                            $detailBien['loyers_payes'] += $echeance->montant_paye;
                        }
                        
                        if ($echeance->statut == 'impaye') {
                            $detailBien['loyers_impayes'] += ($echeance->montant_du - $echeance->montant_paye);
                        }
                    }
                    
                    foreach ($location->paiements as $paiement) {
                        if ($paiement->statut == 'valide' && isset($paiement->commission_agence)) {
                            $detailBien['commission_agence'] += $paiement->commission_agence;
                        }
                    }
                }
            }
            
            // Ventes finalisées
            foreach ($bien->ventes as $vente) {
                if ($vente->statut == 'terminee') {
                    $detailBien['vente_finalisee'] = $vente;
                    $detailBien['vente_prix'] = $vente->prix_vente;
                    $detailBien['vente_montant_paye'] = $vente->montantTotalPaye();
                    $detailBien['vente_date'] = $vente->date_finalisation;
                    
                    // Calculer la commission de la vente
                    $commissionVente = $vente->calculerCommission();
                    $detailBien['commission_agence'] += $commissionVente;
                    
                    // Ajouter le montant de la vente aux revenus du bien
                    $detailBien['loyers_payes'] += $vente->montantTotalPaye();
                }
            }
            
            $detailBien['revenu_net'] = $detailBien['loyers_payes'] - $detailBien['commission_agence'];
            $detailsBiensProprio[] = $detailBien;
        }
        
        // Prochaine échéance
        $prochaineEcheanceProprio = \App\Models\Echeance::whereHas('location', function($q) use ($user) {
            $q->whereHas('annonce', function($q2) use ($user) {
                $q2->where('proprietaire_id', $user->id);
            });
        })
        ->where('statut', 'a_echeance')
        ->where('date_echeance', '>=', now())
        ->orderBy('date_echeance')
        ->first();
        
        return view('frontend.pages.client.espaces.proprietaire', compact(
            'nombreBiensProprio',
            'biensDisponibles',
            'biensLouesProprio',
            'biensVendusProprio',
            'biensProprio',
            'revenusEncaisses',
            'loyersImpayes',
            'prochaineEcheanceProprio',
            'commissionAgenceTotal',
            'revenuNetTotal',
            'detailsBiensProprio',
            'revenusMensuels',
            'filtre'
        ));
    }

    /**
     * Onglet Locations - Dashboard Propriétaire
     */
    public function espaceProprietaireLocations()
    {
        $user = Auth::user();
        
        // Récupérer tous les biens en location
        $biensAvecLocations = \App\Models\Annonce::with(['locations.echeances', 'locations.paiements', 'locations.locataire', 'typeBien'])
            ->where('proprietaire_id', $user->id)
            ->whereHas('locations', function($q) {
                $q->where('statut', 'actif');
            })
            ->get();
        
        if ($biensAvecLocations->count() == 0) {
            return redirect()->route('client.proprietaire')->with('info', 'Vous n\'avez pas de bien en location actuellement.');
        }
        
        // KPI Locations
        $loyersMoisCourant = 0;
        $loyersImpayes = 0;
        $totalBiensLoues = $biensAvecLocations->count();
        $totalBiensProprio = \App\Models\Annonce::where('proprietaire_id', $user->id)->count();
        $tauxOccupation = $totalBiensProprio > 0 ? ($totalBiensLoues / $totalBiensProprio) * 100 : 0;
        
        // Détails par bien
        $biensLocations = [];
        foreach ($biensAvecLocations as $bien) {
            $locationActive = $bien->locations->where('statut', 'actif')->first();
            
            if (!$locationActive) continue;
            
            $loyerMensuel = $locationActive->loyer_mensuel;
            $commissionPct = $locationActive->commission_agence ?? 0;
            $revenuNetMensuel = $loyerMensuel - (($loyerMensuel * $commissionPct) / 100);
            
            // Dernière échéance
            $derniereEcheance = $locationActive->echeances()
                ->orderBy('date_echeance', 'desc')
                ->first();
            
            $statutPaiement = 'aucune';
            if ($derniereEcheance) {
                $statutPaiement = $derniereEcheance->statut;
                
                if ($derniereEcheance->statut == 'paye' && $derniereEcheance->date_echeance->isCurrentMonth()) {
                    $loyersMoisCourant += $derniereEcheance->montant_paye;
                }
                
                if ($derniereEcheance->statut == 'impaye') {
                    $loyersImpayes += ($derniereEcheance->montant_du - $derniereEcheance->montant_paye);
                }
            }
            
            $biensLocations[] = [
                'bien' => $bien,
                'location_active' => $locationActive,
                'locataire' => $locationActive->locataire,
                'loyer_mensuel' => $loyerMensuel,
                'commission_pct' => $commissionPct,
                'revenu_net_mensuel' => $revenuNetMensuel,
                'derniere_echeance' => $derniereEcheance,
                'statut_paiement' => $statutPaiement,
            ];
        }
        
        // Historique mensuel des 12 derniers mois
        $revenusMensuels12Mois = [];
        for ($i = 11; $i >= 0; $i--) {
            $mois = now()->subMonths($i);
            $moisKey = $mois->format('Y-m');
            
            $paiementsMois = \App\Models\Paiement::whereHas('location', function($q) use ($user) {
                $q->whereHas('annonce', function($q2) use ($user) {
                    $q2->where('proprietaire_id', $user->id);
                });
            })
            ->whereYear('date_paiement', $mois->year)
            ->whereMonth('date_paiement', $mois->month)
            ->where('statut', 'valide')
            ->get();
            
            $brut = $paiementsMois->sum('montant');
            $commission = $paiementsMois->sum('commission_agence');
            
            $revenusMensuels12Mois[] = [
                'mois' => $moisKey,
                'brut' => $brut,
                'commission' => $commission,
                'net' => $brut - $commission,
            ];
        }
        
        return view('frontend.pages.client.espaces.proprietaire.locations', compact(
            'biensLocations',
            'loyersMoisCourant',
            'tauxOccupation',
            'loyersImpayes',
            'revenusMensuels12Mois'
        ));
    }

    /**
     * Onglet Ventes - Dashboard Propriétaire
     */
    public function espaceProprietaireVentes()
    {
        $user = Auth::user();
        
        // Récupérer toutes les ventes (finalisées et en cours)
        $biensVentes = \App\Models\Vente::with(['annonce.typeBien', 'client', 'paiements'])
            ->whereHas('annonce', function($q) use ($user) {
                $q->where('proprietaire_id', $user->id);
            })
            ->get();
        
        if ($biensVentes->count() == 0) {
            return redirect()->route('client.proprietaire')->with('info', 'Vous n\'avez pas encore de vente.');
        }
        
        // Séparer ventes finalisées et en cours
        $ventesFinalisees = $biensVentes->where('statut', 'terminee');
        $ventesEnCours = $biensVentes->whereNotIn('statut', ['terminee', 'annule']);
        
        // KPI Ventes
        $ventesFinalisesAnnee = $ventesFinalisees->filter(function($vente) {
            return $vente->date_finalisation && $vente->date_finalisation->year == now()->year;
        })->count();
        
        $montantTotalEncaisse = $ventesFinalisees->sum(function($vente) {
            return $vente->montantTotalPaye();
        });
        
        $montantTotalCommissions = $ventesFinalisees->sum(function($vente) {
            return $vente->calculerCommission();
        });
        
        $montantTotalNet = $montantTotalEncaisse - $montantTotalCommissions;
        
        $ventesEnCoursCount = $ventesEnCours->count();
        
        // Détails ventes finalisées
        $biensVendusDetails = [];
        foreach ($ventesFinalisees as $vente) {
            $commissionMontant = $vente->calculerCommission();
            $commissionPct = $vente->prix_vente > 0 ? ($commissionMontant / $vente->prix_vente) * 100 : 0;
            
            $biensVendusDetails[] = [
                'bien' => $vente->annonce,
                'vente' => $vente,
                'prix_vente' => $vente->prix_vente,
                'date_finalisation' => $vente->date_finalisation,
                'commission_agence' => $commissionMontant,
                'commission_pct' => $commissionPct,
                'revenu_net' => $vente->prix_vente - $commissionMontant,
                'acheteur' => $vente->client,
            ];
        }
        
        // Détails ventes en cours
        $ventesEnCoursDetails = [];
        foreach ($ventesEnCours as $vente) {
            $ventesEnCoursDetails[] = [
                'bien' => $vente->annonce,
                'vente' => $vente,
                'prix_demande' => $vente->prix_vente,
                'statut' => $vente->statut,
                'progression' => $vente->getProgressionAttribute(),
                'date_derniere_action' => $vente->updated_at,
                'client_interesse' => $vente->client,
            ];
        }
        
        return view('frontend.pages.client.espaces.proprietaire.ventes', compact(
            'ventesFinalisesAnnee',
            'montantTotalEncaisse',
            'montantTotalCommissions',
            'montantTotalNet',
            'ventesEnCoursCount',
            'biensVendusDetails',
            'ventesEnCoursDetails'
        ));
    }

    /**
     * Onglet Historique - Dashboard Propriétaire
     */
    public function espaceProprietaireHistorique()
    {
        $user = Auth::user();
        
        // Récupérer tous les biens
        $biens = \App\Models\Annonce::where('proprietaire_id', $user->id)->get();
        
        if ($biens->count() == 0) {
            return redirect()->route('client.proprietaire')->with('info', 'Vous n\'avez pas encore de bien confié à l\'agence.');
        }
        
        // Timeline complète de tous les événements
        $timeline = collect();
        
        foreach ($biens as $bien) {
            // Événements de locations
            $locations = \App\Models\Location::where('annonce_id', $bien->id)->get();
            foreach ($locations as $location) {
                $timeline->push([
                    'type' => 'location',
                    'sous_type' => $location->statut == 'actif' ? 'location_active' : 'location_terminee',
                    'date' => $location->created_at,
                    'bien' => $bien,
                    'titre' => $location->statut == 'actif' ? 'Location en cours' : 'Location terminée',
                    'description' => 'Locataire: ' . $location->locataire->name,
                    'montant' => $location->loyer_mensuel * $location->duree_mois,
                    'commission' => ($location->loyer_mensuel * $location->duree_mois * ($location->commission_agence ?? 0)) / 100,
                    'data' => $location,
                ]);
            }
            
            // Événements de ventes
            $ventes = \App\Models\Vente::where('annonce_id', $bien->id)->get();
            foreach ($ventes as $vente) {
                $timeline->push([
                    'type' => 'vente',
                    'sous_type' => $vente->statut,
                    'date' => $vente->date_finalisation ?? $vente->created_at,
                    'bien' => $bien,
                    'titre' => $vente->statut == 'terminee' ? 'Vente finalisée' : 'Vente ' . str_replace('_', ' ', $vente->statut),
                    'description' => 'Acheteur: ' . $vente->client->name,
                    'montant' => $vente->prix_vente,
                    'commission' => $vente->calculerCommission(),
                    'data' => $vente,
                ]);
            }
        }
        
        // Trier par date décroissante
        $timeline = $timeline->sortByDesc('date')->values();
        
        // Statistiques globales
        $totalRevenusLocations = \App\Models\Paiement::whereHas('location', function($q) use ($user) {
            $q->whereHas('annonce', function($q2) use ($user) {
                $q2->where('proprietaire_id', $user->id);
            });
        })->where('statut', 'valide')->sum('montant');
        
        $totalCommissionsLocations = \App\Models\Paiement::whereHas('location', function($q) use ($user) {
            $q->whereHas('annonce', function($q2) use ($user) {
                $q2->where('proprietaire_id', $user->id);
            });
        })->where('statut', 'valide')->sum('commission_agence');
        
        $ventesFinalisees = \App\Models\Vente::whereHas('annonce', function($q) use ($user) {
            $q->where('proprietaire_id', $user->id);
        })->where('statut', 'terminee')->get();
        
        $totalRevenusVentes = $ventesFinalisees->sum('prix_vente');
        $totalCommissionsVentes = $ventesFinalisees->sum(function($v) {
            return $v->calculerCommission();
        });
        
        return view('frontend.pages.client.espaces.proprietaire.historique', compact(
            'timeline',
            'totalRevenusLocations',
            'totalCommissionsLocations',
            'totalRevenusVentes',
            'totalCommissionsVentes'
        ));
    }

    /**
     * Espace Locataire
     */
    public function espaceLocataire()
    {
        $user = Auth::user();
        
        // Récupérer les locations du locataire
        $locations = \App\Models\Location::with('annonce', 'paiements', 'echeances')->where('locataire_id', $user->id)->get();
        $locationActive = $locations->where('statut', 'actif')->first();
        
        if (!$locationActive && $locations->count() == 0) {
            return redirect()->route('client.dashboard')->with('info', 'Vous n\'avez pas encore de location active.');
        }
        
        $prochaineEcheanceLocataire = null;
        $avanceRestante = 0;
        $impayesLocataire = 0;
        $cautionStatut = null;
        $historiquePaiements = collect();
        $echeancesLocataire = collect();
        
        if ($locationActive) {
            // Prochaine échéance
            $prochaineEcheanceLocataire = $locationActive->echeances()
                ->where('statut', 'a_echeance')
                ->where('date_echeance', '>=', now())
                ->orderBy('date_echeance')
                ->first();
            
            // Avance restante
            if ($locationActive->montant_avance) {
                $totalConsomme = $locationActive->echeances()
                    ->where('statut', 'paye')
                    ->sum('montant_du');
                $avanceRestante = max(0, $locationActive->montant_avance - $totalConsomme);
            }
            
            // Impayés
            $impayesLocataire = $locationActive->echeances()
                ->where('statut', 'impaye')
                ->where('date_echeance', '<', now())
                ->get()
                ->sum(function($echeance) {
                    return $echeance->montant_du - $echeance->montant_paye;
                });
            
            // Statut caution
            $cautionStatut = $locationActive->caution > 0 ? 'En dépôt' : 'Non requis';
            
            // Historique paiements
            $historiquePaiements = $locationActive->paiements()
                ->where('statut', 'valide')
                ->orderBy('date_paiement', 'desc')
                ->take(10)
                ->get();
            
            // Échéances
            $echeancesLocataire = $locationActive->echeances()
                ->orderBy('date_echeance', 'desc')
                ->get();
        }
        
        return view('frontend.pages.client.espaces.locataire', compact(
            'locationActive',
            'prochaineEcheanceLocataire',
            'avanceRestante',
            'impayesLocataire',
            'cautionStatut',
            'historiquePaiements',
            'echeancesLocataire',
            'locations'
        ));
    }

    /**
     * Espace Acheteur
     */
    public function espaceAcheteur()
    {
        $user = Auth::user();
        
        // Récupérer les ventes de l'acheteur
        $ventes = \App\Models\Vente::with('annonce', 'paiements')->where('client_id', $user->id)->get();
        $venteActive = $ventes->where('statut', 'paiement_valide')->first();
        
        if (!$venteActive && $ventes->count() == 0) {
            return redirect()->route('client.dashboard')->with('info', 'Vous n\'avez pas encore d\'achat finalisé.');
        }
        
        $montantPaye = 0;
        $montantRestant = 0;
        $documentsVente = [];
        $remiseCles = false;
        $historiquePaiements = collect();
        
        if ($venteActive) {
            // Calcul des paiements
            $montantPaye = $venteActive->paiements()
                ->where('statut', 'valide')
                ->sum('montant');
            
            $montantRestant = max(0, $venteActive->montant_total - $montantPaye);
            
            // Documents (à adapter selon votre système)
            if ($venteActive->annonce && $venteActive->annonce->hasMedia('documents')) {
                $documentsVente = $venteActive->annonce->getMedia('documents');
            }
            
            // Remise des clés (logique à adapter)
            $remiseCles = $montantRestant == 0;
            
            // Historique paiements
            $historiquePaiements = $venteActive->paiements()
                ->where('statut', 'valide')
                ->orderBy('date_paiement', 'desc')
                ->get();
        }
        
        return view('frontend.pages.client.espaces.acheteur', compact(
            'venteActive',
            'montantPaye',
            'montantRestant',
            'documentsVente',
            'remiseCles',
            'historiquePaiements',
            'ventes'
        ));
    }
}

