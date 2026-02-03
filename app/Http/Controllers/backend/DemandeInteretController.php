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
     * Envoyer le contrat par email
     */
    public function envoyerContrat(Request $request, $id)
    {
        $request->validate([
            'contrat' => 'required|file|mimes:pdf|max:10240',
            'note_admin' => 'nullable|string',
        ]);

        $demande = DemandeInteret::findOrFail($id);
        
        // Sauvegarder le contrat
        if ($request->hasFile('contrat')) {
            $demande->addMediaFromRequest('contrat')
                ->toMediaCollection('contrat');
        }
        
        $demande->update([
            'statut' => 'contrat_envoye',
            'note_admin' => $request->note_admin,
        ]);

        // TODO: Envoyer email au client avec le contrat

        return redirect()->route('backend.demandes.show', $id)
                         ->with('success', 'Contrat envoyé au client par email.');
    }

    /**
     * Planifier une visite (après accord client)
     */
    public function planifierVisite(Request $request, $id)
    {
        $request->validate([
            'date_visite' => 'required|date',
            'note_admin' => 'nullable|string',
        ]);

        $demande = DemandeInteret::findOrFail($id);
        
        $demande->update([
            'statut' => 'visite_planifiee',
            'date_visite' => $request->date_visite,
            'note_admin' => $request->note_admin,
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
                'statut' => 'cloture',
                'compte_rendu_visite' => $request->compte_rendu_visite,
                'client_interesse_apres_visite' => false,
                'motif_cloture' => 'Client non intéressé après la visite',
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
     * Configurer le paiement
     */
    public function configurerPaiement(Request $request, $id)
    {
        $request->validate([
            'montant_caution' => 'required|numeric|min:0',
            'montant_loyer_premier' => 'required|numeric|min:0',
            'montant_frais_agence' => 'required|numeric|min:0',
        ]);

        $demande = DemandeInteret::findOrFail($id);
        
        $montantTotal = $request->montant_caution + $request->montant_loyer_premier + $request->montant_frais_agence;
        
        $demande->update([
            'statut' => 'paiement_en_attente',
            'montant_caution' => $request->montant_caution,
            'montant_loyer_premier' => $request->montant_loyer_premier,
            'montant_frais_agence' => $request->montant_frais_agence,
            'montant_total_paiement' => $montantTotal,
            'note_admin' => $request->note_admin,
        ]);

        return back()->with('success', 'Paiement configuré. Montant total : ' . number_format($montantTotal, 0, ',', ' ') . ' FCFA');
    }

    /**
     * Valider le paiement et remettre les clés
     */
    public function validerPaiement(Request $request, $id)
    {
        $request->validate([
            'details_paiement' => 'nullable|string',
        ]);

        $demande = DemandeInteret::findOrFail($id);
        
        $demande->update([
            'statut' => 'paiement_valide',
            'statut_paiement' => 'valide',
            'details_paiement' => ['note' => $request->details_paiement],
            'date_finalisation' => now(),
            'note_admin' => $request->note_admin,
        ]);

        // Marquer le bien comme loué/vendu et le dépublier
        $annonce = $demande->annonce;
        if ($annonce) {
            $statut = $annonce->type_transaction == 'location' ? 'loue' : 'vendu';
            $annonce->update([
                'statut' => $statut,
                'statut_publication' => 0,
            ]);
        }

        return back()->with('success', 'Paiement validé et clés remises. Le bien a été marqué comme ' . ($annonce->type_transaction == 'location' ? 'loué' : 'vendu') . '.');
    }

    /**
     * Clôturer une demande
     */
    public function cloturerDemande(Request $request, $id)
    {
        $request->validate([
            'motif_cloture' => 'required|string',
        ]);

        $demande = DemandeInteret::findOrFail($id);
        
        $demande->update([
            'statut' => 'cloture',
            'motif_cloture' => $request->motif_cloture,
            'note_admin' => $request->note_admin,
        ]);

        return back()->with('success', 'Demande clôturée.');
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
}

