<?php

namespace App\Services;

use App\Models\Annonce;
use App\Models\Charge;
use App\Models\Location;
use App\Models\Paiement;
use App\Models\User;
use App\Models\Versement;
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

        // Récupérer les versements
        $versements = $this->versementsParPeriode($proprietaire, $dateDebut, $dateFin);
        $totalVersementsEffectues = $versements->where('statut', 'effectue')->sum('montant');
        $totalVersementsPartiels = $versements->where('statut', 'partiel')->sum('montant');
        $totalVersementEnAttente = $versements->where('statut', 'en_attente')->sum('montant');
        
        // Montant total versé (effectué + partiel)
        $montantTotalVerse = $totalVersementsEffectues + $totalVersementsPartiels;
        $resteAVerser = $revenueNet - $montantTotalVerse;

        // Déterminer le statut automatiquement
        $statut = $this->determinerStatutVersement($montantTotalVerse, $revenueNet, $versements);

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
            
            // Versements
            'versements' => $versements,
            'total_versements_effectues' => $totalVersementsEffectues,
            'total_versements_partiels' => $totalVersementsPartiels,
            'total_versement_en_attente' => $totalVersementEnAttente,
            'montant_total_verse' => $montantTotalVerse,
            'reste_a_verser' => $resteAVerser,
            'statut_versement' => $statut,
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
            'type_transaction' => $bien->type_transaction ?? 'N/A',
            
            
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
        // Commissions des locations - récupérer depuis Location, pas Paiement
        $commissionsLocations = Paiement::with(['payable'])
            ->where('payable_type', Location::class)
            ->where('type_paiement', 'loyer')
            ->where('statut', 'paye')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->whereHas('payable', function ($q) use ($bien) {
                $q->where('annonce_id', $bien->id);
            })
            ->get()
            ->filter(function ($paiement) {
                // Filtrer uniquement si la location a une commission
                return $paiement->payable && $paiement->payable->commission_agence;
            })
            ->map(function ($paiement) {
                $montant = floatval($paiement->montant);
                $location = $paiement->payable;
                
                // Récupérer la configuration de commission depuis Location
                $commissionAgence = floatval($location->commission_agence ?? 0);
                $typeCommission = $location->type_commission ?? 'montant';
                
                // Calculer la commission sur ce paiement de loyer
                $commission = $typeCommission === 'pourcentage'
                    ? ($montant * $commissionAgence / 100)
                    : $commissionAgence;
                
                return [
                    'commission_agence' => $commission,
                    'type' => 'location',
                    'date' => $paiement->date_paiement,
                ];
            });

        // Commissions des ventes - récupérer depuis Vente
        $commissionsVentes = Paiement::with(['payable'])
            ->where('payable_type', \App\Models\Vente::class)
            ->where('statut', 'paye')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->whereHas('payable', function ($q) use ($bien) {
                $q->where('annonce_id', $bien->id);
            })
            ->get()
            ->filter(function ($paiement) {
                // Filtrer uniquement si la vente a une commission
                return $paiement->payable && $paiement->payable->commission_agence;
            })
            ->map(function ($paiement) {
                $vente = $paiement->payable;
                
                // Récupérer la configuration de commission depuis Vente
                $commissionAgence = floatval($vente->commission_agence ?? 0);
                $typeCommission = $vente->type_commission ?? 'montant';
                
                // Calculer la commission selon le type
                $commission = $typeCommission === 'pourcentage'
                    ? ($vente->prix_vente * $commissionAgence / 100)
                    : $commissionAgence;
                
                return [
                    'commission_agence' => $commission,
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

    /**
     * Obtenir les versements pour une période donnée
     */
    private function versementsParPeriode(User $proprietaire, Carbon $dateDebut, Carbon $dateFin): Collection
    {
        return Versement::where('proprietaire_id', $proprietaire->id)
            ->where(function ($query) use ($dateDebut, $dateFin) {
                $query->whereBetween('date_debut', [$dateDebut, $dateFin])
                    ->orWhereBetween('date_fin', [$dateDebut, $dateFin])
                    ->orWhere(function ($q) use ($dateDebut, $dateFin) {
                        $q->where('date_debut', '<=', $dateFin)
                            ->where('date_fin', '>=', $dateDebut);
                    });
            })
            ->orderBy('date_versement', 'desc')
            ->get();
    }

    /**
     * Déterminer le statut du versement automatiquement
     */
    private function determinerStatutVersement($montantTotalVerse, $revenueNet, Collection $versements): array
    {
        // Pas de revenu disponible pour cette période
        if ($revenueNet <= 0) {
            return [
                'label' => 'Aucun versement disponible',
                'badge' => 'secondary',
            ];
        }

        // Aucun versement effectué
        if ($montantTotalVerse == 0) {
            return [
                'label' => 'Versement disponible',
                'badge' => 'warning',
            ];
        }

        // Versement partiel (reçu mais moins que le montant dû)
        if ($montantTotalVerse > 0 && $montantTotalVerse < $revenueNet) {
            return [
                'label' => 'Versement partiel',
                'badge' => 'info',
            ];
        }

        // Versement effectué complètement
        if ($montantTotalVerse >= $revenueNet) {
            return [
                'label' => 'Versement totalité effectué',
                'badge' => 'success',
            ];
        }

        // Fallback
        return [
            'label' => 'Année en cours',
            'badge' => 'secondary',
        ];
    }
}

