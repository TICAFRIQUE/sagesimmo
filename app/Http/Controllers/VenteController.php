<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Paiement;
use App\Models\Annonce;
use App\Models\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class VenteController extends Controller
{
    public function index()
    {
        $ventes = Vente::with(['annonce', 'client', 'paiements', 'demandeInteret'])
            ->latest()
            ->paginate(15);
        
        return view('backend.pages.ventes.index', compact('ventes'));
    }

    public function create()
    {
        $annonces = Annonce::where('statut', 'disponible')
            ->where('type_transaction', 'vente')
            ->get();
        // Récupérer tous les utilisateurs sauf les admins
        $clients = User::whereDoesntHave('roles', function($q) {
            $q->where('name', 'superadmin')
              ->orWhere('name', 'developpeur')
              ->orWhere('name', 'admin');
        })->get();
        
        return view('backend.pages.ventes.create', compact('annonces', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'annonce_id' => 'required|exists:annonces,id',
            'client_id' => 'required|exists:users,id',
            'message_client' => 'nullable|string',
            'prix_vente' => 'required|numeric|min:0',
            'montant_caution' => 'nullable|numeric|min:0',
            'montant_frais_agence' => 'nullable|numeric|min:0',
            'commission_agence' => 'nullable|numeric|min:0',
            'type_commission' => 'nullable|in:montant,pourcentage',
            'date_vente' => 'required|date',
            'date_signature' => 'nullable|date',
            'statut' => 'nullable|in:demande_client,fiche_envoyee,visite_planifiee,en_attente_paiement,paiement_valide,annule',
            'notes' => 'nullable|string',
        ]);

        $validated['statut'] = $validated['statut'] ?? 'demande_client';
        $vente = Vente::create($validated);

        // Mettre à jour le statut de l'annonce seulement si paiement validé
        if ($validated['statut'] == 'paiement_valide') {
            $annonce = Annonce::find($validated['annonce_id']);
            $annonce->update(['statut' => 'vendu', 'statut_publication' => 0]);
        }

        Alert::success('Succès', 'Vente enregistrée avec succès');
        return redirect()->route('backend.ventes.show', $vente);
    }

    public function show(Vente $vente)
    {
        $vente->load(['annonce', 'client', 'paiements']);
        return view('backend.pages.ventes.show', compact('vente'));
    }

    public function edit(Vente $vente)
    {
        $annonces = Annonce::all();
        $clients = User::whereDoesntHave('roles', function($q) {
            $q->where('name', 'superadmin')
              ->orWhere('name', 'developpeur')
              ->orWhere('name', 'admin');
        })->get();
        
        return view('backend.pages.ventes.edit', compact('vente', 'annonces', 'clients'));
    }

    public function update(Request $request, Vente $vente)
    {
        $validated = $request->validate([
            'annonce_id' => 'required|exists:annonces,id',
            'client_id' => 'required|exists:users,id',
            'prix_vente' => 'required|numeric|min:0',
            'montant_caution' => 'nullable|numeric|min:0',
            'montant_frais_agence' => 'nullable|numeric|min:0',
            'commission_agence' => 'nullable|numeric|min:0',
            'type_commission' => 'nullable|in:montant,pourcentage',
            'date_vente' => 'required|date',
            'date_signature' => 'nullable|date',
            'statut' => 'required|in:demande_client,fiche_envoyee,visite_planifiee,en_attente_paiement,paiement_valide,annule',
            'notes' => 'nullable|string',
        ]);

        $vente->update($validated);

        Alert::success('Succès', 'Vente modifiée avec succès');
        return redirect()->route('backend.ventes.show', $vente);
    }

    public function destroy(Vente $vente)
    {
        $annonce = $vente->annonce;
        $annonce->update(['statut' => 'disponible']);
        
        $vente->delete();

        Alert::success('Succès', 'Vente supprimée avec succès');
        return redirect()->route('backend.ventes.index');
    }

    // === ACTIONS DU WORKFLOW ===

    /**
     * Envoyer la fiche au client
     */
    public function envoyerFiche(Request $request, Vente $vente)
    {
        $request->validate([
            'note_admin' => 'nullable|string',
            'message_email' => 'nullable|string',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // Max 10MB par fichier
        ]);

        $vente->update([
            'statut' => 'fiche_envoyee',
            'note_admin' => $request->note_admin,
        ]);

        // Gestion des documents uploadés
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $vente->addMedia($document)
                    ->toMediaCollection('documents_fiche');
            }
        }

        // TODO: Envoyer email avec fiche à remplir et documents
        // Utiliser $request->message_email pour le message personnalisé
        // Inclure les documents uploadés en pièces jointes

        Alert::success('Succès', 'Fiche envoyée au client par email avec ' . ($request->hasFile('documents') ? count($request->file('documents')) : 0) . ' document(s)');
        return back();
    }

    /**
     * Planifier une visite
     */
    public function planifierVisite(Request $request, Vente $vente)
    {
        $request->validate([
            'date_visite' => 'required|date',
            'note_admin' => 'nullable|string',
        ]);

        $vente->update([
            'statut' => 'visite_planifiee',
            'date_visite' => $request->date_visite,
            'note_admin' => $request->note_admin,
        ]);

        Alert::success('Succès', 'Visite planifiée pour le ' . \Carbon\Carbon::parse($request->date_visite)->format('d/m/Y à H:i'));
        return back();
    }

    /**
     * Marquer la visite comme effectuée
     */
    public function visiteEffectuee(Request $request, Vente $vente)
    {
        $request->validate([
            'compte_rendu_visite' => 'required|string',
            'client_interesse' => 'required|boolean',
            'note_admin' => 'nullable|string',
        ]);

        if (!$request->client_interesse) {
            $vente->update([
                'statut' => 'annule',
                'compte_rendu_visite' => $request->compte_rendu_visite,
                'note_admin' => $request->note_admin,
            ]);
            Alert::info('Info', 'Vente annulée - Client non intéressé après la visite');
            return back();
        }

        $vente->update([
            'statut' => 'en_attente_paiement',
            'compte_rendu_visite' => $request->compte_rendu_visite,
            'note_admin' => $request->note_admin,
        ]);

        Alert::success('Succès', 'Visite effectuée - Client intéressé. Vous pouvez maintenant configurer le paiement.');
        return back();
    }

    /**
     * Configurer le paiement
     */
    public function configurerPaiement(Request $request, Vente $vente)
    {
        $request->validate([
            'prix_vente' => 'required|numeric|min:0',
            'montant_caution' => 'nullable|numeric|min:0',
            'montant_frais_agence' => 'nullable|numeric|min:0',
            'commission_agence' => 'nullable|numeric|min:0',
            'type_commission' => 'nullable|in:montant,pourcentage',
            'note_admin' => 'nullable|string',
        ]);

        // Récupérer la commission depuis l'annonce si non fournie
        $commissionAgence = $request->commission_agence;
        $typeCommission = $request->type_commission;
        
        if (empty($commissionAgence) && $vente->annonce->commission) {
            $commissionAgence = $vente->annonce->commission;
            $typeCommission = $vente->annonce->type_commission;
        }

        $vente->update([
            'statut' => 'en_attente_paiement',
            'prix_vente' => $request->prix_vente,
            'montant_caution' => $request->montant_caution ?? 0,
            'montant_frais_agence' => $request->montant_frais_agence ?? 0,
            'commission_agence' => $commissionAgence ?? 0,
            'type_commission' => $typeCommission,
            'note_admin' => $request->note_admin,
        ]);

        $montantTotal = $request->prix_vente + ($request->montant_caution ?? 0) + ($request->montant_frais_agence ?? 0);
        Alert::success('Succès', 'Paiement configuré. Le client doit payer : ' . number_format($montantTotal, 0, ',', ' ') . ' FCFA. Les paiements peuvent être effectués en plusieurs fois.');
        return back();
    }

    /**
     * Valider le paiement et finaliser
     */
    public function validerPaiement(Request $request, Vente $vente)
    {
        $request->validate([
            'date_signature' => 'nullable|date',
            'note_admin' => 'nullable|string',
        ]);

        // Vérifier que le paiement est complet
        $resteAPayer = $vente->resteAPayer();
        
        if ($resteAPayer > 0) {
            Alert::error('Erreur', 'Le paiement n\'est pas complet. Il reste ' . number_format($resteAPayer, 0, ',', ' ') . ' FCFA à payer. Ajoutez les paiements manquants avant de finaliser.');
            return back();
        }

        $vente->update([
            'statut' => 'paiement_valide',
            'date_signature' => $request->date_signature ?? now(),
            'date_finalisation' => now(),
            'note_admin' => $request->note_admin,
        ]);

        // Marquer le bien comme vendu et le dépublier
        $vente->annonce->update([
            'statut' => 'vendu',
            'statut_publication' => 0,
        ]);

        Alert::success('Succès', 'Paiement complet validé ! Vente finalisée, remise des clés au client. Le bien a été marqué comme vendu.');
        return back();
    }

    /**
     * Annuler la vente
     */
    public function annulerVente(Request $request, Vente $vente)
    {
        $request->validate([
            'note_admin' => 'required|string',
        ]);

        $vente->update([
            'statut' => 'annule',
            'note_admin' => $request->note_admin,
        ]);

        Alert::success('Succès', 'Vente annulée');
        return back();
    }

    // === FIN ACTIONS DU WORKFLOW ===

    public function addPaiement(Request $request, Vente $vente)
    {
        $validated = $request->validate([
            'montant' => 'required|numeric|min:0',
            'date_paiement' => 'required|date',
            'methode_paiement' => 'required|in:espèces,virement,chèque,carte_bancaire,autre',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Vérifier que le montant ne dépasse pas le montant restant à payer
        $montantRestant = $vente->resteAPayer();
        
        if ($validated['montant'] > $montantRestant) {
            Alert::error('Erreur', 'Le montant du paiement (' . number_format($validated['montant'], 0, ',', ' ') . ' FCFA) dépasse le montant restant à payer (' . number_format($montantRestant, 0, ',', ' ') . ' FCFA)');
            return redirect()->back()->withInput();
        }

        $vente->paiements()->create($validated);

        Alert::success('Succès', 'Paiement ajouté avec succès');
        return redirect()->route('backend.ventes.show', $vente);
    }
    public function createFromDemande($demandeId)
    {
        $demande = \App\Models\DemandeInteret::with(['annonce', 'user'])->findOrFail($demandeId);
        
        // Vérifier si une vente existe déjà pour cette demande
        if ($demande->vente) {
            Alert::warning('Attention', 'Une vente existe déjà pour cette demande');
            return redirect()->route('backend.ventes.show', $demande->vente);
        }

        return view('backend.pages.ventes.create-from-demande', compact('demande'));
    }

    public function storeFromDemande(Request $request, $demandeId)
    {
        $demande = \App\Models\DemandeInteret::findOrFail($demandeId);
        
        $validated = $request->validate([
            'prix_vente' => 'required|numeric|min:0',
            'commission_agence' => 'nullable|numeric|min:0',
            'type_commission' => 'nullable|in:montant,pourcentage',
            'date_vente' => 'required|date',
            'date_signature' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['demande_interet_id'] = $demande->id;
        $validated['annonce_id'] = $demande->annonce_id;
        $validated['client_id'] = $demande->user_id;
        $validated['statut'] = 'en_cours';

        $vente = Vente::create($validated);

        // Mettre à jour le statut de l'annonce
        $demande->annonce->update(['statut' => 'vendu']);
        
        // Mettre à jour le statut de la demande
        $demande->update(['statut' => 'paiement_valide']);

        Alert::success('Succès', 'Vente créée avec succès depuis la demande');
        return redirect()->route('backend.ventes.show', $vente);
    }}
