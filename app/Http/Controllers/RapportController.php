<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Location;
use App\Models\Vente;
use App\Models\User;
use App\Models\Charge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Services\RapportProprietaireService;
use App\Services\RapportAgenceService;

class RapportController extends Controller
{
    /**
     * Rapport des commissions
     */
    public function commissions(Request $request)
    {
        // Récupérer les filtres
        $dateDebut = $request->input('date_debut', now()->startOfMonth()->format('Y-m-d'));
        $dateFin = $request->input('date_fin', now()->endOfMonth()->format('Y-m-d'));
        $typeTransaction = $request->input('type_transaction', 'tous'); // tous, location, vente
        $locataire = $request->input('locataire');
        
        // Commissions des locations (paiements de loyer uniquement)
        $commissionsLocations = collect();
        if (in_array($typeTransaction, ['tous', 'location'])) {
            $commissionsLocations = Paiement::with(['payable.locataire', 'payable.annonce'])
                ->where('payable_type', Location::class)
                ->where('type_paiement', 'loyer') // Seulement les loyers, pas la caution ni l'avance
                ->where('statut', 'paye')
                ->whereBetween('date_paiement', [$dateDebut, $dateFin])
                ->when($locataire, function ($query, $locataire) {
                    $query->whereHas('payable', function ($q) use ($locataire) {
                        $q->where('locataire_id', $locataire);
                    });
                })
                ->get()
                ->filter(function ($paiement) {
                    // Filtrer uniquement les paiements dont la location a une commission
                    return $paiement->payable && $paiement->payable->commission_agence;
                })
                ->map(function ($paiement) {
                    $montant = floatval($paiement->montant);
                    
                    // Récupérer la commission depuis la location, pas depuis le paiement
                    $location = $paiement->payable;
                    $commissionAgence = floatval($location->commission_agence ?? 0);
                    $typeCommission = $location->type_commission ?? 'montant';
                    
                    // Calculer la commission sur ce paiement de loyer
                    $commission = $typeCommission == 'pourcentage'
                        ? ($montant * $commissionAgence / 100)
                        : $commissionAgence;
                    
                    return [
                        'id' => $paiement->id,
                        'location_id' => $location->id,
                        'date' => $paiement->date_paiement,
                        'type' => 'Location',
                        'reference' => $location->annonce->reference ?? 'N/A',
                        'bien' => $location->annonce->titre ?? 'N/A',
                        'client' => $location->locataire->name ?? 'N/A',
                        'montant_transaction' => $montant,
                        'commission_config' => $commissionAgence . ($typeCommission == 'pourcentage' ? '%' : ' FCFA'),
                        'commission_montant' => $commission,
                        'methode_paiement' => $paiement->methode_paiement,
                        'reference_paiement' => $paiement->reference,
                    ];
                });
        }
        
        // Commissions des ventes (récupérer depuis le modèle Vente)
        $commissionsVentes = collect();
        if (in_array($typeTransaction, ['tous', 'vente'])) {
            // Récupérer les ventes avec commission et qui ont des paiements dans la période
            $ventes = Vente::with(['client', 'annonce', 'paiements'])
                ->whereNotNull('commission_agence')
                ->where('commission_agence', '>', 0)
                ->whereHas('paiements', function ($query) use ($dateDebut, $dateFin) {
                    $query->where('statut', 'paye')
                        ->whereBetween('date_paiement', [$dateDebut, $dateFin]);
                })
                ->get();
            
            $commissionsVentes = $ventes->map(function ($vente) {
                // Utiliser la date du dernier paiement comme date de référence
                $dernierPaiement = $vente->paiements()
                    ->where('statut', 'paye')
                    ->orderBy('date_paiement', 'desc')
                    ->first();
                
                if (!$dernierPaiement) {
                    return null;
                }
                
                // Calculer la commission selon le type
                $commissionAgence = floatval($vente->commission_agence);
                if ($vente->type_commission === 'pourcentage') {
                    $commission = ($vente->prix_vente * $commissionAgence) / 100;
                } else {
                    $commission = $commissionAgence;
                }
                
                return [
                    'id' => $vente->id,
                    'vente_id' => $vente->id,
                    'date' => $dernierPaiement->date_paiement,
                    'type' => 'Vente',
                    'reference' => $vente->annonce->reference ?? 'N/A',
                    'bien' => $vente->annonce->titre ?? 'N/A',
                    'client' => $vente->client->name ?? 'N/A',
                    'montant_transaction' => floatval($vente->prix_vente),
                    'commission_config' => $commissionAgence . ($vente->type_commission == 'pourcentage' ? '%' : ' FCFA'),
                    'commission_montant' => $commission,
                    'methode_paiement' => $dernierPaiement->methode_paiement,
                    'reference_paiement' => $dernierPaiement->reference,
                ];
            })->filter(); // Filtrer les valeurs null
        }
        
        // Statistiques séparées pour chaque type
        $totalCommissionsLocations = $commissionsLocations->sum('commission_montant');
        $totalTransactionsLocations = $commissionsLocations->sum('montant_transaction');
        $nombreLocations = $commissionsLocations->count();
        
        $totalCommissionsVentes = $commissionsVentes->sum('commission_montant');
        $totalTransactionsVentes = $commissionsVentes->sum('montant_transaction');
        $nombreVentes = $commissionsVentes->count();
        
        // Statistiques globales
        $totalCommissions = $totalCommissionsLocations + $totalCommissionsVentes;
        $totalTransactions = $totalTransactionsLocations + $totalTransactionsVentes;
        $nombreTransactions = $nombreLocations + $nombreVentes;
        $commissionMoyenne = $nombreTransactions > 0 ? $totalCommissions / $nombreTransactions : 0;
        
        // Trier chaque collection par date
        $commissionsLocations = $commissionsLocations->sortByDesc('date')->values();
        $commissionsVentes = $commissionsVentes->sortByDesc('date')->values();
        
        // Liste des locataires pour le filtre
        $locataires = \App\Models\User::whereHas('locations')->get();
        
        return view('backend.pages.rapports.commissions', compact(
            'commissionsLocations',
            'commissionsVentes',
            'totalCommissionsLocations',
            'totalTransactionsLocations',
            'nombreLocations',
            'totalCommissionsVentes',
            'totalTransactionsVentes',
            'nombreVentes',
            'totalCommissions',
            'totalTransactions',
            'nombreTransactions',
            'commissionMoyenne',
            'dateDebut',
            'dateFin',
            'typeTransaction',
            'locataires',
            'locataire'
        ));
    }
    
    /**
     * Statistiques générales
     */
    public function statistiques(Request $request)
    {
        $dateDebut = $request->input('date_debut', now()->startOfYear()->format('Y-m-d'));
        $dateFin = $request->input('date_fin', now()->endOfYear()->format('Y-m-d'));
        
        // Statistiques Locations
        $locationsActives = Location::where('statut', 'actif')->count();
        $totalLoyers = Paiement::where('payable_type', Location::class)
            ->where('type_paiement', 'loyer')
            ->where('statut', 'paye')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->sum('montant');
        
        $commissionsLoyers = Paiement::where('payable_type', Location::class)
            ->where('type_paiement', 'loyer')
            ->where('statut', 'paye')
            ->whereNotNull('commission_agence')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->get()
            ->sum(function ($paiement) {
                $montant = floatval($paiement->montant);
                $commission = floatval($paiement->commission_agence);
                return $paiement->type_commission == 'pourcentage'
                    ? ($montant * $commission / 100)
                    : $commission;
            });
        
        // Statistiques Ventes
        $ventesCompletes = Vente::where('statut', 'terminee')->count();
        $totalVentes = Paiement::where('payable_type', Vente::class)
            ->where('type_paiement', 'prix_achat')
            ->where('statut', 'paye')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->sum('montant');
        
        $commissionsVentes = Paiement::where('payable_type', Vente::class)
            ->where('type_paiement', 'prix_achat')
            ->where('statut', 'paye')
            ->whereNotNull('commission_agence')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->get()
            ->sum(function ($paiement) {
                $montant = floatval($paiement->montant);
                $commission = floatval($paiement->commission_agence);
                return $paiement->type_commission == 'pourcentage'
                    ? ($montant * $commission / 100)
                    : $commission;
            });
        
        // Évolution mensuelle
        $evolutionMensuelle = Paiement::select(
                DB::raw('DATE_FORMAT(date_paiement, "%Y-%m") as mois'),
                DB::raw('SUM(montant) as total'),
                DB::raw('COUNT(*) as nombre')
            )
            ->where('statut', 'paye')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();
        
        // Top 5 biens les plus rentables
        $topBiens = Paiement::with(['payable.annonce'])
            ->where('statut', 'paye')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->get()
            ->groupBy(function ($paiement) {
                return ($paiement->payable_type ?? '') . '-' . ($paiement->payable_id ?? 'autre');
            })
            ->map(function ($group) {
                $commission = $group->sum(function ($paiement) {
                    if (!$paiement->commission_agence) return 0;
                    $montant = floatval($paiement->montant);
                    $commissionAgence = floatval($paiement->commission_agence);
                    return $paiement->type_commission == 'pourcentage'
                        ? ($montant * $commissionAgence / 100)
                        : $commissionAgence;
                });
                
                $first = $group->first();
                $bien = $first->payable?->annonce ?? null;
                
                return [
                    'bien' => $bien?->titre ?? 'N/A',
                    'reference' => $bien?->reference ?? 'N/A',
                    'total' => $group->sum(function ($paiement) {
                        return floatval($paiement->montant);
                    }),
                    'commission' => $commission,
                    'nombre_transactions' => $group->count(),
                ];
            })
            ->sortByDesc('commission')
            ->take(5);
        
        return view('backend.pages.rapports.statistiques', compact(
            'locationsActives',
            'totalLoyers',
            'commissionsLoyers',
            'ventesCompletes',
            'totalVentes',
            'commissionsVentes',
            'evolutionMensuelle',
            'topBiens',
            'dateDebut',
            'dateFin'
        ));
    }

    /**
     * Rapport financier propriétaire - Affiche les revenus d'un propriétaire
     */
    public function rapportProprietaire(Request $request)
    {
        // Seul les administrateurs peuvent voir le rapport propriétaire
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $request->validate([
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
            'proprietaire_id' => 'nullable|exists:users,id',
        ]);

        // Déterminer le propriétaire
        $proprietaireId = $request->input('proprietaire_id');

        // Si pas de propriétaire spécifié, afficher liste de sélection
        if (!$proprietaireId) {
            $proprietaires = User::where('role', 'proprietaire')->get();
            return view('backend.pages.rapports.proprietaire-select', compact('proprietaires'));
        }

        $proprietaire = User::findOrFail($proprietaireId);

        // Filtres de dates
        $dateDebut = $request->input('date_debut') 
            ? Carbon::createFromFormat('Y-m-d', $request->input('date_debut'))
            : now()->startOfYear();
        
        $dateFin = $request->input('date_fin')
            ? Carbon::createFromFormat('Y-m-d', $request->input('date_fin'))
            : now()->endOfYear();

        // Générer le rapport
        $service = new RapportProprietaireService();
        $rapport = $service->genererRapport($proprietaire, $dateDebut, $dateFin);

        // Liste des propriétaires pour le filtre
        $proprietaires = User::where('role', 'proprietaire')->get();

        return view('backend.pages.rapports.proprietaire', compact(
            'rapport',
            'proprietaire',
            'dateDebut',
            'dateFin',
            'proprietaires'
        ));
    }

    /**
     * Rapport financier agence - Affiche les revenus de l'agence
     */
    public function rapportAgence(Request $request)
    {
        // Seul les administrateurs peuvent voir le rapport agence
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $request->validate([
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
        ]);

        // Filtres de dates
        $dateDebut = $request->input('date_debut')
            ? Carbon::createFromFormat('Y-m-d', $request->input('date_debut'))
            : now()->startOfYear();
        
        $dateFin = $request->input('date_fin')
            ? Carbon::createFromFormat('Y-m-d', $request->input('date_fin'))
            : now()->endOfYear();

        // Générer le rapport
        $service = new RapportAgenceService();
        $rapport = $service->genererRapport($dateDebut, $dateFin);

        return view('backend.pages.rapports.agence', compact(
            'rapport',
            'dateDebut',
            'dateFin'
        ));
    }

    /**
     * Gestion des charges - Liste des charges
     */
    public function chargesIndex(Request $request)
    {
        // Seul les administrateurs peuvent voir les charges
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $query = Charge::with('annonce');

        // Filtre par bien
        if ($request->filled('annonce_id')) {
            $query->where('annonce_id', $request->input('annonce_id'));
        }

        // Filtre par type
        if ($request->filled('type_charge')) {
            $query->where('type_charge', $request->input('type_charge'));
        }

        // Filtre par date
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_charge', [
                $request->input('date_debut'),
                $request->input('date_fin')
            ]);
        }

        $charges = $query->orderBy('date_charge', 'desc')->paginate(20);

        // Liste des biens
        $biens = \App\Models\Annonce::all();

        return view('backend.pages.rapports.charges.index', compact(
            'charges',
            'biens'
        ));
    }

    /**
     * Gestion des charges - Créer une charge
     */
    public function chargesCreate(Request $request)
    {
        // Seul les administrateurs peuvent créer des charges
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $biens = \App\Models\Annonce::all();

        return view('backend.pages.rapports.charges.create', compact('biens'));
    }

    /**
     * Gestion des charges - Enregistrer une charge
     */
    public function chargesStore(Request $request)
    {
        // Seul les administrateurs peuvent créer des charges
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $request->validate([
            'annonce_id' => 'required|exists:annonces,id',
            'type_charge' => 'required|in:maintenance,reparation,taxe,autre',
            'montant' => 'required|numeric|min:0',
            'date_charge' => 'required|date',
            'description' => 'nullable|string',
            'reference' => 'nullable|string',
        ]);

        Charge::create($request->all());

        return redirect()->route('charges.index')
            ->with('success', 'Charge enregistrée avec succès');
    }

    /**
     * Gestion des charges - Éditer une charge
     */
    public function chargesEdit(Charge $charge)
    {
        // Seul les administrateurs peuvent éditer les charges
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $biens = \App\Models\Annonce::all();

        return view('backend.pages.rapports.charges.edit', compact('charge', 'biens'));
    }

    /**
     * Gestion des charges - Mettre à jour une charge
     */
    public function chargesUpdate(Request $request, Charge $charge)
    {
        // Seul les administrateurs peuvent mettre à jour les charges
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $request->validate([
            'annonce_id' => 'required|exists:annonces,id',
            'type_charge' => 'required|in:maintenance,reparation,taxe,autre',
            'montant' => 'required|numeric|min:0',
            'date_charge' => 'required|date',
            'description' => 'nullable|string',
            'reference' => 'nullable|string',
        ]);

        $charge->update($request->all());

        return redirect()->route('charges.index')
            ->with('success', 'Charge mise à jour avec succès');
    }

    /**
     * Gestion des charges - Supprimer une charge
     */
    public function chargesDestroy(Charge $charge)
    {
        // Seul les administrateurs peuvent supprimer les charges
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $charge->delete();

        return redirect()->route('charges.index')
            ->with('success', 'Charge supprimée avec succès');
    }
}
