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
            'prix_vente' => 'required|numeric|min:0',
            'commission_agence' => 'nullable|numeric|min:0',
            'type_commission' => 'nullable|in:fixe,pourcentage',
            'date_vente' => 'required|date',
            'date_signature' => 'nullable|date',
            'statut' => 'required|in:en_cours,completé,annulé',
            'notes' => 'nullable|string',
        ]);

        $vente = Vente::create($validated);

        // Mettre à jour le statut de l'annonce
        $annonce = Annonce::find($validated['annonce_id']);
        $annonce->update(['statut' => 'vendu']);

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
            'commission_agence' => 'nullable|numeric|min:0',
            'type_commission' => 'nullable|in:fixe,pourcentage',
            'date_vente' => 'required|date',
            'date_signature' => 'nullable|date',
            'statut' => 'required|in:en_cours,completé,annulé',
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
            'type_commission' => 'nullable|in:fixe,pourcentage',
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
