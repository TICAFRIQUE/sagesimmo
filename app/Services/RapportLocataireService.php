<?php

namespace App\Services;

use App\Models\Echeance;
use App\Models\Location;
use App\Models\Paiement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RapportLocataireService
{
    /**
     * Générer le rapport complet d'un locataire
     */
    public function genererRapport(User $locataire, ?Carbon $dateDebut = null, ?Carbon $dateFin = null): array
    {
        $locations = $locataire->locations()
            ->with(['annonce.typeBien', 'echeances.paiements'])
            ->whereIn('statut', ['actif', 'resilie', 'en_attente_paiement'])
            ->get();

        $rapportParLocation = $locations->map(function (Location $location) use ($dateDebut, $dateFin) {
            return $this->calculerRapportLocation($location, $dateDebut, $dateFin);
        });

        // Toutes les échéances de la période
        $toutesEcheances = $rapportParLocation->pluck('echeances')->flatten(1);

        // KPI
        $totalDu = $toutesEcheances->sum('montant_du');
        $totalPaye = $toutesEcheances->sum('montant_paye');
        $totalRestant = $toutesEcheances->sum('montant_restant');
        
        $echeancesEnRetard = $toutesEcheances->filter(fn($e) => in_array($e['statut'], ['en_retard']));
        $echeancesImpayees = $toutesEcheances->filter(fn($e) => in_array($e['statut'], ['impaye']));
        $echeancesAVenir = $toutesEcheances->filter(fn($e) => $e['statut'] === 'a_echeance');
        $echeancesPayees = $toutesEcheances->filter(fn($e) => $e['statut'] === 'paye');

        // Prochaine échéance
        $prochaineEcheance = $this->prochaineEcheance($locataire);

        // Statut global du locataire
        $statutGlobal = $this->determinerStatutGlobal($echeancesEnRetard, $echeancesImpayees, $toutesEcheances);

        return [
            'locataire' => $locataire,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'periode' => $dateDebut && $dateFin ? $dateDebut->format('d/m/Y') . ' au ' . $dateFin->format('d/m/Y') : 'Toutes les échéances',
            'locations' => $rapportParLocation,
            'nombre_locations' => $locations->count(),
            
            // KPI financiers
            'total_du' => $totalDu,
            'total_paye' => $totalPaye,
            'total_restant' => $totalRestant,
            'taux_paiement' => $totalDu > 0 ? round(($totalPaye / $totalDu) * 100, 1) : 0,
            
            // KPI échéances
            'nb_echeances_total' => $toutesEcheances->count(),
            'nb_en_retard' => $echeancesEnRetard->count(),
            'nb_impayees' => $echeancesImpayees->count(),
            'nb_a_venir' => $echeancesAVenir->count(),
            'nb_payees' => $echeancesPayees->count(),
            'montant_en_retard' => $echeancesEnRetard->sum('montant_restant'),
            'montant_impaye' => $echeancesImpayees->sum('montant_restant'),
            
            // Prochaine échéance
            'prochaine_echeance' => $prochaineEcheance,
            
            // Statut
            'statut_global' => $statutGlobal,
        ];
    }

    /**
     * Générer un aperçu rapide pour la liste
     */
    public function genererApercu(User $locataire, ?Carbon $dateDebut = null, ?Carbon $dateFin = null): array
    {
        $locations = $locataire->locations()
            ->with(['annonce', 'echeances'])
            ->whereIn('statut', ['actif', 'resilie', 'en_attente_paiement'])
            ->get();

        $echeances = collect();
        foreach ($locations as $location) {
            $echeancesLocation = $location->echeances;
            if ($dateDebut && $dateFin) {
                $echeancesLocation = $echeancesLocation->filter(function ($e) use ($dateDebut, $dateFin) {
                    return $e->date_echeance->between($dateDebut, $dateFin);
                });
            }
            $echeances = $echeances->concat($echeancesLocation);
        }

        $totalDu = $echeances->sum('montant_du');
        $totalPaye = $echeances->sum('montant_paye');

        // Calculer le statut effectif de chaque échéance en temps réel
        $echeancesAvecStatut = $echeances->map(function ($e) {
            $e->statut_effectif = $this->calculerStatutEffectif($e);
            return $e;
        });

        $nbEnRetard = $echeancesAvecStatut->filter(fn($e) => in_array($e->statut_effectif, ['en_retard', 'impaye']))->count();
        $nbImpayees = $echeancesAvecStatut->filter(fn($e) => $e->statut_effectif === 'impaye')->count();
        $montantRetard = $echeancesAvecStatut->filter(fn($e) => in_array($e->statut_effectif, ['en_retard', 'impaye']))
            ->sum(fn($e) => $e->montant_du - $e->montant_paye);
        
        $prochaineEcheance = $this->prochaineEcheance($locataire);
        $statutGlobal = $this->determinerStatutGlobalFromCollection($echeancesAvecStatut);

        return [
            'nb_locations' => $locations->count(),
            'total_du' => $totalDu,
            'total_paye' => $totalPaye,
            'total_restant' => $totalDu - $totalPaye,
            'taux_paiement' => $totalDu > 0 ? round(($totalPaye / $totalDu) * 100, 1) : 0,
            'nb_echeances' => $echeances->count(),
            'nb_en_retard' => $nbEnRetard,
            'nb_impayees' => $nbImpayees,
            'montant_retard' => $montantRetard,
            'prochaine_echeance' => $prochaineEcheance,
            'statut_global' => $statutGlobal,
            'locations' => $locations,
        ];
    }

    /**
     * Calculer le statut effectif d'une échéance en temps réel
     * (ne dépend pas du champ statut en BDD qui peut être obsolète)
     */
    private function calculerStatutEffectif(Echeance $echeance): string
    {
        // Déjà payé intégralement
        if ($echeance->montant_paye >= $echeance->montant_du) {
            return 'paye';
        }

        // Paiement partiel
        if ($echeance->montant_paye > 0 && $echeance->montant_paye < $echeance->montant_du) {
            $joursRetard = $echeance->joursDeRetard();
            if ($joursRetard > 30) {
                return 'impaye';
            } elseif ($joursRetard > 0) {
                return 'en_retard';
            }
            return 'partiel';
        }

        // Aucun paiement et date dépassée
        if ($echeance->montant_paye == 0 && $echeance->date_echeance->isPast()) {
            $joursRetard = $echeance->joursDeRetard();
            if ($joursRetard > 30) {
                return 'impaye';
            }
            return 'en_retard';
        }

        return 'a_echeance';
    }

    /**
     * Calculer le rapport pour une location spécifique
     */
    private function calculerRapportLocation(Location $location, ?Carbon $dateDebut = null, ?Carbon $dateFin = null): array
    {
        $echeances = $location->echeances
            ->when($dateDebut && $dateFin, function ($collection) use ($dateDebut, $dateFin) {
                return $collection->filter(function ($echeance) use ($dateDebut, $dateFin) {
                    return $echeance->date_echeance->between($dateDebut, $dateFin);
                });
            })
            ->map(function ($echeance) {
                return [
                    'id' => $echeance->id,
                    'date_echeance' => $echeance->date_echeance,
                    'montant_du' => $echeance->montant_du,
                    'montant_paye' => $echeance->montant_paye,
                    'montant_restant' => $echeance->montant_du - $echeance->montant_paye,
                    'statut' => $this->calculerStatutEffectif($echeance),
                    'jours_retard' => $echeance->joursDeRetard(),
                    'priorite' => $echeance->niveauPriorite(),
                    'paiements' => $echeance->paiements,
                ];
            })
            ->sortBy('date_echeance')
            ->values();

        $totalDu = $echeances->sum('montant_du');
        $totalPaye = $echeances->sum('montant_paye');

        return [
            'location' => $location,
            'bien' => $location->annonce,
            'adresse' => $location->annonce->adresse ?? 'N/A',
            'type_bien' => $location->annonce->typeBien?->nom ?? 'N/A',
            'loyer_mensuel' => $location->loyer_mensuel,
            'date_debut' => $location->date_debut,
            'date_fin' => $location->date_fin,
            'statut_location' => $location->statut,
            'echeances' => $echeances,
            'total_du' => $totalDu,
            'total_paye' => $totalPaye,
            'total_restant' => $totalDu - $totalPaye,
            'taux_paiement' => $totalDu > 0 ? round(($totalPaye / $totalDu) * 100, 1) : 0,
        ];
    }

    /**
     * Obtenir la prochaine échéance d'un locataire
     */
    private function prochaineEcheance(User $locataire): ?array
    {
        $echeance = Echeance::whereHas('location', function ($q) use ($locataire) {
                $q->where('locataire_id', $locataire->id)
                  ->whereIn('statut', ['actif']);
            })
            ->where('statut', 'a_echeance')
            ->where('date_echeance', '>=', now())
            ->orderBy('date_echeance', 'asc')
            ->with('location.annonce')
            ->first();

        if (!$echeance) return null;

        return [
            'date' => $echeance->date_echeance,
            'montant' => $echeance->montant_du,
            'bien' => $echeance->location->annonce->adresse ?? 'N/A',
            'jours_restants' => now()->diffInDays($echeance->date_echeance, false),
        ];
    }

    /**
     * Déterminer le statut global (depuis tableaux)
     */
    private function determinerStatutGlobal($echeancesEnRetard, $echeancesImpayees, $toutesEcheances): array
    {
        if ($echeancesImpayees->count() > 0) {
            return ['label' => 'Impayé', 'badge' => 'danger', 'code' => 'impaye'];
        }
        if ($echeancesEnRetard->count() > 0) {
            return ['label' => 'En retard', 'badge' => 'warning', 'code' => 'en_retard'];
        }
        if ($toutesEcheances->count() === 0) {
            return ['label' => 'Aucune échéance', 'badge' => 'secondary', 'code' => 'aucun'];
        }
        return ['label' => 'À jour', 'badge' => 'success', 'code' => 'a_jour'];
    }

    /**
     * Déterminer le statut global (depuis modèles Eloquent avec statut effectif calculé)
     */
    private function determinerStatutGlobalFromCollection(Collection $echeances): array
    {
        $nbImpayees = $echeances->filter(fn($e) => ($e->statut_effectif ?? $this->calculerStatutEffectif($e)) === 'impaye')->count();
        $nbEnRetard = $echeances->filter(fn($e) => ($e->statut_effectif ?? $this->calculerStatutEffectif($e)) === 'en_retard')->count();

        if ($nbImpayees > 0) {
            return ['label' => 'Impayé', 'badge' => 'danger', 'code' => 'impaye'];
        }
        if ($nbEnRetard > 0) {
            return ['label' => 'En retard', 'badge' => 'warning', 'code' => 'en_retard'];
        }
        if ($echeances->count() === 0) {
            return ['label' => 'Aucune échéance', 'badge' => 'secondary', 'code' => 'aucun'];
        }
        return ['label' => 'À jour', 'badge' => 'success', 'code' => 'a_jour'];
    }
}
