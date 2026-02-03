<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Vente extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'demande_interet_id',
        'annonce_id',
        'client_id',
        'message_client',
        'prix_vente',
        'montant_caution',
        'montant_frais_agence',
        'commission_agence',
        'type_commission',
        'date_vente',
        'date_signature',
        'date_visite',
        'compte_rendu_visite',
        'date_finalisation',
        'statut',
        'notes',
        'note_admin',
    ];

    protected $casts = [
        'date_vente' => 'date',
        'date_signature' => 'date',
        'date_visite' => 'datetime',
        'date_finalisation' => 'datetime',
        'prix_vente' => 'decimal:2',
        'montant_caution' => 'decimal:2',
        'montant_frais_agence' => 'decimal:2',
        'commission_agence' => 'decimal:2',
    ];

    public function demandeInteret()
    {
        return $this->belongsTo(DemandeInteret::class, 'demande_interet_id');
    }

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function paiements()
    {
        return $this->morphMany(Paiement::class, 'payable');
    }

    public function montantTotal()
    {
        return $this->paiements()->sum('montant');
    }

    public function resteAPayer()
    {
        return $this->prix_vente - $this->montantTotal();
    }

    public function calculerCommission()
    {
        if (!$this->commission_agence) {
            return 0;
        }

        if ($this->type_commission === 'pourcentage') {
            return ($this->prix_vente * $this->commission_agence) / 100;
        }

        return $this->commission_agence;
    }

    /**
     * Obtenir le badge de statut formaté
     */
    public function getStatutBadgeAttribute()
    {
        $badges = [
            'demande_client' => '<span class="badge bg-primary">Demande client</span>',
            'fiche_envoyee' => '<span class="badge bg-info">Fiche envoyée</span>',
            'visite_planifiee' => '<span class="badge bg-cyan">Visite planifiée</span>',
            'en_attente_paiement' => '<span class="badge bg-warning">En attente paiement</span>',
            'paiement_valide' => '<span class="badge bg-success">Finalisé</span>',
            'annule' => '<span class="badge bg-danger">Annulé</span>',
        ];

        return $badges[$this->statut] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }

    /**
     * Obtenir la progression en pourcentage
     */
    public function getProgressionAttribute()
    {
        $progressions = [
            'demande_client' => 20,
            'fiche_envoyee' => 40,
            'visite_planifiee' => 60,
            'en_attente_paiement' => 80,
            'paiement_valide' => 100,
            'annule' => 0,
        ];

        return $progressions[$this->statut] ?? 0;
    }
}
