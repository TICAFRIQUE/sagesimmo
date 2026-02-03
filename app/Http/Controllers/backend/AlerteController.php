<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Echeance;
use App\Models\Location;
use Illuminate\Http\Request;

class AlerteController extends Controller
{
    /**
     * Afficher le dashboard des alertes
     */
    public function index()
    {
        // Échéances impayées (>30 jours)
        $impayees = Echeance::with('location.locataire', 'location.annonce')
            ->impayees()
            ->orderBy('date_echeance', 'asc')
            ->get();
        
        // Échéances en retard (<30 jours)
        $enRetard = Echeance::with('location.locataire', 'location.annonce')
            ->enRetard()
            ->where('statut', '!=', 'impaye')
            ->orderBy('date_echeance', 'asc')
            ->get();
        
        // Échéances à venir (7 prochains jours)
        $aVenir = Echeance::with('location.locataire', 'location.annonce')
            ->aVenir(7)
            ->orderBy('date_echeance', 'asc')
            ->get();
        
        // Statistiques
        $stats = [
            'total_impaye' => $impayees->sum('montant_du') - $impayees->sum('montant_paye'),
            'total_retard' => $enRetard->sum('montant_du') - $enRetard->sum('montant_paye'),
            'nb_impayees' => $impayees->count(),
            'nb_retard' => $enRetard->count(),
            'nb_a_venir' => $aVenir->count(),
        ];
        
        return view('backend.pages.alertes.index', compact('impayees', 'enRetard', 'aVenir', 'stats'));
    }
    
    /**
     * Mettre à jour manuellement tous les statuts
     */
    public function mettreAJourStatuts()
    {
        \Artisan::call('echeances:verifier-retards');
        
        alert()->success('Succès', 'Les statuts des échéances ont été mis à jour');
        return redirect()->back();
    }
}
