<?php

namespace App\Http\Controllers\frontend;

use Str;
use App\Models\User;
use App\Models\Annonce;
use App\Models\TypeBien;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\NouvelleDemandeVente;
use App\Notifications\NouvelleDemandeLocation;

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
        // Validation des champs du formulaire prospect
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'required|string',
        ]);

        $bien = Annonce::where('slug', $slug)->firstOrFail();

        // Vérifier si un prospect avec cet email ou ce téléphone existe déjà
        $user = User::where('email', $request->email)
            ->orWhere('phone', $request->phone)
            ->first();

        if (!$user) {
            // Créer le prospect
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);

            // Assigner le rôle prospect
            $user->assignRole('prospect');
        } else {
            // Mettre à jour les infos si nécessaire
            $user->update([
                'username' => $user->username ?: $request->username,
                'phone' => $user->phone ?: $request->phone,
            ]);
        }

        // Vérifier si ce user a déjà une demande active pour ce bien
        if ($bien->type_transaction == 'vente') {
            $demandeExistante = \App\Models\Vente::where('annonce_id', $bien->id)
                ->where('client_id', $user->id)
                ->whereNotIn('statut', ['annule', 'paiement_valide'])
                ->exists();

            if ($demandeExistante) {
                return back()->with('error', 'Vous avez déjà une demande en cours pour ce bien.');
            }
        } else {
            $demandeExistante = \App\Models\Location::where('annonce_id', $bien->id)
                ->where('locataire_id', $user->id)
                ->whereNotIn('statut', ['annule', 'termine', 'resilie'])
                ->exists();

            if ($demandeExistante) {
                return back()->with('error', 'Vous avez déjà une demande en cours pour ce bien.');
            }
        }

        // Récupérer tous les admins pour les notifier
        $admins = User::role(['superadmin', 'administrateur', 'developpeur'])->get();

        // Créer directement une vente ou location selon le type de transaction
        if ($bien->type_transaction == 'vente') {
            $vente = \App\Models\Vente::create([
                'annonce_id' => $bien->id,
                'client_id' => $user->id,
                'message_client' => $request->message,
                'prix_vente' => $bien->prix,
                'date_vente' => now(),
                'statut' => 'demande_client',
            ]);

            // Charger les relations avant la notification
            $vente->load(['client', 'annonce']);

            // Notifier tous les admins
            foreach ($admins as $admin) {
                $admin->notify(new NouvelleDemandeVente($vente));
            }
        } else {
            $location = \App\Models\Location::create([
                'annonce_id' => $bien->id,
                'locataire_id' => $user->id,
                'message_client' => $request->message,
                'loyer_mensuel' => $bien->prix,
                'date_debut' => now(),
                'statut' => 'demande_client',
            ]);

            // Charger les relations avant la notification
            $location->load(['locataire', 'annonce']);

            // Notifier tous les admins
            foreach ($admins as $admin) {
                $admin->notify(new NouvelleDemandeLocation($location));
            }
        }

        return back()->with('success', 'Votre demande a été envoyée avec succès. Nous vous recontacterons bientôt.');
    }
}
