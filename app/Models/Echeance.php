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
        'commission_agence',
        'statut',
        'notes',
    ];

    protected $casts = [
        'date_echeance' => 'date',
        'montant_du' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'commission_agence' => 'decimal:2',
    ];

    // Relations
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    // Méthodes
    public function montantRestant()
    {
        return $this->montant_du - $this->montant_paye;
    }

    /**
     * Mettre à jour automatiquement le statut selon la situation
     */
    public function mettreAJourStatut()
    {
        $ancienStatut = $this->statut;
        
        if ($this->montant_paye >= $this->montant_du) {
            $this->statut = 'paye';
        } elseif ($this->montant_paye > 0 && $this->montant_paye < $this->montant_du) {
            // Paiement partiel
            if ($this->date_echeance->isPast()) {
                $this->statut = 'en_retard';
            } else {
                $this->statut = 'partiel';
            }
        } elseif ($this->montant_paye == 0 && $this->date_echeance->isPast()) {
            // Aucun paiement et date dépassée
            $joursRetard = $this->joursDeRetard();
            if ($joursRetard > 30) {
                $this->statut = 'impaye'; // Impayé après 30 jours
            } else {
                $this->statut = 'en_retard';
            }
        } else {
            $this->statut = 'a_echeance';
        }
        
        $this->save();
        
        return $ancienStatut !== $this->statut;
    }

    /**
     * Vérifier si l'échéance est en retard
     */
    public function estEnRetard()
    {
        return in_array($this->statut, ['en_retard', 'impaye']) || 
               ($this->date_echeance->isPast() && $this->montant_paye < $this->montant_du);
    }

    /**
     * Calculer le nombre de jours de retard
     */
    public function joursDeRetard()
    {
        if (!$this->date_echeance->isPast() || $this->statut == 'paye') {
            return 0;
        }
        
        return $this->date_echeance->diffInDays(now());
    }

    /**
     * Obtenir le niveau de priorité (1=urgent, 2=important, 3=normal)
     */
    public function niveauPriorite()
    {
        $jours = $this->joursDeRetard();
        
        if ($jours > 30 || $this->statut == 'impaye') {
            return 1; // Urgent
        } elseif ($jours > 7 || $this->statut == 'en_retard') {
            return 2; // Important
        } elseif ($jours > 0) {
            return 3; // Normal
        }
        
        return 4; // Pas de retard
    }

    /**
     * Badge HTML du statut
     */
    public function getStatutBadgeAttribute()
    {
        $badges = [
            'a_echeance' => '<span class="badge bg-secondary">À échéance</span>',
            'en_retard' => '<span class="badge bg-warning"><i class="ri-alarm-warning-line"></i> En retard (' . $this->joursDeRetard() . 'j)</span>',
            'partiel' => '<span class="badge bg-info">Partiel</span>',
            'paye' => '<span class="badge bg-success"><i class="ri-checkbox-circle-line"></i> Payé</span>',
            'impaye' => '<span class="badge bg-danger"><i class="ri-alert-line"></i> Impayé (' . $this->joursDeRetard() . 'j)</span>',
            'cloture' => '<span class="badge bg-dark">Clôturé</span>',
        ];

        return $badges[$this->statut] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }

    /**
     * Scope pour les échéances en retard
     */
    public function scopeEnRetard($query)
    {
        return $query->whereIn('statut', ['en_retard', 'impaye'])
                    ->orWhere(function($q) {
                        $q->where('date_echeance', '<', now())
                          ->where('statut', '!=', 'paye');
                    });
    }

    /**
     * Scope pour les échéances impayées (>30 jours)
     */
    public function scopeImpayees($query)
    {
        return $query->where('statut', 'impaye')
                    ->orWhere(function($q) {
                        $q->where('date_echeance', '<', now()->subDays(30))
                          ->where('statut', '!=', 'paye');
                    });
    }

    /**
     * Scope pour les échéances à venir (dans les X jours)
     */
    public function scopeAVenir($query, $jours = 7)
    {
        return $query->where('statut', 'a_echeance')
                    ->whereBetween('date_echeance', [now(), now()->addDays($jours)]);
    }
}
