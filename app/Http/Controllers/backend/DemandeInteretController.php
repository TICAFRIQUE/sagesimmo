<?php

namespace App\Http\Controllers\backend;

use App\Models\Annonce;
use Illuminate\Http\Request;
use App\Models\DemandeInteret;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class DemandeInteretController extends Controller
{
    /**
     * Afficher la liste des demandes
     */
    public function index(Request $request)
    {
        $query = DemandeInteret::with(['user', 'annonce'])
            ->orderBy('created_at', 'desc');

        // Filtrer par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtrer par annonce
        if ($request->filled('annonce_id')) {
            $query->where('annonce_id', $request->annonce_id);
        }

        $demandes = $query->paginate(15);
        $annonces = Annonce::all();

        return view('backend.pages.demandes.index', compact('demandes', 'annonces'));
    }

    /**
     * Afficher les détails d'une demande
     */
    public function show($id)
    {
        $demande = DemandeInteret::with(['user', 'annonce'])->findOrFail($id);
        return view('backend.pages.demandes.show', compact('demande'));
    }

    /**
     * Planifier une visite
     */
    public function planifierVisite(Request $request, $id)
    {
        $request->validate([
            'date_visite' => 'required|date',
            'note_admin' => 'nullable|string',
        ]);

        $demande = DemandeInteret::findOrFail($id);
        
        Log::info('Planification visite', [
            'demande_id' => $id,
            'ancien_statut' => $demande->statut,
            'date_visite' => $request->date_visite,
            'note_admin' => $request->note_admin,
        ]);
        
        $demande->update([
            'statut' => 'visite_planifiee',
            'date_visite' => $request->date_visite,
            'note_admin' => $request->note_admin,
        ]);

        Log::info('Visite planifiée avec succès', [
            'demande_id' => $id,
            'nouveau_statut' => $demande->fresh()->statut,
        ]);

        return redirect()->route('backend.demandes.show', $id)
                         ->with('success', 'Visite planifiée pour le ' . \Carbon\Carbon::parse($request->date_visite)->format('d/m/Y à H:i'));
    }

    /**
     * Marquer la visite comme effectuée
     */
    public function visiteEffectuee(Request $request, $id)
    {
        $request->validate([
            'compte_rendu_visite' => 'required|string',
            'client_interesse_apres_visite' => 'required|boolean',
        ]);

        $demande = DemandeInteret::findOrFail($id);
        
        // Si client non intéressé, clôturer
        if (!$request->client_interesse_apres_visite) {
            $demande->update([
                'statut' => 'cloture_non_interesse',
                'compte_rendu_visite' => $request->compte_rendu_visite,
                'client_interesse_apres_visite' => false,
                'note_admin' => $request->note_admin,
            ]);
            return back()->with('info', 'Demande clôturée - Client non intéressé après la visite.');
        }

        $demande->update([
            'statut' => 'visite_effectuee',
            'compte_rendu_visite' => $request->compte_rendu_visite,
            'client_interesse_apres_visite' => true,
            'note_admin' => $request->note_admin,
        ]);

        return back()->with('success', 'Visite marquée comme effectuée. Le client est intéressé.');
    }

    /**
     * Demander des pièces au client
     */
    public function demanderPieces(Request $request, $id)
    {
        $request->validate([
            'pieces_demandees' => 'required|string',
        ]);

        $demande = DemandeInteret::findOrFail($id);
        
        $demande->update([
            'pieces_demandees' => $request->pieces_demandees,
            'note_admin' => $request->note_admin,
        ]);

        return back()->with('success', 'Demande de pièces envoyée au client.');
    }

    /**
     * Confirmer réception des documents
     */
    public function documentsRecus(Request $request, $id)
    {
        $demande = DemandeInteret::findOrFail($id);
        
        $demande->update([
            'statut' => 'documents_recus',
            'note_admin' => $request->note_admin,
        ]);

        return back()->with('success', 'Documents marqués comme reçus.');
    }

    /**
     * Valider le dossier
     */
    public function validerDossier(Request $request, $id)
    {
        $demande = DemandeInteret::findOrFail($id);
        
        $demande->update([
            'statut' => 'dossier_valide',
            'note_admin' => $request->note_admin,
        ]);

        return back()->with('success', 'Dossier validé avec succès.');
    }

    /**
     * Refuser le dossier
     */
    public function refuserDossier(Request $request, $id)
    {
        $request->validate([
            'raison_refus_dossier' => 'required|string',
        ]);

        $demande = DemandeInteret::findOrFail($id);
        
        $demande->update([
            'statut' => 'cloture_refus',
            'raison_refus_dossier' => $request->raison_refus_dossier,
            'note_admin' => $request->note_admin,
        ]);

        return back()->with('success', 'Dossier refusé.');
    }

    /**
     * Générer le contrat
     */
    public function genererContrat(Request $request, $id)
    {
        $request->validate([
            'contrat' => 'required|file|mimes:pdf|max:10240',
        ]);

        $demande = DemandeInteret::findOrFail($id);
        
        // Sauvegarder le contrat
        if ($request->hasFile('contrat')) {
            $demande->addMediaFromRequest('contrat')
                ->toMediaCollection('contrat');
        }
        
        $demande->update([
            'statut' => 'contrat_genere',
            'note_admin' => $request->note_admin,
        ]);

        return back()->with('success', 'Contrat généré avec succès. Vous pouvez maintenant créer le suivi de ' . ($demande->annonce->type_transaction == 'location' ? 'location' : 'vente') . '.');
    }

    /**
     * Mettre à jour les notes admin
     */
    public function updateNote(Request $request, $id)
    {
        $demande = DemandeInteret::findOrFail($id);
        
        $demande->update([
            'note_admin' => $request->note_admin,
        ]);

        return back()->with('success', 'Note mise à jour avec succès.');
    }

    /**
     * Supprimer une demande
     */
    public function destroy($id)
    {
        $demande = DemandeInteret::findOrFail($id);
        $demande->delete();

        return back()->with('success', 'La demande a été supprimée.');
    }

    /**
     * Revenir à l'étape précédente
     */
    public function changerStatut(Request $request, $id)
    {
        $request->validate([
            'statut' => 'required|string',
            'motif_refus' => 'nullable|string',
        ]);

        $demande = DemandeInteret::findOrFail($id);
        
        $demande->update([
            'statut' => $request->statut,
            'motif_refus' => $request->motif_refus,
            'note_admin' => $request->note_admin,
        ]);

        return back()->with('success', 'Statut modifié avec succès.');
    }
}
