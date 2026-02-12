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
        $demandesEnCours = $ventes->whereNotIn('statut', ['annulee', 'terminee'])->count() + 
                          $locations->whereNotIn('statut', ['annule', 'actif', 'termine', 'resilie'])->count();
        $demandesFinalisees = $ventes->where('statut', 'terminee')->count() + 
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
        $biensAchetes = $ventes->where('statut', 'terminee');
        
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
        $vente = \App\Models\Vente::with('annonce')
            ->where('client_id', $user->id)
            ->where('id', $id)
            ->first();
        
        if ($vente) {
            // Rediriger vers le workflow de la vente
            return redirect()->route('client.acheteur.workflow', $vente->id);
        }
        
        // Sinon chercher dans les locations
        $demande = \App\Models\Location::with('annonce', 'paiements', 'echeances')
            ->where('locataire_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();
        
        $demande->type_transaction = 'location';
        
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
            $vente->update(['statut' => 'annulee']);
            return redirect()->route('client.demandes')->with('success', 'Demande annulée avec succès.');
        }
        
        // Sinon chercher dans les locations avec statut demande_client
        $location = \App\Models\Location::where('locataire_id', $user->id)
            ->where('id', $id)
            ->where('statut', 'demande_client')
            ->first();
        
        if ($location) {
            $location->update(['statut', 'annule']);
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
                // Utiliser les PAIEMENTS pour les revenus encaissés
                $paiementsLocation = $location->paiements()
                    ->where('type_paiement', 'loyer')
                    ->where('statut', 'paye')
                    ->get();
                
                foreach ($paiementsLocation as $paiement) {
                    $revenusEncaisses += $paiement->montant;
                    
                    $mois = \Carbon\Carbon::parse($paiement->date_paiement)->format('Y-m');
                    if (!isset($revenusMensuels[$mois])) {
                        $revenusMensuels[$mois] = ['mois' => $mois, 'brut' => 0, 'commission' => 0, 'net' => 0];
                    }
                    $revenusMensuels[$mois]['brut'] += $paiement->montant;
                    
                    // Calculer la commission selon le type
                    $commissionPct = $location->commission_agence ?? 0;
                    $typeCommission = $location->type_commission ?? 'pourcentage';
                    
                    $commission = 0;
                    if ($typeCommission == 'pourcentage') {
                        $commission = ($paiement->montant * $commissionPct) / 100;
                    } else {
                        $commission = $commissionPct;
                    }
                    
                    $commissionAgenceTotal += $commission;
                    $revenusMensuels[$mois]['commission'] += $commission;
                }
                
                // Calculer les impayés à partir des échéances
                foreach ($location->echeances as $echeance) {
                    if ($echeance->statut == 'impaye') {
                        $loyersImpayes += ($echeance->montant_du - $echeance->montant_paye);
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
                    
                    // Utiliser les PAIEMENTS pour les revenus encaissés
                    $paiementsLocation = $location->paiements()
                        ->where('type_paiement', 'loyer')
                        ->where('statut', 'paye')
                        ->get();
                    
                    foreach ($paiementsLocation as $paiement) {
                        $detailBien['loyers_payes'] += $paiement->montant;
                        
                        // Calculer la commission selon le type
                        $commissionPct = $location->commission_agence ?? 0;
                        $typeCommission = $location->type_commission ?? 'pourcentage';
                        
                        if ($typeCommission == 'pourcentage') {
                            $detailBien['commission_agence'] += ($paiement->montant * $commissionPct) / 100;
                        } else {
                            $detailBien['commission_agence'] += $commissionPct;
                        }
                    }
                    
                    // Calculer les impayés à partir des échéances
                    foreach ($location->echeances as $echeance) {
                        if ($echeance->statut == 'impaye') {
                            $detailBien['loyers_impayes'] += ($echeance->montant_du - $echeance->montant_paye);
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
        
        // Gestion du filtre période
        $moisFiltre = request('mois');
        $anneeFiltre = request('annee');
        
        // Définir les dates de début et fin selon le filtre
        $dateDebut = null;
        $dateFin = null;
        $periode = 'all';
        
        if ($moisFiltre && $anneeFiltre) {
            // Filtre par mois et année spécifiques
            $dateDebut = \Carbon\Carbon::create($anneeFiltre, $moisFiltre, 1)->startOfMonth();
            $dateFin = \Carbon\Carbon::create($anneeFiltre, $moisFiltre, 1)->endOfMonth();
            $periode = 'custom';
        } elseif ($anneeFiltre) {
            // Filtre par année uniquement
            $dateDebut = \Carbon\Carbon::create($anneeFiltre, 1, 1)->startOfYear();
            $dateFin = \Carbon\Carbon::create($anneeFiltre, 12, 31)->endOfYear();
            $periode = 'custom';
        }
        
        // KPI Locations selon le filtre
        $loyersEncaisses = 0;
        $loyersImpayes = 0;
        $commissionAgence = 0;
        $revenusNets = 0;
        
        // Prochaine échéance (globale, non filtrée)
        $prochaineEcheance = \App\Models\Echeance::whereHas('location', function($q) use ($user) {
            $q->whereHas('annonce', function($q2) use ($user) {
                $q2->where('proprietaire_id', $user->id);
            })->where('statut', 'actif');
        })
        ->where('statut', '!=', 'paye')
        ->where('date_echeance', '>=', now())
        ->orderBy('date_echeance', 'asc')
        ->first();
        
        // Détails par bien
        $biensLocations = [];
        foreach ($biensAvecLocations as $bien) {
            $locationActive = $bien->locations->where('statut', 'actif')->first();
            
            if (!$locationActive) continue;
            
            $loyerMensuel = $locationActive->loyer_mensuel;
            $commissionPct = $locationActive->commission_agence ?? 0;
            $typeCommission = $locationActive->type_commission ?? 'pourcentage';
            
            // Calculer les montants encaissés et commission selon le filtre
            // Utiliser les PAIEMENTS (pas les échéances) pour avoir les montants réellement perçus
            $paiementsQuery = $locationActive->paiements()
                ->where('type_paiement', 'loyer') // Uniquement les loyers (pas caution/avance)
                ->where('statut', 'paye');
            
            if ($dateDebut && $dateFin && $periode != 'all') {
                $paiementsQuery->whereBetween('date_paiement', [$dateDebut, $dateFin]);
            }
            
            $paiements = $paiementsQuery->get();
            
            // Calculer encaissé et commission à partir des paiements réels
            $encaisse = $paiements->sum('montant');
            
            // Calculer la commission réelle sur les paiements
            $commissionReelle = 0;
            foreach ($paiements as $paiement) {
                if ($typeCommission == 'pourcentage') {
                    $commissionReelle += ($paiement->montant * $commissionPct) / 100;
                } else {
                    $commissionReelle += $commissionPct;
                }
            }
            
            $net = $encaisse - $commissionReelle;
            
            // Calculer les retards (échéances impayées dans la période)
            $echeancesQuery = $locationActive->echeances()
                ->where('statut', 'impaye')
                ->where('date_echeance', '<', now());
            
            if ($dateDebut && $dateFin && $periode != 'all') {
                $echeancesQuery->whereBetween('date_echeance', [$dateDebut, $dateFin]);
            }
            
            $echeancesImpayes = $echeancesQuery->get();
            $retard = $echeancesImpayes->sum(function($e) {
                return $e->montant_du - $e->montant_paye;
            });
            
            // Dernière échéance pour le statut
            $derniereEcheance = $locationActive->echeances()
                ->orderBy('date_echeance', 'desc')
                ->first();
            
            $statutPaiement = $derniereEcheance ? $derniereEcheance->statut : 'aucune';
            
            // Prochaine échéance (à venir ou en retard)
            $prochaineEcheance = $locationActive->echeances()
                ->where('statut', '!=', 'paye')
                ->where('date_echeance', '>=', now()->subMonths(3)) // Inclure les 3 derniers mois pour voir les retards
                ->orderBy('date_echeance', 'asc')
                ->first();
            
            // Si pas de prochaine échéance à venir, prendre la dernière
            if (!$prochaineEcheance) {
                $prochaineEcheance = $derniereEcheance;
            }
            
            // Accumuler les totaux
            $loyersEncaisses += $encaisse;
            $loyersImpayes += $retard;
            $commissionAgence += $commissionReelle;
            $revenusNets += $net;
            
            $biensLocations[] = [
                'bien' => $bien,
                'location_active' => $locationActive,
                'locataire' => $locationActive->locataire,
                'loyer_mensuel' => $loyerMensuel,
                'commission_pct' => $commissionPct,
                'encaisse' => $encaisse,
                'retard' => $retard,
                'net' => $net,
                'commission_reelle' => $commissionReelle,
                'derniere_echeance' => $derniereEcheance,
                'prochaine_echeance' => $prochaineEcheance,
                'statut_paiement' => $statutPaiement,
            ];
        }
        
        // Historique mensuel des 12 derniers mois
        $revenusMensuels12Mois = [];
        for ($i = 11; $i >= 0; $i--) {
            $mois = now()->subMonths($i);
            $moisKey = $mois->format('Y-m');
            
            // Récupérer les paiements de loyers du mois pour ce propriétaire
            $paiementsMois = \App\Models\Paiement::with('payable')
                ->where('payable_type', \App\Models\Location::class)
                ->where('type_paiement', 'loyer') // Uniquement les loyers
                ->whereHas('payable', function($q) use ($user) {
                    $q->whereHas('annonce', function($q2) use ($user) {
                        $q2->where('proprietaire_id', $user->id);
                    });
                })
                ->whereYear('date_paiement', $mois->year)
                ->whereMonth('date_paiement', $mois->month)
                ->where('statut', 'paye') // Statut correct : 'paye'
                ->get();
            
            $brut = $paiementsMois->sum('montant');
            
            // Calculer la commission réelle selon le type de commission de chaque location
            $commission = 0;
            foreach ($paiementsMois as $paiement) {
                $location = $paiement->payable;
                if ($location) {
                    $commissionPct = $location->commission_agence ?? 0;
                    $typeCommission = $location->type_commission ?? 'pourcentage';
                    
                    if ($typeCommission == 'pourcentage') {
                        $commission += ($paiement->montant * $commissionPct) / 100;
                    } else {
                        $commission += $commissionPct;
                    }
                }
            }
            
            $revenusMensuels12Mois[] = [
                'mois' => $moisKey,
                'brut' => $brut,
                'commission' => $commission,
                'net' => $brut - $commission,
            ];
        }
        
        return view('frontend.pages.client.espaces.proprietaire.locations', compact(
            'biensLocations',
            'loyersEncaisses',
            'loyersImpayes',
            'commissionAgence',
            'revenusNets',
            'prochaineEcheance',
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
        
        // Récupérer toutes les locations du locataire
        $locations = \App\Models\Location::with('annonce.typeBien', 'paiements', 'echeances')
            ->where('locataire_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        if ($locations->count() == 0) {
            return redirect()->route('client.dashboard')->with('info', 'Vous n\'avez pas encore de location.');
        }
        
        // Calculer les KPI
        $nombreBiensLoues = $locations->count();
        
        // Total dépensé (somme de tous les paiements validés)
        $totalDepense = $locations->sum(function($location) {
            return $location->paiements()->where('statut', 'valide')->sum('montant');
        });
        
        // Montant restant (somme des échéances non complètement payées)
        $montantRestantTotal = $locations->sum(function($location) {
            return $location->echeances
                ->sum(function($echeance) {
                    $reste = $echeance->montant_du - $echeance->montant_paye;
                    return $reste > 0 ? $reste : 0;
                });
        });
        
        return view('frontend.pages.client.espaces.locataire', compact(
            'locations',
            'nombreBiensLoues',
            'totalDepense',
            'montantRestantTotal'
        ));
    }

    /**
     * Voir le workflow d'une location
     */
    public function workflowLocation($id)
    {
        $user = Auth::user();
        
        $location = \App\Models\Location::with('annonce.typeBien', 'locataire', 'paiements', 'echeances')
            ->where('id', $id)
            ->where('locataire_id', $user->id)
            ->firstOrFail();
        
        return view('frontend.pages.client.espaces.workflow-location', compact('location'));
    }

    /**
     * Voir les échéances de paiement d'une location
     */
    public function echeancesLocation($id)
    {
        $user = Auth::user();
        
        $location = \App\Models\Location::with('annonce.typeBien', 'echeances', 'paiements')
            ->where('id', $id)
            ->where('locataire_id', $user->id)
            ->firstOrFail();
        
        return view('frontend.pages.client.espaces.echeances-location', compact('location'));
    }

    /**
     * Espace Acheteur
     */
    public function espaceAcheteur()
    {
        $user = Auth::user();
        
        // Récupérer toutes les ventes de l'acheteur
        $ventes = \App\Models\Vente::with('annonce.typeBien', 'paiements')
            ->where('client_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        if ($ventes->count() == 0) {
            return redirect()->route('client.dashboard')->with('info', 'Vous n\'avez pas encore effectué d\'achat.');
        }
        
        // Calculer les KPI
        $nombreBiensAchetes = $ventes->count();
        $totalDepense = $ventes->sum(function($vente) {
            return $vente->montantTotalPaye();
        });
        $montantRestantTotal = $ventes->sum(function($vente) {
            return $vente->resteAPayer();
        });
        
        return view('frontend.pages.client.espaces.acheteur', compact(
            'ventes',
            'nombreBiensAchetes',
            'totalDepense',
            'montantRestantTotal'
        ));
    }

    /**
     * Voir le workflow d'une vente
     */
    public function workflowVente($id)
    {
        $user = Auth::user();
        
        $vente = \App\Models\Vente::with('annonce.typeBien', 'client', 'paiements')
            ->where('id', $id)
            ->where('client_id', $user->id)
            ->firstOrFail();
        
        return view('frontend.pages.client.espaces.workflow-vente', compact('vente'));
    }

    /**
     * Voir la situation financière d'une vente
     */
    public function situationFinanciereVente($id)
    {
        $user = Auth::user();
        
        $vente = \App\Models\Vente::with('annonce.typeBien', 'paiements')
            ->where('id', $id)
            ->where('client_id', $user->id)
            ->firstOrFail();
        
        return view('frontend.pages.client.espaces.situation-financiere-vente', compact('vente'));
    }
}

