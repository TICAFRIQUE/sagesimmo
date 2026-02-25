<?php

namespace App\Http\Controllers;

use App\Models\Versement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VersementController extends Controller
{
    /**
     * Lister tous les versements
     */
    public function index(Request $request)
    {
        // Seul les administrateurs peuvent voir les versements
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $query = Versement::with('proprietaire');

        // Filtres
        if ($request->filled('proprietaire_id')) {
            $query->where('proprietaire_id', $request->input('proprietaire_id'));
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_versement', [
                $request->input('date_debut'),
                $request->input('date_fin')
            ]);
        }

        $versements = $query->orderBy('date_versement', 'desc')->paginate(20);
        $proprietaires = User::where('role', 'proprietaire')->get();

        return view('backend.pages.versements.index', compact('versements', 'proprietaires'));
    }

    /**
     * Créer un nouveau versement
     */
    public function create(Request $request)
    {
        // Seul les administrateurs peuvent créer des versements
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $proprietaires = User::where('role', 'proprietaire')->get();
        $proprietairePreselecte = $request->input('proprietaire_id');
        $montantAVerser = null;

        // Si un propriétaire est pré-sélectionné, calculer le montant à verser pour le mois en cours
        if ($proprietairePreselecte) {
            $proprietaire = User::find($proprietairePreselecte);
            if ($proprietaire) {
                $service = new \App\Services\RapportProprietaireService();
                $rapport = $service->genererRapport($proprietaire, now()->startOfMonth(), now()->endOfMonth());
                $montantAVerser = $rapport['revenue_net'] ?? 0;
            }
        }

        return view('backend.pages.versements.create', compact('proprietaires', 'proprietairePreselecte', 'montantAVerser'));
    }

    /**
     * Enregistrer un nouveau versement
     */
    public function store(Request $request)
    {
        // Seul les administrateurs peuvent créer des versements
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return response()->json(['success' => false, 'message' => 'Accès refusé'], 403);
        }

        $validated = $request->validate([
            'proprietaire_id' => 'required|exists:users,id',
            'montant' => 'required|numeric|min:0',
            'date_versement' => 'required|date',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
            'mode_versement' => 'required|in:virement,chèque,espèces,mobile_money,autre',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Convertir en centimes (entiers)
        $validated['montant'] = intval($validated['montant']);

        // Le statut est toujours "effectue" à la création - c'est un paiement reçu
        $validated['statut'] = 'effectue';

        try {
            Versement::create($validated);

            // Si c'est une requête AJAX, retourner JSON
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => true, 'message' => 'Versement enregistré avec succès']);
            }

            return redirect()->route('backend.versements.index')
                ->with('success', 'Versement enregistré avec succès');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Erreur lors de l\'enregistrement du versement');
        }
    }

    /**
     * Éditer un versement
     */
    public function edit(Versement $versement)
    {
        // Seul les administrateurs peuvent éditer les versements
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $proprietaires = User::where('role', 'proprietaire')->get();

        // Calculer le montant à verser pour le propriétaire et la période
        $montantAVerser = 0;
        if ($versement->proprietaire && $versement->date_debut && $versement->date_fin) {
            $service = new \App\Services\RapportProprietaireService();
            $rapport = $service->genererRapport($versement->proprietaire, $versement->date_debut, $versement->date_fin);
            $montantAVerser = $rapport['revenue_net'] ?? 0;
        }

        return view('backend.pages.versements.edit', compact('versement', 'proprietaires', 'montantAVerser'));
    }

    /**
     * Mettre à jour un versement
     */
    public function update(Request $request, Versement $versement)
    {
        // Seul les administrateurs peuvent mettre à jour les versements
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $validated = $request->validate([
            'proprietaire_id' => 'required|exists:users,id',
            'montant' => 'required|numeric|min:0',
            'date_versement' => 'required|date',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
            'mode_versement' => 'required|in:virement,chèque,espèces,mobile_money,autre',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Convertir en centimes (entiers)
        $validated['montant'] = intval($validated['montant']);

        // Garder le statut "effectue" pour les paiements reçus
        $validated['statut'] = 'effectue';

        $versement->update($validated);

        return redirect()->route('backend.versements.index')
            ->with('success', 'Versement mis à jour avec succès');
    }

    /**
     * Annuler un versement (changer le statut à "annule")
     */
    public function cancel(Versement $versement)
    {
        // Seul les administrateurs peuvent annuler les versements
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $versement->update(['statut' => 'annule']);

        // Si c'est une requête AJAX, retourner JSON
        if (request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true, 'message' => 'Versement annulé avec succès']);
        }

        return redirect()->back()->with('success', 'Versement annulé avec succès');
    }

    /**
     * Supprimer un versement
     */
    public function destroy(Versement $versement)
    {
        // Seul les administrateurs peuvent supprimer les versements
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $versement->delete();

        return redirect()->route('backend.versements.index')
            ->with('success', 'Versement supprimé avec succès');
    }
}
