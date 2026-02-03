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
                ->whereNotNull('commission_agence')
                ->whereBetween('date_paiement', [$dateDebut, $dateFin])
                ->when($locataire, function ($query, $locataire) {
                    $query->whereHas('payable', function ($q) use ($locataire) {
                        $q->where('locataire_id', $locataire);
                    });
                })
                ->get()
                ->map(function ($paiement) {
                    $montant = floatval($paiement->montant);
                    $commissionAgence = floatval($paiement->commission_agence);
                    $commission = $paiement->type_commission == 'pourcentage'
                        ? ($montant * $commissionAgence / 100)
                        : $commissionAgence;
                    
                    return [
                        'id' => $paiement->id,
                        'date' => $paiement->date_paiement,
                        'type' => 'Location',
                        'reference' => $paiement->payable->annonce->reference ?? 'N/A',
                        'bien' => $paiement->payable->annonce->titre ?? 'N/A',
                        'client' => $paiement->payable->locataire->name ?? 'N/A',
                        'montant_transaction' => $montant,
                        'commission_config' => $commissionAgence . ($paiement->type_commission == 'pourcentage' ? '%' : ' FCFA'),
                        'commission_montant' => $commission,
                        'methode_paiement' => $paiement->methode_paiement,
                        'reference_paiement' => $paiement->reference,
                    ];
                });
        }
        
        // Commissions des ventes (paiements du prix d'achat uniquement)
        $commissionsVentes = collect();
        if (in_array($typeTransaction, ['tous', 'vente'])) {
            $commissionsVentes = Paiement::with(['payable.acheteur', 'payable.annonce'])
                ->where('payable_type', Vente::class)
                ->where('type_paiement', 'prix_achat')
                ->where('statut', 'paye')
                ->whereNotNull('commission_agence')
                ->whereBetween('date_paiement', [$dateDebut, $dateFin])
                ->get()
                ->map(function ($paiement) {
                    $montant = floatval($paiement->montant);
                    $commissionAgence = floatval($paiement->commission_agence);
                    $commission = $paiement->type_commission == 'pourcentage'
                        ? ($montant * $commissionAgence / 100)
                        : $commissionAgence;
                    
                    return [
                        'id' => $paiement->id,
                        'date' => $paiement->date_paiement,
                        'type' => 'Vente',
                        'reference' => $paiement->payable->annonce->reference ?? 'N/A',
                        'bien' => $paiement->payable->annonce->titre ?? 'N/A',
                        'client' => $paiement->payable->acheteur->name ?? 'N/A',
                        'montant_transaction' => $montant,
                        'commission_config' => $commissionAgence . ($paiement->type_commission == 'pourcentage' ? '%' : ' FCFA'),
                        'commission_montant' => $commission,
                        'methode_paiement' => $paiement->methode_paiement,
                        'reference_paiement' => $paiement->reference,
                    ];
                });
        }
        
        // Fusionner et trier
        $commissions = $commissionsLocations->concat($commissionsVentes)
            ->sortByDesc('date')
            ->values();
        
        // Statistiques
        $totalCommissions = $commissions->sum('commission_montant');
        $totalTransactions = $commissions->sum('montant_transaction');
        $nombreTransactions = $commissions->count();
        $commissionMoyenne = $nombreTransactions > 0 ? $totalCommissions / $nombreTransactions : 0;
        
        // Répartition par type
        $parType = $commissions->groupBy('type')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('commission_montant'),
            ];
        });
        
        // Répartition par mois (pour le graphique)
        $parMois = $commissions->groupBy(function ($item) {
            return Carbon::parse($item['date'])->format('Y-m');
        })->map(function ($group) {
            return $group->sum('commission_montant');
        })->sortKeys();
        
        // Liste des locataires pour le filtre
        $locataires = \App\Models\User::whereHas('locations')->get();
        
        return view('backend.pages.rapports.commissions', compact(
            'commissions',
            'totalCommissions',
            'totalTransactions',
            'nombreTransactions',
            'commissionMoyenne',
            'parType',
            'parMois',
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
