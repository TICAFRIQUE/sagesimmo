<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    use HasFactory;

    protected $fillable = [
        'demande_interet_id',
        'annonce_id',
        'client_id',
        'prix_vente',
        'commission_agence',
        'type_commission',
        'date_vente',
        'date_signature',
        'statut',
        'notes',
    ];

    protected $casts = [
        'date_vente' => 'date',
        'date_signature' => 'date',
        'prix_vente' => 'decimal:2',
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
}
