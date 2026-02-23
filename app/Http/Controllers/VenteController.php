<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Paiement;
use App\Models\Annonce;
use App\Models\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Controllers\Controller;

class VenteController extends Controller
{
    public function index(Request $request)
    {
        $query = Vente::with(['annonce', 'client', 'paiements']);

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre par client
        if ($request->filled('client')) {
            $query->where('client_id', $request->client);
        }

        // Filtre par période
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        $ventes = $query->latest()->paginate(15)->withQueryString();

        // Récupérer tous les clients qui ont au moins une vente
        $clients = User::whereHas('ventes')->orderBy('username')->get();


        // Confirmation de suppression
        $title = 'Suppression de vente';
        $text = "Êtes-vous sûr de vouloir supprimer cette vente ?";
        confirmDelete($title, $text);

        return view('backend.pages.ventes.index', compact('ventes', 'clients'));
    }

    public function create()
    {
        $annonces = Annonce::where('statut', 'disponible')
            ->where('type_transaction', 'vente')
            ->get();
        // Récupérer tous les utilisateurs sauf les admins
        $clients = User::whereDoesntHave('roles', function ($q) {
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
            'notes' => 'nullable|string',
        ]);

        // Créer la vente avec le statut initial de demande client
        $validated['statut'] = 'demande_client';
        $vente = Vente::create($validated);

        Alert::success('Succès', 'Vente créée avec succès. Vous pouvez maintenant gérer le workflow.');
        return redirect()->route('backend.ventes.show', $vente);
    }

    public function show(Vente $vente)
    {
        $vente->load(['annonce', 'client', 'paiements']);
        return view('backend.pages.ventes.show', compact('vente'));
    }

    public function fiche(Vente $vente)
    {
        $vente->load(['annonce.typeBien', 'client', 'paiements']);
        return view('backend.pages.ventes.fiche', compact('vente'));
    }

    public function edit(Vente $vente)
    {
        // Bloquer la modification si des paiements existent
        if ($vente->paiements()->count() > 0) {
            Alert::warning('Modification impossible', 'Cette vente ne peut plus être modifiée car des paiements ont déjà été enregistrés.');
            return redirect()->route('backend.ventes.show', $vente);
        }

        $annonces = Annonce::all();
        $clients = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'superadmin')
                ->orWhere('name', 'developpeur')
                ->orWhere('name', 'admin');
        })->get();

        return view('backend.pages.ventes.edit', compact('vente', 'annonces', 'clients'));
    }

    public function update(Request $request, Vente $vente)
    {
        // Bloquer la modification si des paiements existent
        if ($vente->paiements()->count() > 0) {
            Alert::warning('Modification impossible', 'Cette vente ne peut plus être modifiée car des paiements ont déjà été enregistrés.');
            return redirect()->route('backend.ventes.show', $vente);
        }

        $validated = $request->validate([
            'annonce_id' => 'required|exists:annonces,id',
            'client_id' => 'required|exists:users,id',
            'prix_vente' => 'required|numeric|min:0',
            'message' => 'nullable|string',
        ]);

        // Réinitialiser le workflow à demande_client
        $validated['statut'] = 'demande_client';

        $vente->update($validated);

        Alert::success('Succès', 'Vente modifiée avec succès. Le workflow a été réinitialisé à "Demande client".');
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
            'statut' => 'retour_prospect',
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

        Alert::success('Succès', 'Fiche envoyée au client par email avec ' . ($request->hasFile('documents') ? count($request->file('documents')) : 0) . ' document(s). En attente du retour du client.');
        return back();
    }

    /**
     * Confirmer le retour du prospect
     */
    public function confirmerRetourProspect(Request $request, Vente $vente)
    {
        $request->validate([
            'client_interesse' => 'required|boolean',
            'note_admin' => 'nullable|string',
        ]);

        if (!$request->client_interesse) {
            $vente->update([
                'statut' => 'annulee',
                'note_admin' => $request->note_admin,
                'client_interesse_retour' => false, // Indiquer que le client n'est pas interessé apres le retour de la fiche
            ]);
            Alert::info('Info', 'Vente annulée - Client non intéressé');
            return back();
        }

        $vente->update([
            'statut' => 'fiche_envoyee',
            'note_admin' => $request->note_admin,
            'client_interesse_retour' => true, // Indiquer que le client est interessé apres le retour de la fiche
        ]);

        Alert::success('Succès', 'Client confirmé intéressé. Vous pouvez maintenant planifier une visite.');
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
            'compte_rendu_visite' => 'nullable|string',
            'client_interesse' => 'required|boolean',
            'note_admin' => 'nullable|string',
        ]);

        if (!$request->client_interesse) {
            $vente->update([
                'statut' => 'annule',
                'compte_rendu_visite' => $request->compte_rendu_visite,
                'note_admin' => $request->note_admin,
                'client_interesse_visite' => false, // Indiquer que le client n'est pas intéressé après la visite
            ]);
            Alert::info('Info', 'Vente annulée - Client non intéressé après la visite');
            return back();
        }

        $vente->update([
            'statut' => 'offre_acceptee',
            'compte_rendu_visite' => $request->compte_rendu_visite,
            'note_admin' => $request->note_admin,
            'client_interesse_visite' => true, // Indiquer que le client est intéressé après la visite
        ]);

        // Convertir le client en acheteur
        // $vente->client->syncRoles(['acheteur']);

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
            'statut' => 'offre_acceptee',
            'prix_vente' => $request->prix_vente,
            'montant_caution' => $request->montant_caution ?? 0,
            'montant_frais_agence' => $request->montant_frais_agence ?? 0,
            'commission_agence' => $commissionAgence ?? 0,
            'type_commission' => $typeCommission,
            'note_admin' => $request->note_admin,
        ]);

    //    //changer le role du client en acheteur s'il ne l'est pas déjà
    //     if (!$vente->client->hasRole('acheteur')) {
    //         $vente->client->syncRoles(['acheteur']);
    //     }
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
        if (!$vente->estEntierementPaye()) {
            $resteAPayer = $vente->resteAPayer();
            Alert::error('Erreur', 'Le paiement n\'est pas complet. Il reste ' . number_format($resteAPayer, 0, ',', ' ') . ' FCFA à payer. Ajoutez les paiements manquants avant de finaliser la vente.');
            return back();
        }

        // Finaliser la vente
        $vente->update([
            'statut' => 'terminee',
            'date_signature' => $request->date_signature ?? now(),
            'date_finalisation' => now(),
            'note_admin' => $request->note_admin,
        ]);



        // Marquer le bien comme vendu et le dépublier
        $vente->annonce->update([
            'statut' => 'vendu',
            'statut_publication' => 0,
        ]);

        // Récupérer les statistiques de paiement
        $montantTotal = $vente->montantTotalPaye();
        $commissionTotale = $vente->totalCommissionsPercues();
        $nombrePaiements = $vente->paiements()->count();

        // Convertir le client en acheteur (si ce n'est pas déjà fait)
     //changer le role du client en acheteur s'il ne l'est pas déjà
        if (!$vente->client->hasRole('acheteur')) {
            $vente->client->syncRoles(['acheteur']);
        }

        Alert::success(
            'Succès',
            'Vente finalisée avec succès ! ' .
                'Montant total payé : ' . number_format($montantTotal, 0, ',', ' ') . ' FCFA ' .
                'en ' . $nombrePaiements . ' paiement(s). ' .
                'Commission perçue : ' . number_format($commissionTotale, 0, ',', ' ') . ' FCFA. ' .
                'Le bien a été marqué comme vendu.'
        );

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
            // Configuration de la commission (seulement au premier paiement)
            'commission_agence' => 'nullable|numeric|min:0',
            'type_commission' => 'nullable|in:pourcentage,montant',
        ]);

        // Vérifier que le montant ne dépasse pas le montant total à payer
        $montantRestant = $vente->resteAPayer();

        if ($validated['montant'] > $montantRestant) {
            Alert::error('Erreur', 'Le montant du paiement (' . number_format($validated['montant'], 0, ',', ' ') . ' FCFA) dépasse le montant restant à payer (' . number_format($montantRestant, 0, ',', ' ') . ' FCFA)');
            return redirect()->back()->withInput();
        }

        // Configuration de la commission (seulement si pas encore définie)
        if (!$vente->commission_agence && isset($validated['commission_agence']) && isset($validated['type_commission'])) {
            // Enregistrer la configuration de la commission
            $vente->update([
                'commission_agence' => $validated['commission_agence'],
                'type_commission' => $validated['type_commission'],
            ]);
        }

        // Créer le paiement avec historique immuable
        $paiement = $vente->paiements()->create([
            'type_paiement' => 'prix_achat',
            'montant' => $validated['montant'],
            'date_paiement' => $validated['date_paiement'],
            'methode_paiement' => $validated['methode_paiement'],
            'reference' => $validated['reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'statut' => 'paye',
        ]);

        // Vérifier si le paiement est maintenant complet
        if ($vente->estEntierementPaye() && $vente->statut === 'offre_acceptee') {
            Alert::success('Succès', 'Paiement ajouté avec succès (' . number_format($validated['montant'], 0, ',', ' ') . ' FCFA). Le paiement est maintenant complet ! Vous pouvez valider la vente.');
        } else {
            $resteAPayer = $vente->resteAPayer();
            Alert::success('Succès', 'Paiement de ' . number_format($validated['montant'], 0, ',', ' ') . ' FCFA ajouté. Reste à payer : ' . number_format($resteAPayer, 0, ',', ' ') . ' FCFA');
        }
        //changer le role du client en acheteur s'il ne l'est pas déjà
        if (!$vente->client->hasRole('acheteur')) {
            $vente->client->syncRoles(['acheteur']);
        }

        return redirect()->route('backend.ventes.show', $vente);
    }
}
