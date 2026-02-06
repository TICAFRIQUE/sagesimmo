<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Location;
use App\Models\Vente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
}
