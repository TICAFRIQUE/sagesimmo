<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    use HasFactory;

    protected $fillable = [
        'annonce_id',
        'type_charge',
        'montant',
        'date_charge',
        'description',
        'reference',
        'notes',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_charge' => 'date',
    ];

    /**
     * Relation avec l'annonce
     */
    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    /**
     * Get le propriétaire du bien via l'annonce
     */
    public function proprietaire()
    {
        return $this->annonce->proprietaire();
    }

    /**
     * Obtenir le libellé du type de charge
     */
    public function getTypeChargeLibelleAttribute(): string
    {
        return match ($this->type_charge) {
            'maintenance' => 'Maintenance',
            'reparation' => 'Réparation',
            'taxe' => 'Taxe',
            'autre' => 'Autre',
            default => $this->type_charge,
        };
    }

    /**
     * Scopes de requête
     */
    public function scopeEntreDates($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('date_charge', [$dateDebut, $dateFin]);
    }

    public function scopeParAnnonce($query, $annonceId)
    {
        return $query->where('annonce_id', $annonceId);
    }

    public function scopeParProprietaire($query, $proprietaireId)
    {
        return $query->whereHas('annonce', function ($q) use ($proprietaireId) {
            $q->where('proprietaire_id', $proprietaireId);
        });
    }

    public function scopeParType($query, $type)
    {
        return $query->where('type_charge', $type);
    }
}
