<?php

namespace App\Services;

use App\Models\Annonce;
use App\Models\Charge;
use App\Models\Location;
use App\Models\Paiement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RapportProprietaireService
{
    /**
     * Générer le rapport financier complé d'un propriétaire
     */
    public function genererRapport(User $proprietaire, Carbon $dateDebut, Carbon $dateFin): array
    {
        $biens = $proprietaire->annonces()->get();

        $rapportParBien = $biens->map(function (Annonce $bien) use ($dateDebut, $dateFin) {
            return $this->calculerRapportBien($bien, $dateDebut, $dateFin);
        });

        // Agrégation totale
        $totalBrutEncaisse = $rapportParBien->sum('total_brut_encaisse');
        $totalCharges = $rapportParBien->sum('total_charges');
        $totalCommissionAgence = $rapportParBien->sum('total_commission_agence');
        $revenueNet = $totalBrutEncaisse - $totalCommissionAgence - $totalCharges;

        return [
            'proprietaire' => $proprietaire,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'periode' => $dateDebut->format('d/m/Y') . ' au ' . $dateFin->format('d/m/Y'),
            'biens' => $rapportParBien,
            'nombre_biens' => $biens->count(),
            'total_brut_encaisse' => $totalBrutEncaisse,
            'total_charges' => $totalCharges,
            'total_commission_agence' => $totalCommissionAgence,
            'revenue_net' => $revenueNet,
            'detail_charges' => $this->detailChargesParProprietaire($proprietaire, $dateDebut, $dateFin),
        ];
    }

    /**
     * Calculer le rapport pour un bien spécifique
     */
    public function calculerRapportBien(Annonce $bien, Carbon $dateDebut, Carbon $dateFin): array
    {
        // 1. Encaissements (Loyers + Ventes)
        $encaissementLoyers = $this->calculerEncaissementLoyers($bien, $dateDebut, $dateFin);
        $encaissementVentes = $this->calculerEncaissementVentes($bien, $dateDebut, $dateFin);
        $totalBrutEncaisse = $encaissementLoyers['total'] + $encaissementVentes['total'];

        // 2. Charges
        $chargesDetails = $this->chargesByBien($bien, $dateDebut, $dateFin);
        $totalCharges = $chargesDetails->sum('montant');

        // 3. Commission agence
        $commissionDetails = $this->commissionsByBien($bien, $dateDebut, $dateFin);
        $totalCommission = $commissionDetails->sum('commission_agence');

        // 4. Revenu net
        $revenueNet = $totalBrutEncaisse - $totalCharges - $totalCommission;

        return [
            'bien' => $bien,
            'adresse' => $bien->adresse ?? 'N/A',
            'ville' => $bien->ville ?? 'N/A',
            'type_bien' => $bien->typeBien?->nom ?? 'N/A',
            
            // Encaissements
            'encaissement_loyers' => $encaissementLoyers,
            'encaissement_ventes' => $encaissementVentes,
            'total_brut_encaisse' => $totalBrutEncaisse,
            
            // Charges
            'charges' => $chargesDetails,
            'total_charges' => $totalCharges,
            'detail_charges_par_type' => $chargesDetails->groupBy('type_charge'),
            
            // Commission
            'commissions' => $commissionDetails,
            'total_commission_agence' => $totalCommission,
            
            // Résultat
            'revenue_net' => $revenueNet,
        ];
    }

    /**
     * Calculer les encaissements de loyers
     */
    private function calculerEncaissementLoyers(Annonce $bien, Carbon $dateDebut, Carbon $dateFin): array
    {
        $paiements = Paiement::where('payable_type', Location::class)
            ->where('type_paiement', 'loyer')
            ->where('statut', 'paye')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->whereHas('payable', function ($q) use ($bien) {
                $q->where('annonce_id', $bien->id);
            })
            ->get();

        $total = $paiements->sum('montant');

        return [
            'paiements' => $paiements,
            'nombre' => $paiements->count(),
            'total' => $total,
            'type' => 'location',
        ];
    }

    /**
     * Calculer les encaissements de ventes
     */
    private function calculerEncaissementVentes(Annonce $bien, Carbon $dateDebut, Carbon $dateFin): array
    {
        $paiements = Paiement::where('payable_type', \App\Models\Vente::class)
            ->where('statut', 'paye')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->whereHas('payable', function ($q) use ($bien) {
                $q->where('annonce_id', $bien->id);
            })
            ->get();

        $total = $paiements->sum('montant');

        return [
            'paiements' => $paiements,
            'nombre' => $paiements->count(),
            'total' => $total,
            'type' => 'vente',
        ];
    }

    /**
     * Obtenir les charges d'un bien
     */
    private function chargesByBien(Annonce $bien, Carbon $dateDebut, Carbon $dateFin): Collection
    {
        return Charge::where('annonce_id', $bien->id)
            ->whereBetween('date_charge', [$dateDebut, $dateFin])
            ->get();
    }

    /**
     * Obtenir les commissions d'un bien
     */
    private function commissionsByBien(Annonce $bien, Carbon $dateDebut, Carbon $dateFin): Collection
    {
        $commissionsLocations = Paiement::where('payable_type', Location::class)
            ->where('type_paiement', 'loyer')
            ->where('statut', 'paye')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->whereHas('payable', function ($q) use ($bien) {
                $q->where('annonce_id', $bien->id);
            })
            ->get()
            ->map(function ($paiement) {
                return [
                    'commission_agence' => $paiement->commission_agence ?? 0,
                    'type' => 'location',
                    'date' => $paiement->date_paiement,
                ];
            });

        $commissionsVentes = Paiement::where('payable_type', \App\Models\Vente::class)
            ->where('statut', 'paye')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->whereHas('payable', function ($q) use ($bien) {
                $q->where('annonce_id', $bien->id);
            })
            ->get()
            ->map(function ($paiement) {
                return [
                    'commission_agence' => $paiement->commission_agence ?? 0,
                    'type' => 'vente',
                    'date' => $paiement->date_paiement,
                ];
            });

        return collect()->concat($commissionsLocations)->concat($commissionsVentes);
    }

    /**
     * Détail des charges par propriétaire
     */
    private function detailChargesParProprietaire(User $proprietaire, Carbon $dateDebut, Carbon $dateFin): array
    {
        $charges = Charge::whereHas('annonce', function ($q) use ($proprietaire) {
            $q->where('proprietaire_id', $proprietaire->id);
        })
            ->whereBetween('date_charge', [$dateDebut, $dateFin])
            ->get();

        return [
            'total' => $charges->sum('montant'),
            'par_type' => $charges->groupBy('type_charge')->map->sum('montant'),
            'par_bien' => $charges->groupBy('annonce_id'),
            'nombre_charges' => $charges->count(),
        ];
    }
}
