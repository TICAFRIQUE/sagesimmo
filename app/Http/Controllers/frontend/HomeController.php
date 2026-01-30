<?php

namespace App\Http\Controllers\frontend;

use App\Models\Annonce;
use App\Models\TypeBien;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Afficher la page d'accueil
     */
    public function index()
    {
        // Récupérer les biens à la une (les plus récents)
        $biensRecents = Annonce::with(['typeBien', 'media'])
            ->where('statut', 'disponible')
            ->latest()
            ->take(6)
            ->get();

        // Biens en location
        $biensLocation = Annonce::with(['typeBien', 'media'])
            ->where('statut', 'disponible')
            ->where('type_transaction', 'location')
            ->latest()
            ->take(3)
            ->get();

        // Biens en vente
        $biensVente = Annonce::with(['typeBien', 'media'])
            ->where('statut', 'disponible')
            ->where('type_transaction', 'vente')
            ->latest()
            ->take(3)
            ->get();

        // Statistiques
        $stats = [
            'total_biens' => Annonce::where('statut', 'disponible')->count(),
            'biens_location' => Annonce::where('statut', 'disponible')->where('type_transaction', 'location')->count(),
            'biens_vente' => Annonce::where('statut', 'disponible')->where('type_transaction', 'vente')->count(),
            'types_biens' => TypeBien::count(),
        ];

        // Types de biens pour le filtre
        $typesBiens = TypeBien::all();
        
        // Villes et communes
        $villes = config('ville-commune');

        return view('frontend.pages.home', compact(
            'biensRecents',
            'biensLocation',
            'biensVente',
            'stats',
            'typesBiens',
            'villes'
        ));
    }

    /**
     * Recherche rapide depuis la bannière
     */
    public function search(Request $request)
    {
        $query = Annonce::with(['typeBien', 'media'])
            ->where('statut', 'disponible');

        // Filtre par type d'annonce (location/vente)
        if ($request->filled('type_annonce')) {
            $query->where('type_transaction', $request->type_annonce);
        }

        // Filtre par type de bien
        if ($request->filled('type_bien_id')) {
            $query->where('type_bien_id', $request->type_bien_id);
        }

        // Filtre par ville
        if ($request->filled('ville')) {
            $query->where('ville', $request->ville);
        }

        // Filtre par commune
        if ($request->filled('commune')) {
            $query->where('quartier', $request->commune);
        }

        // Filtre par prix
        if ($request->filled('prix_min')) {
            $query->where('prix', '>=', $request->prix_min);
        }
        if ($request->filled('prix_max')) {
            $query->where('prix', '<=', $request->prix_max);
        }

        // Filtre par nombre de chambres
        if ($request->filled('chambres')) {
            $query->where('nombre_chambres', '>=', $request->chambres);
        }

        $biens = $query->latest()->paginate(12)->withQueryString();
        $typesBiens = TypeBien::all();
        $villes = config('ville-commune');

        // Stats pour les filtres
        $stats = [
            'total' => $query->count(),
            'location' => Annonce::where('statut', 'disponible')->where('type_transaction', 'location')->count(),
            'vente' => Annonce::where('statut', 'disponible')->where('type_transaction', 'vente')->count(),
        ];

        return view('frontend.pages.properties.index', compact('biens', 'typesBiens', 'villes', 'stats'));
    }
}
