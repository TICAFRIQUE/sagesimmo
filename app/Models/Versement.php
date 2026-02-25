<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Versement extends Model
{
    use HasFactory;

    protected $fillable = [
        'proprietaire_id',
        'montant',
        'date_versement',
        'date_debut',
        'date_fin',
        'mode_versement',
        'reference',
        'statut',
        'notes',
    ];

    protected $casts = [
        'date_versement' => 'date',
        'date_debut' => 'date',
        'date_fin' => 'date',
        'montant' => 'integer',
    ];

    // Relations
    public function proprietaire()
    {
        return $this->belongsTo(User::class, 'proprietaire_id');
    }

    // Scopes
    public function scopeEffectues($query)
    {
        return $query->where('statut', 'effectue');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopePourProprietaire($query, $proprietaireId)
    {
        return $query->where('proprietaire_id', $proprietaireId);
    }

    public function scopeParPeriode($query, $dateDebut, $dateFin)
    {
        return $query->where(function ($q) use ($dateDebut, $dateFin) {
            $q->whereBetween('date_debut', [$dateDebut, $dateFin])
              ->orWhereBetween('date_fin', [$dateDebut, $dateFin]);
        });
    }
}
