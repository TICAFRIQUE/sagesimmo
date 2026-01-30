<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Equipement extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        'nom',
        'slug',
        'description',
        'icone',
        'actif',
        'ordre',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'nom'
            ]
        ];
    }

    /**
     * Relation avec les annonces
     */
    public function annonces()
    {
        return $this->belongsToMany(Annonce::class, 'annonce_equipement');
    }

    /**
     * Scope pour les équipements actifs
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope pour l'ordre
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('ordre', 'asc')->orderBy('nom', 'asc');
    }
}
