<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Location extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'demande_interet_id',
        'annonce_id',
        'locataire_id',
        'message_client',
        'loyer_mensuel',
        'avance_sur_loyer',
        'montant_avance',
        'premier_paiement_valide',
        'nombre_cautions',
        'caution',
        'montant_frais_agence',
        'commission_agence',
        'type_commission',
        'date_debut',
        'date_fin',
        'date_visite',
        'compte_rendu_visite',
        'date_finalisation',
        'statut',
        'jour_paiement',
        'conditions',
        'note_admin',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_visite' => 'datetime',
        'date_finalisation' => 'datetime',
        'loyer_mensuel' => 'decimal:2',
        'avance_sur_loyer' => 'integer',
        'montant_avance' => 'decimal:2',
        'premier_paiement_valide' => 'boolean',
        'caution' => 'decimal:2',
        'montant_frais_agence' => 'decimal:2',
        'commission_agence' => 'decimal:2',
        'type_commission' => 'string',
    ];

    public function demandeInteret()
    {
        return $this->belongsTo(DemandeInteret::class, 'demande_interet_id');
    }

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    public function locataire()
    {
        return $this->belongsTo(User::class, 'locataire_id');
    }

    public function paiements()
    {
        return $this->morphMany(Paiement::class, 'payable');
    }

    public function paiementsLoyer()
    {
        return $this->morphMany(Paiement::class, 'payable')->where('type_paiement', 'loyer');
    }

    public function echeances()
    {
        return $this->hasMany(Echeance::class);
    }

    public function montantTotalPaye()
    {
        return $this->paiements()->sum('montant');
    }

    /**
     * Calculer le montant total du premier paiement
     */
    public function getMontantPremierPaiementAttribute()
    {
        return $this->caution + $this->montant_avance + $this->montant_frais_agence;
    }

    /**
     * Vérifier si le premier paiement est complet
     */
    public function premierPaiementComplet()
    {
        $totalPremiersPaiements = $this->paiements()
            ->whereIn('type_paiement', ['caution', 'avance', 'frais_agence'])
            ->sum('montant');
        
        return $totalPremiersPaiements >= $this->montant_premier_paiement;
    }

    /**
     * Générer les échéances mensuelles
     */
    public function genererEcheances()
    {
        if ($this->premier_paiement_valide) {
            return; // Échéances déjà générées
        }

        $dateDebut = $this->date_debut;
        // Si pas de date_fin, générer pour 2 ans (24 mois) au lieu de 1 an
        $dateFin = $this->date_fin ?? now()->addYears(2);
        
        $currentDate = $dateDebut->copy();
        $echeanceNumber = 1;

        while ($currentDate->lte($dateFin)) {
            $estPayeParAvance = $echeanceNumber <= $this->avance_sur_loyer;
            
            $echeance = Echeance::create([
                'location_id' => $this->id,
                'date_echeance' => $currentDate->copy(),
                'montant_du' => $this->loyer_mensuel,
                'montant_paye' => $estPayeParAvance ? $this->loyer_mensuel : 0,
                'statut' => $estPayeParAvance ? 'paye' : 'a_echeance',
                'commission_agence' => $this->calculerCommission($this->loyer_mensuel),
            ]);

            // Si échéance payée par avance, créer un paiement virtuel
            if ($estPayeParAvance) {
                $paiement = $this->paiements()->where('type_paiement', 'avance')->first();
                if ($paiement) {
                    // Lier le paiement d'avance aux premières échéances
                    $echeance->paiements()->create([
                        'payable_type' => Location::class,
                        'payable_id' => $this->id,
                        'type_paiement' => 'loyer',
                        'statut' => 'paye',
                        'montant' => $this->loyer_mensuel,
                        'date_paiement' => $paiement->date_paiement,
                        'methode_paiement' => $paiement->methode_paiement,
                        'notes' => 'Payé par avance (échéance ' . $echeanceNumber . ')',
                    ]);
                }
            }

            $currentDate->addMonth();
            $echeanceNumber++;
        }

        $this->update(['premier_paiement_valide' => true]);
    }

    /**
     * Générer les échéances supplémentaires pour prolonger la location
     * À utiliser quand toutes les échéances existantes arrivent à terme
     */
    public function genererEcheancesSuivantes($nombreMois = 12)
    {
        // Convertir en entier pour éviter les erreurs Carbon
        $nombreMois = (int) $nombreMois;
        
        if ($this->statut !== 'actif') {
            return false;
        }

        // Trouver la dernière échéance existante
        $derniereEcheance = $this->echeances()->orderBy('date_echeance', 'desc')->first();
        
        if (!$derniereEcheance) {
            return false;
        }

        $dateDebut = $derniereEcheance->date_echeance->copy()->addMonth();
        $dateFin = $this->date_fin ?? $dateDebut->copy()->addMonths($nombreMois);
        
        $currentDate = $dateDebut->copy();
        $echeancesCreees = 0;

        while ($currentDate->lte($dateFin) && $echeancesCreees < $nombreMois) {
            Echeance::create([
                'location_id' => $this->id,
                'date_echeance' => $currentDate->copy(),
                'montant_du' => $this->loyer_mensuel,
                'montant_paye' => 0,
                'statut' => 'a_echeance',
                'commission_agence' => $this->calculerCommission($this->loyer_mensuel),
            ]);

            $currentDate->addMonth();
            $echeancesCreees++;
        }

        return $echeancesCreees;
    }

    /**
     * Vérifier si de nouvelles échéances doivent être générées
     * Retourne true s'il reste moins de 3 mois d'échéances
     */
    public function doitGenererNouvellesEcheances()
    {
        if ($this->statut !== 'actif') {
            return false;
        }

        // Compter les échéances futures (non payées et à venir)
        $echeancesFutures = $this->echeances()
            ->where('date_echeance', '>', now())
            ->whereIn('statut', ['a_echeance', 'en_retard', 'impaye'])
            ->count();

        // Si moins de 3 mois d'échéances à venir, il faut en générer de nouvelles
        return $echeancesFutures < 3;
    }

    /**
     * Obtenir le total des commissions perçues (en FCFA réel)
     * Prend en compte le type de commission (pourcentage ou montant) de chaque paiement
     */
    public function totalCommissionsPercues()
    {
        return $this->paiements->sum(function($paiement) {
            return $paiement->montant_commission;
        });
    }

    /**
     * Calculer la commission de l'agence pour un montant donné
     */
    public function calculerCommission($montant)
    {
        if (!$this->commission_agence) {
            return 0;
        }

        if ($this->type_commission === 'pourcentage') {
            return ($montant * $this->commission_agence) / 100;
        }

        return $this->commission_agence;
    }

    /**
     * Obtenir le badge de statut formaté
     */
    public function getStatutBadgeAttribute()
    {
        $badges = [
            'brouillon' => '<span class="badge bg-secondary">Brouillon</span>',
            'demande_client' => '<span class="badge bg-primary">Demande client</span>',
            'fiche_envoyee' => '<span class="badge bg-info">Fiche envoyée</span>',
            'visite_planifiee' => '<span class="badge bg-dark">Visite planifiée</span>',
            'en_attente_paiement' => '<span class="badge bg-warning">En attente paiement</span>',
            'actif' => '<span class="badge bg-success">Actif</span>',
            'termine' => '<span class="badge bg-secondary">Terminé</span>',
            'resilie' => '<span class="badge bg-danger">Résilié</span>',
        ];

        return $badges[$this->statut] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }

    /**
     * Obtenir la progression en pourcentage
     */
    public function getProgressionAttribute()
    {
        $progressions = [
            'brouillon' => 10,
            'demande_client' => 20,
            'fiche_envoyee' => 40,
            'visite_planifiee' => 60,
            'en_attente_paiement' => 80,
            'actif' => 100,
            'termine' => 100,
            'resilie' => 0,
        ];

        return $progressions[$this->statut] ?? 0;
    }
}
