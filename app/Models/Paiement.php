<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'payable_type',
        'payable_id',
        'echeance_id',
        'type_paiement',
        'statut',
        'montant',
        'commission_agence',
        'type_commission',
        'date_paiement',
        'methode_paiement',
        'reference',
        'notes',
    ];

    protected $casts = [
        'date_paiement' => 'date',
        'montant' => 'decimal:2',
        'commission_agence' => 'decimal:2',
    ];

    // Relations
    public function payable()
    {
        return $this->morphTo();
    }

    public function echeance()
    {
        return $this->belongsTo(Echeance::class);
    }

    /**
     * Relation helper pour récupérer la location si payable_type est Location
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'payable_id')
            ->where('payable_type', Location::class);
    }

    /**
     * Relation helper pour récupérer la vente si payable_type est Vente
     */
    public function vente()
    {
        return $this->belongsTo(Vente::class, 'payable_id')
            ->where('payable_type', Vente::class);
    }

    // Scopes
    public function scopeLoyers($query)
    {
        return $query->where('type_paiement', 'loyer');
    }

    public function scopeCaution($query)
    {
        return $query->where('type_paiement', 'caution');
    }

    public function scopeAvance($query)
    {
        return $query->where('type_paiement', 'avance');
    }

    public function scopeFraisAgence($query)
    {
        return $query->where('type_paiement', 'frais_agence');
    }

    public function scopePaye($query)
    {
        return $query->where('statut', 'paye');
    }

    /**
     * Calculer le montant réel de la commission en FCFA
     * Si type_commission est "pourcentage", calcule le montant basé sur le montant du paiement
     * Sinon, retourne directement la commission_agence
     */
    public function getMontantCommissionAttribute()
    {
        if ($this->type_commission === 'pourcentage') {
            return ($this->montant * $this->commission_agence) / 100;
        }
        
        return $this->commission_agence ?? 0;
    }

    /**
     * Obtenir le libellé formaté de la commission
     */
    public function getCommissionFormatteeAttribute()
    {
        if (!$this->commission_agence) {
            return '-';
        }

        if ($this->type_commission === 'pourcentage') {
            return number_format($this->commission_agence, 0, ',', ' ') . '% (' . 
                   number_format($this->montant_commission, 0, ',', ' ') . ' FCFA)';
        }

        return number_format($this->commission_agence, 0, ',', ' ') . ' FCFA';
    }}