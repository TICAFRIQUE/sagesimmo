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
        'pieces_demandees',
        'pieces_fournies',
        'documents_urls',
        'raison_refus_dossier',
        'contrat_url',
        'date_signature_contrat',
        'montant_caution',
        'montant_loyer_premier',
        'montant_frais_agence',
        'montant_total_paiement',
        'statut_paiement',
        'details_paiement',
        'commission_agence',
        'type_commission',
        'date_finalisation',
        'motif_refus',
        'note_admin',
    ];

    protected $casts = [
        'date_visite' => 'datetime',
        'date_signature_contrat' => 'datetime',
        'date_finalisation' => 'datetime',
        'pieces_fournies' => 'array',
        'documents_urls' => 'array',
        'details_paiement' => 'array',
        'client_interesse_apres_visite' => 'boolean',
        'montant_caution' => 'decimal:2',
        'montant_loyer_premier' => 'decimal:2', 
        'montant_frais_agence' => 'decimal:2',
        'montant_total_paiement' => 'decimal:2',
        'commission_agence' => 'decimal:2',
    ];

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('documents_client')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'application/pdf']);

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
            'nouvelle' => '<span class="badge bg-primary">Nouvelle</span>',
            'visite_planifiee' => '<span class="badge bg-info">Visite planifiée</span>',
            'visite_effectuee' => '<span class="badge bg-cyan">Visite effectuée</span>',
            'documents_recus' => '<span class="badge bg-purple">Documents reçus</span>',
            'dossier_valide' => '<span class="badge bg-success">Dossier validé</span>',
            'contrat_genere' => '<span class="badge bg-dark">Contrat généré</span>',
            'paiement_en_attente' => '<span class="badge bg-warning">Paiement en attente</span>',
            'paiement_valide' => '<span class="badge bg-success">Paiement validé</span>',
            'cloture_refus' => '<span class="badge bg-danger">Clôturé - Refusé</span>',
            'cloture_non_interesse' => '<span class="badge bg-secondary">Clôturé - Non intéressé</span>',
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
            'visite_planifiee' => 'Visite planifiée',
            'visite_effectuee' => 'Visite effectuée',
            'documents_recus' => 'Documents reçus',
            'dossier_valide' => 'Dossier validé',
            'contrat_genere' => 'Contrat généré',
            'paiement_en_attente' => 'Paiement en attente',
            'paiement_valide' => 'Paiement validé',
            'cloture_refus' => 'Clôturé - Refusé',
            'cloture_non_interesse' => 'Clôturé - Non intéressé',
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
            'visite_planifiee' => 20,
            'visite_effectuee' => 35,
            'documents_recus' => 50,
            'dossier_valide' => 65,
            'contrat_genere' => 80,
            'paiement_en_attente' => 90,
            'paiement_valide' => 100,
            'cloture_refus' => 0,
            'cloture_non_interesse' => 0,
        ];

        return $progressions[$this->statut] ?? 0;
    }

    /**
     * Vérifier si la demande est clôturée
     */
    public function isClotureAttribute()
    {
        return in_array($this->statut, ['cloture_refus', 'cloture_non_interesse', 'paiement_valide']);
    }

    /**
     * Vérifier si la demande est en cours
     */
    public function isEnCoursAttribute()
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
