<?php

namespace App\Http\Controllers\frontend;

use App\Models\Annonce;
use App\Models\TypeBien;
use App\Models\DemandeInteret;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    /**
     * Afficher la liste des biens avec filtres
     */
    public function index(Request $request)
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

        // Filtre par superficie
        if ($request->filled('superficie_min')) {
            $query->where('surface', '>=', $request->superficie_min);
        }
        if ($request->filled('superficie_max')) {
            $query->where('surface', '<=', $request->superficie_max);
        }

        // Filtre par nombre de chambres
        if ($request->filled('chambres')) {
            $query->where('nombre_chambres', '>=', $request->chambres);
        }

        // Filtre par nombre de salles de bain
        if ($request->filled('salles_bain')) {
            $query->where('nombre_salles_bain', '>=', $request->salles_bain);
        }

        // Tri
        $sort = $request->get('sort', 'recent');
        switch ($sort) {
            case 'prix_asc':
                $query->orderBy('prix', 'asc');
                break;
            case 'prix_desc':
                $query->orderBy('prix', 'desc');
                break;
            case 'superficie_desc':
                $query->orderBy('surface', 'desc');
                break;
            default:
                $query->latest();
        }

        $biens = $query->paginate(12)->withQueryString();
        $typesBiens = TypeBien::all();
        $villes = config('ville-commune');

        // Stats pour les filtres
        $stats = [
            'total' => $query->count(),
            'location' => Annonce::where('statut', 'disponible')->where('type_transaction', 'location')->count(),
            'vente' => Annonce::where('statut', 'disponible')->where('type_transaction', 'vente')->count(),
        ];

        return view('frontend.pages.properties.index', compact('biens', 'typesBiens', 'stats', 'villes'));
    }

    /**
     * Afficher les détails d'un bien
     */
    public function show($slug)
    {
        $bien = Annonce::with(['typeBien', 'equipements', 'media', 'user'])
            ->where('slug', $slug)
            ->where('statut', 'disponible')
            ->firstOrFail();

        // Incrémenter les vues
        $bien->increment('nombre_vues');

        // Biens similaires
        $biensSimilaires = Annonce::with(['typeBien', 'media'])
            ->where('statut', 'disponible')
            ->where('slug', '!=', $bien->slug)
            ->where('type_transaction', $bien->type_transaction)
            ->where('type_bien_id', $bien->type_bien_id)
            ->latest()
            ->take(3)
            ->get();

        return view('frontend.pages.properties.show', compact('bien', 'biensSimilaires'));
    }

    /**
     * Soumettre une demande de contact pour un bien
     */
    public function contact(Request $request, $slug)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $bien = Annonce::where('slug', $slug)->firstOrFail();

        // Récupérer les informations de l'utilisateur connecté
        $user = Auth::user();
        
        // Sauvegarder la demande dans la base de données
        DemandeInteret::create([
            'user_id' => $user->id,
            'annonce_id' => $bien->id,
            'message' => $request->message,
            'statut' => 'nouvelle',
        ]);

        return back()->with('success', 'Votre demande a été envoyée avec succès. Nous vous recontacterons bientôt.');
    }
}
