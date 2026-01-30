<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Echeance extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'date_echeance',
        'montant_du',
        'montant_paye',
        'statut',
        'notes',
    ];

    protected $casts = [
        'date_echeance' => 'date',
        'montant_du' => 'decimal:2',
        'montant_paye' => 'decimal:2',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function montantRestant()
    {
        return $this->montant_du - $this->montant_paye;
    }

    public function estEnRetard()
    {
        return $this->statut !== 'payé' && $this->date_echeance < Carbon::now();
    }

    public function updateStatut()
    {
        if ($this->montant_paye >= $this->montant_du) {
            $this->statut = 'payé';
        } elseif ($this->montant_paye > 0) {
            $this->statut = 'partiel';
        } elseif ($this->estEnRetard()) {
            $this->statut = 'en_retard';
        } elseif ($this->date_echeance < Carbon::now()) {
            $this->statut = 'impayé';
        } else {
            $this->statut = 'en_attente';
        }
        $this->save();
    }
}
