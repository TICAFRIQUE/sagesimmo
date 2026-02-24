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
        'annonce_id',
        'client_id',
        'message_client',
        'prix_vente',
        'commission_agence',
        'type_commission',
        'date_vente',
        'date_signature',
        'date_visite',
        'compte_rendu_visite',
        'client_interesse_visite',
        'client_interesse_retour',
        'date_finalisation',
        'statut',
        'note_admin',
    ];

    protected $casts = [
        'date_vente' => 'date',
        'date_signature' => 'date',
        'date_visite' => 'datetime',
        'date_finalisation' => 'datetime',
        'prix_vente' => 'integer',
        'commission_agence' => 'integer',
    ];

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Alias pour client() - pour compatibilité
     */
    public function acheteur()
    {
        return $this->client();
    }

    public function paiements()
    {
        return $this->morphMany(Paiement::class, 'payable');
    }

    /**
     * Montant total à payer (seulement le prix de vente)
     */
    public function montantTotalAPayer()
    {
        return $this->prix_vente;
    }

    /**
     * Montant déjà payé (somme de tous les paiements)
     */
    public function montantTotalPaye()
    {
        return $this->paiements()->where('statut', 'paye')->sum('montant');
    }

    /**
     * Reste à payer
     */
    public function resteAPayer()
    {
        return max(0, $this->montantTotalAPayer() - $this->montantTotalPaye());
    }

    /**
     * Alias de montantTotalPaye() pour compatibilité
     */
    public function montantTotal()
    {
        return $this->montantTotalPaye();
    }

    /**
     * Vérifier si le paiement est complet
     */
    public function estEntierementPaye()
    {
        return $this->resteAPayer() <= 0;
    }

    /**
     * Pourcentage de paiement
     */
    public function pourcentagePaiement()
    {
        if ($this->montantTotalAPayer() == 0) {
            return 0;
        }
        return min(100, ($this->montantTotalPaye() / $this->montantTotalAPayer()) * 100);
    }

    /**
     * Calculer la commission d'agence en FCFA
     */
    public function calculerCommission()
    {
        // Si aucune commission n'est définie, retourner 0
        if (!$this->commission_agence) {
            return 0;
        }
        // Calculer la commission en fonction du type

        if ($this->type_commission === 'pourcentage') {
            return ($this->prix_vente * $this->commission_agence) / 100;
        }
        

        return $this->commission_agence;
    }

    /**
     * Obtenir le total des commissions perçues
     */
    public function totalCommissionsPercues()
    {
        // Pour les ventes, la commission est perçue lorsque la vente est finalisée
        if (in_array($this->statut, ['terminee'])) {
            return $this->calculerCommission();
        }
        return 0;
    }

    /**
     * Obtenir la commission attendue
     */
    public function getCommissionAttendue()
    {
        return $this->calculerCommission();
    }

    /**
     * Obtenir le badge de statut formaté
     */
    public function getStatutBadgeAttribute()
    {
        $badges = [
            'demande_client' => '<span class="badge bg-primary">Nouvelle demande</span>',
            'fiche_envoyee' => '<span class="badge bg-info">Fiche envoyée</span>',
            'retour_prospect' => '<span class="badge bg-secondary">En attente retour</span>',
            'visite_planifiee' => '<span class="badge bg-cyan">Visite planifiée</span>',
            'offre_acceptee' => '<span class="badge bg-warning">Offre acceptée/ En attente paiement</span>',
            'terminee' => '<span class="badge bg-success">Finalisé</span>',
            'annulee' => '<span class="badge bg-danger">Annulé</span>',
        ];

        return $badges[$this->statut] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }

    /**
     * Obtenir la progression en pourcentage
     */
    public function getProgressionAttribute()
    {
        $progressions = [
            'demande_client' => 15,
            'retour_prospect' => 25,
            'fiche_envoyee' => 40,
            'visite_planifiee' => 60,
            'offre_acceptee' => 80,
            'terminee' => 100,
            'annulee' => 0,
        ];

        return $progressions[$this->statut] ?? 0;
    }
}
