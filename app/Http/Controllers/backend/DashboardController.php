<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Annonce;
use App\Models\Location;
use App\Models\Vente;
use App\Models\User;
use App\Models\Echeance;
use App\Models\Paiement;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
    * Afficher le tableau de bord
    */
    public function index(Request $request)
    {
        // KPIs Principaux
        $totalBiens = Annonce::count();
        $biensDisponibles = Annonce::where('statut', 'disponible')->count();
        $biensLoues = Annonce::where('statut', 'loue')->count();
        $biensVendus = Annonce::where('statut', 'vendu')->count();
        
        // Taux d'occupation
        $tauxOccupation = $totalBiens > 0 ? (($biensLoues + $biensVendus) / $totalBiens) * 100 : 0;
        
        // Locations
        $locationsActives = Location::where('statut', 'actif')->count();
        $locationsEnAttente = Location::where('statut', 'en_attente_paiement')->count();
        $nouvellesDemandesLocation = Location::where('statut', 'demande_client')->count();
        
        // Loyers mensuels totaux
        $loyersMensuels = Location::where('statut', 'actif')
            ->sum('loyer_mensuel');
        
        // Échéances en retard
        $echeancesEnRetard = Echeance::whereIn('statut', ['en_retard', 'impaye'])
            ->whereHas('location', function($q) {
                $q->where('statut', 'actif');
            })
            ->get();
        $nombreEcheancesEnRetard = $echeancesEnRetard->count();
        $montantImpaye = $echeancesEnRetard->sum(function($e) {
            return $e->montant_du - $e->montant_paye;
        });
        
        // Échéances du mois en cours
        $debutMois = Carbon::now()->startOfMonth();
        $finMois = Carbon::now()->endOfMonth();
        $echeancesMois = Echeance::whereBetween('date_echeance', [$debutMois, $finMois])
            ->whereHas('location', function($q) {
                $q->where('statut', 'actif');
            })
            ->get();
        $echeancesMoisPayees = $echeancesMois->where('statut', 'paye')->count();
        $echeancesMoisTotal = $echeancesMois->count();
        
        // Locations arrivant à expiration (30 jours)
        $locationsExpiration = Location::where('statut', 'actif')
            ->whereNotNull('date_fin')
            ->whereBetween('date_fin', [Carbon::now(), Carbon::now()->addDays(30)])
            ->count();
        
        // Ventes
        $ventesEnCours = Vente::whereIn('statut', ['demande_client', 'fiche_envoyee', 'visite_planifiee', 'offre_acceptee'])->count();
        $ventesTerminees = Vente::where('statut', 'terminee')->count();
        $nouvellesDemandesVente = Vente::where('statut', 'demande_client')->count();
        $ventesEnAttentePaiement = Vente::where('statut', 'offre_acceptee')
            ->whereHas('paiements', function($q) {
                // Ventes avec paiements mais pas complètement payées
            }, '<', 1)
            ->count();
        
        // Ventes finalisées ce mois
        $ventesFinaliseesMois = Vente::where('statut', 'terminee')
            ->whereBetween('date_finalisation', [$debutMois, $finMois])
            ->count();
        
        // Valeur totale des ventes en cours
        $valeurVentesEnCours = Vente::whereIn('statut', ['offre_acceptee', 'en_cours'])
            ->with('annonce')
            ->get()
            ->sum(function($vente) {
                return $vente->annonce->prix ?? 0;
            });
        
        // Clients
        $totalClients = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['locataire', 'acheteur', 'proprietaire']);
        })->count();
        
        $totalProprietaires = User::whereHas('roles', function($q) {
            $q->where('name', 'proprietaire');
        })->count();
        
        $nouveauxClientsMois = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['locataire', 'acheteur', 'proprietaire']);
        })->whereBetween('created_at', [$debutMois, $finMois])->count();
        
        // Revenus du mois
        $paiementsMois = Paiement::whereBetween('date_paiement', [$debutMois, $finMois])->get();
        $loyersPercus = $paiementsMois->where('type_paiement', 'loyer')->sum('montant');
        $cautionsPercues = $paiementsMois->where('type_paiement', 'caution')->sum('montant');
        $ventesRealisees = $paiementsMois->whereIn('type_paiement', ['vente', 'acompte'])->sum('montant');
        $commissionsAgence = $paiementsMois->sum('commission_agence');
        $revenusTotalMois = $paiementsMois->sum('montant');
        
        // Revenus mois précédent pour comparaison
        $debutMoisPrecedent = Carbon::now()->subMonth()->startOfMonth();
        $finMoisPrecedent = Carbon::now()->subMonth()->endOfMonth();
        $revenusMoisPrecedent = Paiement::whereBetween('date_paiement', [$debutMoisPrecedent, $finMoisPrecedent])
            ->sum('montant');
        $evolutionRevenus = $revenusMoisPrecedent > 0 
            ? (($revenusTotalMois - $revenusMoisPrecedent) / $revenusMoisPrecedent) * 100 
            : 0;
        
        // Visites planifiées cette semaine
        $debutSemaine = Carbon::now()->startOfWeek();
        $finSemaine = Carbon::now()->endOfWeek();
        $visitesLocations = Location::where('statut', 'visite_planifiee')
            ->whereNotNull('date_visite')
            ->whereBetween('date_visite', [$debutSemaine, $finSemaine])
            ->count();
        $visitesVentes = Vente::where('statut', 'visite_planifiee')
            ->whereNotNull('date_visite')
            ->whereBetween('date_visite', [$debutSemaine, $finSemaine])
            ->count();
        $visitesTotal = $visitesLocations + $visitesVentes;
        
        // Notifications non lues
        $notificationsNonLues = auth()->user()->unreadNotifications->count();
        
        // Dernières locations actives
        $dernieresLocations = Location::whereIn('statut', ['actif', 'en_attente_paiement'])
            ->with(['annonce', 'locataire'])
            ->latest()
            ->take(5)
            ->get();
        
        // Dernières ventes
        $dernieresVentes = Vente::whereIn('statut', ['offre_acceptee', 'terminee'])
            ->with(['annonce', 'client'])
            ->latest()
            ->take(5)
            ->get();
        
        // Prochaines échéances (7 jours)
        $prochainesEcheances = Echeance::where('statut', '!=', 'paye')
            ->whereBetween('date_echeance', [Carbon::now(), Carbon::now()->addDays(7)])
            ->with(['location.annonce', 'location.locataire'])
            ->orderBy('date_echeance')
            ->take(10)
            ->get();
        
        return view('backend.pages.index', compact(
            'totalBiens', 'biensDisponibles', 'biensLoues', 'biensVendus', 'tauxOccupation',
            'locationsActives', 'locationsEnAttente', 'nouvellesDemandesLocation', 'loyersMensuels',
            'nombreEcheancesEnRetard', 'montantImpaye', 'echeancesMoisPayees', 'echeancesMoisTotal',
            'locationsExpiration', 'ventesEnCours', 'ventesTerminees', 'nouvellesDemandesVente',
            'ventesEnAttentePaiement', 'ventesFinaliseesMois', 'valeurVentesEnCours',
            'totalClients', 'totalProprietaires', 'nouveauxClientsMois',
            'loyersPercus', 'cautionsPercues', 'ventesRealisees', 'commissionsAgence', 
            'revenusTotalMois', 'evolutionRevenus', 'visitesTotal', 'notificationsNonLues',
            'dernieresLocations', 'dernieresVentes', 'prochainesEcheances'
        ));
    }
}
