<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\DemandeInteret;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardClientController extends Controller
{
    /**
     * Afficher le dashboard du client
     */
    public function index()
    {
        $user = Auth::user();
        
        // Statistiques
        $totalDemandes = DemandeInteret::where('user_id', $user->id)->count();
        $demandesEnCours = DemandeInteret::where('user_id', $user->id)
            ->whereNotIn('statut', ['cloture_refus', 'cloture_non_interesse', 'paiement_valide'])
            ->count();
        $demandesFinalisees = DemandeInteret::where('user_id', $user->id)
            ->where('statut', 'paiement_valide')
            ->count();
        $demandesVisites = DemandeInteret::where('user_id', $user->id)
            ->where('statut', 'visite_planifiee')
            ->count();
        
        // Dernières demandes
        $dernieresDemandes = DemandeInteret::with('annonce')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Prochaines visites
        $prochainesVisites = DemandeInteret::with('annonce')
            ->where('user_id', $user->id)
            ->where('statut', 'visite_planifiee')
            ->whereNotNull('date_visite')
            ->where('date_visite', '>=', now())
            ->orderBy('date_visite', 'asc')
            ->get();
        
        return view('frontend.pages.client.dashboard', compact(
            'totalDemandes',
            'demandesEnCours',
            'demandesFinalisees',
            'demandesVisites',
            'dernieresDemandes',
            'prochainesVisites'
        ));
    }

    /**
     * Afficher le profil du client
     */
    public function profil()
    {
        $user = Auth::user();
        return view('frontend.pages.client.profil', compact('user'));
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfil(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);
        
        $user->update([
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);
        
        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Changer le mot de passe
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = User::findOrFail(Auth::id());
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }
        
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        
        return back()->with('success', 'Mot de passe modifié avec succès.');
    }

    /**
     * Afficher la liste des demandes du client
     */
    public function demandes(Request $request)
    {
        $query = DemandeInteret::with('annonce')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');
        
        // Filtrer par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        $demandes = $query->paginate(10);
        
        return view('frontend.pages.client.demandes.index', compact('demandes'));
    }

    /**
     * Afficher les détails d'une demande
     */
    public function showDemande($id)
    {
        $demande = DemandeInteret::with('annonce')
            ->where('user_id', Auth::id())
            ->findOrFail($id);
        
        return view('frontend.pages.client.demandes.show', compact('demande'));
    }

    /**
     * Annuler une demande
     */
    public function cancelDemande($id)
    {
        $demande = DemandeInteret::where('user_id', Auth::id())
            ->where('statut', 'nouvelle')
            ->findOrFail($id);
        
        $demande->delete();
        
        return redirect()->route('client.demandes')->with('success', 'Demande annulée avec succès.');
    }

    /**
     * Uploader des documents
     */
    public function uploadDocuments(Request $request, $id)
    {
        $request->validate([
            'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $demande = DemandeInteret::where('user_id', Auth::id())->findOrFail($id);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $demande->addMedia($document)
                    ->toMediaCollection('documents_client');
            }
        }

        // Mettre à jour le statut si nécessaire
        if ($demande->statut == 'visite_effectuee') {
            $demande->update(['statut' => 'documents_recus']);
        }

        return back()->with('success', 'Documents envoyés avec succès.');
    }
}
