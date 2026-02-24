<?php

namespace App\Services;

use App\Models\Location;
use App\Models\Paiement;
use App\Models\User;
use App\Models\Vente;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RapportAgenceService
{
    /**
     * Générer le rapport financier complet de l'agence
     */
    public function genererRapport(Carbon $dateDebut, Carbon $dateFin): array
    {
        $loyers = $this->calculerEncaissementLoyers($dateDebut, $dateFin);
        $ventes = $this->calculerEncaissementVentes($dateDebut, $dateFin);

        $totalCommission = $loyers['total_commission'] + $ventes['total_commission'];
        $totalEncaisseLoyers = $loyers['total_encaisse'];
        $totalEncaisseVentes = $ventes['total_encaisse'];
        $totalBrutEncaisse = $totalEncaisseLoyers + $totalEncaisseVentes;

        return [
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'periode' => $dateDebut->format('d/m/Y') . ' au ' . $dateFin->format('d/m/Y'),
            
            // Encaissements
            'total_loyers_encaisses' => $totalEncaisseLoyers,
            'total_ventes_encaissees' => $totalEncaisseVentes,
            'total_encaisse' => $totalBrutEncaisse,
            'detail_loyers' => $loyers,
            'detail_ventes' => $ventes,
            
            // Commissions
            'total_commissions' => $totalCommission,
            'commissions_loyers' => $loyers['total_commission'],
            'commissions_ventes' => $ventes['total_commission'],
            
            // Détails par bien
            'detail_par_bien' => $this->detailParBien($dateDebut, $dateFin),
            
            // Détails par propriétaire
            'detail_par_proprietaire' => $this->detailParProprietaire($dateDebut, $dateFin),
            
            // Détails par type
            'detail_par_type' => [
                'location' => $loyers,
                'vente' => $ventes,
            ],
        ];
    }

    /**
     * Calculer l'encaissement des loyers
     */
    private function calculerEncaissementLoyers(Carbon $dateDebut, Carbon $dateFin): array
    {
        $paiements = Paiement::where('payable_type', Location::class)
            ->where('type_paiement', 'loyer')
            ->where('statut', 'paye')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->with(['payable' => function ($q) {
                $q->with('annonce', 'locataire');
            }])
            ->get();

        $totalEncaisse = $paiements->sum('montant');
        $totalCommission = $paiements->sum('commission_agence');

        return [
            'paiements' => $paiements,
            'nombre_paiements' => $paiements->count(),
            'total_encaisse' => $totalEncaisse,
            'total_commission' => $totalCommission,
            'detail_par_bien' => $this->groupPaymentsByBien($paiements),
            'detail_par_locataire' => $this->groupPaymentsByLocataire($paiements),
        ];
    }

    /**
     * Calculer l'encaissement des ventes
     */
    private function calculerEncaissementVentes(Carbon $dateDebut, Carbon $dateFin): array
    {
        $paiements = Paiement::where('payable_type', Vente::class)
            ->where('statut', 'paye')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->with(['payable' => function ($q) {
                $q->with('annonce', 'client');
            }])
            ->get();

        $totalEncaisse = $paiements->sum('montant');
        $totalCommission = $paiements->sum('commission_agence');

        return [
            'paiements' => $paiements,
            'nombre_paiements' => $paiements->count(),
            'total_encaisse' => $totalEncaisse,
            'total_commission' => $totalCommission,
            'detail_par_bien' => $this->groupPaymentsByBien($paiements),
            'detail_par_client' => $this->groupPaymentsByClient($paiements),
        ];
    }

    /**
     * Détail par bien
     */
    private function detailParBien(Carbon $dateDebut, Carbon $dateFin): Collection
    {
        // Récupérer tous les paiements entre les deux dates
        $paiements = Paiement::where('statut', 'paye')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->whereIn('payable_type', [Location::class, Vente::class])
            ->with('payable')
            ->get();

        // Grouper par bien - utiliser array plutôt que Collection
        $byBien = [];
        foreach ($paiements as $paiement) {
            $payable = $paiement->payable;
            if ($payable && $payable->annonce_id) {
                $annonceId = $payable->annonce_id;
                if (!isset($byBien[$annonceId])) {
                    $annonce = $payable->annonce;
                    $byBien[$annonceId] = [
                        'annonce' => $annonce,
                        'adresse' => $annonce->adresse ?? 'N/A',
                        'type_bien' => $annonce->typeBien?->nom ?? 'N/A',
                        'total_encaisse' => 0,
                        'total_commission' => 0,
                        'nombre_transactions' => 0,
                    ];
                }
                $byBien[$annonceId]['total_encaisse'] += $paiement->montant;
                $byBien[$annonceId]['total_commission'] += $paiement->commission_agence ?? 0;
                $byBien[$annonceId]['nombre_transactions']++;
            }
        }

        return collect($byBien)->values()->sortByDesc('total_commission');
    }

    /**
     * Détail par propriétaire
     */
    private function detailParProprietaire(Carbon $dateDebut, Carbon $dateFin): Collection
    {
        $paiements = Paiement::where('statut', 'paye')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->whereIn('payable_type', [Location::class, Vente::class])
            ->with('payable')
            ->get();

        // Utiliser array plutôt que Collection
        $byProprietaire = [];
        foreach ($paiements as $paiement) {
            $payable = $paiement->payable;
            if ($payable && $payable->annonce && $payable->annonce->proprietaire_id) {
                $proprietaireId = $payable->annonce->proprietaire_id;
                if (!isset($byProprietaire[$proprietaireId])) {
                    $proprietaire = $payable->annonce->proprietaire;
                    $byProprietaire[$proprietaireId] = [
                        'proprietaire' => $proprietaire,
                        'nom' => $proprietaire->username ?? 'N/A',
                        'total_encaisse' => 0,
                        'total_commission' => 0,
                        'nombre_biens' => 0,
                        'nombre_transactions' => 0,
                    ];
                }
                $byProprietaire[$proprietaireId]['total_encaisse'] += $paiement->montant;
                $byProprietaire[$proprietaireId]['total_commission'] += $paiement->commission_agence ?? 0;
                $byProprietaire[$proprietaireId]['nombre_transactions']++;
            }
        }

        return collect($byProprietaire)->values()->sortByDesc('total_commission');
    }

    /**
     * Grouper les paiements par bien
     */
    private function groupPaymentsByBien(Collection $paiements): Collection
    {
        return $paiements->groupBy(function ($paiement) {
            return $paiement->payable?->annonce_id;
        })->map(function ($group) {
            $firstPayable = $group->first()->payable;
            return [
                'annonce' => $firstPayable->annonce,
                'total_encaisse' => $group->sum('montant'),
                'total_commission' => $group->sum('commission_agence'),
                'nombre' => $group->count(),
            ];
        })->sortByDesc('total_commission');
    }

    /**
     * Grouper les paiements par locataire
     */
    private function groupPaymentsByLocataire(Collection $paiements): Collection
    {
        return $paiements->groupBy(function ($paiement) {
            return $paiement->payable?->locataire_id;
        })->map(function ($group) {
            $firstPayable = $group->first()->payable;
            return [
                'locataire' => $firstPayable->locataire,
                'total_encaisse' => $group->sum('montant'),
                'total_commission' => $group->sum('commission_agence'),
                'nombre' => $group->count(),
            ];
        })->sortByDesc('total_commission');
    }

    /**
     * Grouper les paiements par client (ventes)
     */
    private function groupPaymentsByClient(Collection $paiements): Collection
    {
        return $paiements->groupBy(function ($paiement) {
            return $paiement->payable?->client_id;
        })->map(function ($group) {
            $firstPayable = $group->first()->payable;
            return [
                'client' => $firstPayable->client,
                'total_encaisse' => $group->sum('montant'),
                'total_commission' => $group->sum('commission_agence'),
                'nombre' => $group->count(),
            ];
        })->sortByDesc('total_commission');
    }
}
