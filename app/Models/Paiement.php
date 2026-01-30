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
        'montant',
        'date_paiement',
        'methode_paiement',
        'reference',
        'notes',
    ];

    protected $casts = [
        'date_paiement' => 'date',
        'montant' => 'decimal:2',
    ];

    public function payable()
    {
        return $this->morphTo();
    }
}
