<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class DemandeInteret extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'annonce_id',
        'message',
        'statut',
        'date_visite',
        'compte_rendu_visite',
        'client_interesse_apres_visite',
        'contrat_url',
        'montant_caution',
        'montant_loyer_premier',
        'montant_frais_agence',
        'montant_total_paiement',
        'statut_paiement',
        'details_paiement',
        'date_finalisation',
        'motif_cloture',
        'note_admin',
    ];

    protected $casts = [
        'date_visite' => 'datetime',
        'date_finalisation' => 'datetime',
        'details_paiement' => 'array',
        'client_interesse_apres_visite' => 'boolean',
        'montant_caution' => 'decimal:2',
        'montant_loyer_premier' => 'decimal:2', 
        'montant_frais_agence' => 'decimal:2',
        'montant_total_paiement' => 'decimal:2',
    ];

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('contrat')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);
    }

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'annonce
     */
    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    /**
     * Obtenir le badge de statut formaté
     */
    public function getStatutBadgeAttribute()
    {
        $badges = [
            'nouvelle' => '<span class="badge bg-primary">Nouvelle demande</span>',
            'contrat_envoye' => '<span class="badge bg-info">Fiche envoyée</span>',
            'visite_planifiee' => '<span class="badge bg-cyan">Visite planifiée</span>',
            'visite_effectuee' => '<span class="badge bg-purple">Visite effectuée</span>',
            'paiement_en_attente' => '<span class="badge bg-warning">Paiement en attente</span>',
            'paiement_valide' => '<span class="badge bg-success">✅ Finalisé - Clés remises</span>',
            'cloture' => '<span class="badge bg-danger">❌ Clôturé</span>',
        ];

        return $badges[$this->statut] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }

    /**
     * Obtenir le label du statut
     */
    public function getStatutLabelAttribute()
    {
        $labels = [
            'nouvelle' => 'Nouvelle demande',
            'contrat_envoye' => 'Fiche d\'information envoyée',
            'visite_planifiee' => 'Visite planifiée',
            'visite_effectuee' => 'Visite effectuée',
            'paiement_en_attente' => 'En attente de paiement',
            'paiement_valide' => 'Finalisé - Clés remises',
            'cloture' => 'Clôturé',
        ];

        return $labels[$this->statut] ?? 'Inconnu';
    }

    /**
     * Obtenir la progression en pourcentage
     */
    public function getProgressionAttribute()
    {
        $progressions = [
            'nouvelle' => 10,
            'contrat_envoye' => 20,
            'visite_planifiee' => 35,
            'visite_effectuee' => 50,
            'paiement_en_attente' => 75,
            'paiement_valide' => 100,
            'cloture' => 0,
        ];

        return $progressions[$this->statut] ?? 0;
    }

    /**
     * Vérifier si la demande est clôturée
     */
    public function getIsClotureAttribute()
    {
        return in_array($this->statut, ['cloture', 'paiement_valide']);
    }

    /**
     * Vérifier si la demande est en cours
     */
    public function getIsEnCoursAttribute()
    {
        return !$this->is_cloture;
    }

    /**
     * Relation avec la vente créée depuis cette demande
     */
    public function vente()
    {
        return $this->hasOne(Vente::class, 'demande_interet_id');
    }

    /**
     * Relation avec la location créée depuis cette demande
     */
    public function location()
    {
        return $this->hasOne(Location::class, 'demande_interet_id');
    }

    /**
     * Vérifier si la demande a été convertie en vente ou location
     */
    public function getIsConvertieAttribute()
    {
        return $this->vente()->exists() || $this->location()->exists();
    }
}
