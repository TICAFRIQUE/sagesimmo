<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'demande_interet_id',
        'annonce_id',
        'locataire_id',
        'loyer_mensuel',
        'nombre_cautions',
        'caution',
        'date_debut',
        'date_fin',
        'statut',
        'jour_paiement',
        'conditions',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'loyer_mensuel' => 'decimal:2',
        'caution' => 'decimal:2',
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

    public function echeances()
    {
        return $this->hasMany(Echeance::class);
    }

    public function montantTotalPaye()
    {
        return $this->paiements()->sum('montant');
    }
}
