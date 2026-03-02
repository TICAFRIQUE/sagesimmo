<?php

namespace App\Services;

use App\Models\Paiement;
use App\Models\Vente;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RapportAcheteurService
{
    /**
     * Générer le rapport complet d'un acheteur
     */
    public function genererRapport(User $acheteur, Carbon $dateDebut, Carbon $dateFin): array
    {
        $ventes = $acheteur->ventes()
            ->with(['annonce.typeBien', 'paiements'])
            ->whereIn('statut', ['offre_acceptee', 'terminee'])
            ->get();

        $rapportParVente = $ventes->map(function (Vente $vente) use ($dateDebut, $dateFin) {
            return $this->calculerRapportVente($vente, $dateDebut, $dateFin);
        });

        // KPI globaux
        $totalAPayer = $rapportParVente->sum('prix_vente');
        $totalPaye = $rapportParVente->sum('total_paye');
        $totalRestant = $rapportParVente->sum('reste_a_payer');
        
        // Paiements de la période
        $paiementsPeriode = $rapportParVente->pluck('paiements_periode')->flatten(1);
        $totalPayePeriode = $paiementsPeriode->sum('montant');
        
        // Compteurs paiements par statut
        $ventesEnCours = $rapportParVente->filter(fn($r) => $r['reste_a_payer'] > 0);
        $ventesTerminees = $rapportParVente->filter(fn($r) => $r['reste_a_payer'] <= 0);

        // Statut global
        $statutGlobal = $this->determinerStatutGlobal($totalPaye, $totalAPayer, $ventes);

        return [
            'acheteur' => $acheteur,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'periode' => $dateDebut->format('d/m/Y') . ' au ' . $dateFin->format('d/m/Y'),
            'ventes' => $rapportParVente,
            'nombre_ventes' => $ventes->count(),
            
            // KPI financiers globaux
            'total_a_payer' => $totalAPayer,
            'total_paye' => $totalPaye,
            'total_restant' => $totalRestant,
            'taux_paiement' => $totalAPayer > 0 ? round(($totalPaye / $totalAPayer) * 100, 1) : 0,
            
            // Période
            'total_paye_periode' => $totalPayePeriode,
            'nb_paiements_periode' => $paiementsPeriode->count(),
            
            // Compteurs
            'nb_ventes_en_cours' => $ventesEnCours->count(),
            'nb_ventes_terminees' => $ventesTerminees->count(),
            
            // Statut
            'statut_global' => $statutGlobal,
        ];
    }

    /**
     * Générer un aperçu rapide pour la liste
     */
    public function genererApercu(User $acheteur, Carbon $dateDebut, Carbon $dateFin): array
    {
        $ventes = $acheteur->ventes()
            ->with(['annonce', 'paiements'])
            ->whereIn('statut', ['offre_acceptee', 'terminee'])
            ->get();

        $totalAPayer = $ventes->sum('prix_vente');
        $totalPaye = $ventes->sum(fn($v) => $v->montantTotalPaye());
        $totalRestant = $totalAPayer - $totalPaye;

        // Paiements de la période
        $paiementsPeriode = Paiement::where('payable_type', Vente::class)
            ->whereIn('payable_id', $ventes->pluck('id'))
            ->where('statut', 'paye')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->get();

        $statutGlobal = $this->determinerStatutGlobal($totalPaye, $totalAPayer, $ventes);

        return [
            'nb_ventes' => $ventes->count(),
            'total_a_payer' => $totalAPayer,
            'total_paye' => $totalPaye,
            'total_restant' => $totalRestant,
            'taux_paiement' => $totalAPayer > 0 ? round(($totalPaye / $totalAPayer) * 100, 1) : 0,
            'total_paye_periode' => $paiementsPeriode->sum('montant'),
            'nb_paiements_periode' => $paiementsPeriode->count(),
            'statut_global' => $statutGlobal,
            'ventes' => $ventes,
        ];
    }

    /**
     * Calculer le rapport pour une vente spécifique
     */
    private function calculerRapportVente(Vente $vente, Carbon $dateDebut, Carbon $dateFin): array
    {
        $prixVente = $vente->prix_vente;
        $totalPaye = $vente->montantTotalPaye();
        $resteAPayer = $vente->resteAPayer();
        $pourcentage = $vente->pourcentagePaiement();

        // Paiements sur la période
        $paiementsPeriode = $vente->paiements
            ->filter(function ($p) use ($dateDebut, $dateFin) {
                $datePaiement = Carbon::parse($p->date_paiement);
                return $p->statut === 'paye' && $datePaiement->between($dateDebut, $dateFin);
            })
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'date' => $p->date_paiement,
                    'montant' => $p->montant,
                    'type' => $p->type_paiement,
                    'methode' => $p->methode_paiement,
                    'reference' => $p->reference,
                    'statut' => $p->statut,
                ];
            })
            ->sortBy('date')
            ->values();

        // Tous les paiements
        $tousPaiements = $vente->paiements
            ->where('statut', 'paye')
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'date' => $p->date_paiement,
                    'montant' => $p->montant,
                    'type' => $p->type_paiement,
                    'methode' => $p->methode_paiement,
                    'reference' => $p->reference,
                    'statut' => $p->statut,
                ];
            })
            ->sortBy('date')
            ->values();

        // Déterminer statut paiement de cette vente
        $statutPaiement = $this->determinerStatutVente($totalPaye, $prixVente, $vente);

        return [
            'vente' => $vente,
            'bien' => $vente->annonce,
            'adresse' => $vente->annonce->adresse ?? 'N/A',
            'type_bien' => $vente->annonce->typeBien?->nom ?? 'N/A',
            'prix_vente' => $prixVente,
            'total_paye' => $totalPaye,
            'reste_a_payer' => $resteAPayer,
            'pourcentage_paiement' => $pourcentage,
            'date_vente' => $vente->date_vente,
            'statut_vente' => $vente->statut,
            'commission_agence' => $vente->calculerCommission(),

            // Paiements
            'paiements_periode' => $paiementsPeriode,
            'total_paye_periode' => $paiementsPeriode->sum('montant'),
            'tous_paiements' => $tousPaiements,
            
            // Statut
            'statut_paiement' => $statutPaiement,
        ];
    }

    /**
     * Déterminer statut paiement d'une vente
     */
    private function determinerStatutVente($totalPaye, $prixVente, Vente $vente): array
    {
        if ($totalPaye >= $prixVente) {
            return ['label' => 'Payé intégralement', 'badge' => 'success', 'code' => 'paye'];
        }
        if ($totalPaye > 0) {
            return ['label' => 'Paiement partiel', 'badge' => 'info', 'code' => 'partiel'];
        }
        if ($vente->statut === 'offre_acceptee') {
            return ['label' => 'En attente de paiement', 'badge' => 'warning', 'code' => 'en_attente'];
        }
        return ['label' => 'Non payé', 'badge' => 'danger', 'code' => 'non_paye'];
    }

    /**
     * Déterminer le statut global de l'acheteur
     */
    private function determinerStatutGlobal($totalPaye, $totalAPayer, $ventes): array
    {
        if ($ventes->count() === 0) {
            return ['label' => 'Aucune vente', 'badge' => 'secondary', 'code' => 'aucun'];
        }
        if ($totalAPayer > 0 && $totalPaye >= $totalAPayer) {
            return ['label' => 'Tout payé', 'badge' => 'success', 'code' => 'paye'];
        }
        if ($totalPaye > 0 && $totalPaye < $totalAPayer) {
            return ['label' => 'Paiement en cours', 'badge' => 'info', 'code' => 'en_cours'];
        }
        if ($totalPaye == 0 && $totalAPayer > 0) {
            return ['label' => 'En attente paiement', 'badge' => 'warning', 'code' => 'en_attente'];
        }
        return ['label' => 'Aucun', 'badge' => 'secondary', 'code' => 'aucun'];
    }
}
