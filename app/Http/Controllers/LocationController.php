<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Echeance;
use App\Models\Paiement;
use App\Models\Annonce;
use App\Models\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::with(['annonce', 'locataire', 'echeances', 'demandeInteret'])
            ->latest()
            ->paginate(15);
        
        return view('backend.pages.locations.index', compact('locations'));
    }

    public function create()
    {
        $annonces = Annonce::where('statut', 'disponible')
            ->where('type_transaction', 'location')
            ->get();
        // Récupérer tous les utilisateurs qui ont des rôles de locataires
        $locataires = User::whereHas('roles', function($q) {
            $q->where('name', 'locataire');
        })->get();
        
        return view('backend.pages.locations.create', compact('annonces', 'locataires'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'annonce_id' => 'required|exists:annonces,id',
            'locataire_id' => 'required|exists:users,id',
            'message_client' => 'nullable|string',
            'loyer_mensuel' => 'nullable|numeric|min:0',
            'nombre_cautions' => 'nullable|integer|min:0',
            'caution' => 'nullable|numeric|min:0',
            'montant_frais_agence' => 'nullable|numeric|min:0',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'jour_paiement' => 'nullable|integer|min:1|max:31',
            'conditions' => 'nullable|string',
            'statut_initial' => 'nullable|in:demande_client,brouillon,fiche_envoyee',
        ]);

        // Utiliser le statut choisi par l'admin, sinon 'brouillon' par défaut pour création admin
        $validated['statut'] = $request->statut_initial ?? 'brouillon';
        unset($validated['statut_initial']);
        
        $location = Location::create($validated);

        $message = 'Location enregistrée avec succès.';
        if ($validated['statut'] == 'brouillon') {
            $message .= ' Configurez les détails et envoyez la fiche pour démarrer le workflow.';
        } elseif ($validated['statut'] == 'demande_client') {
            $message .= ' Vous devrez envoyer la fiche au client pour continuer.';
        } elseif ($validated['statut'] == 'fiche_envoyee') {
            $message .= ' Vous pouvez maintenant planifier une visite.';
        }
        
        Alert::success('Succès', $message);
        return redirect()->route('backend.locations.show', $location);
    }

    public function show(Location $location)
    {
        $location->load(['annonce', 'locataire', 'echeances', 'paiements']);
        return view('backend.pages.locations.show', compact('location'));
    }

    public function edit(Location $location)
    {
        $annonces = Annonce::all();
        $locataires = User::whereDoesntHave('roles', function($q) {
            $q->where('name', 'superadmin')
              ->orWhere('name', 'developpeur')
              ->orWhere('name', 'admin');
        })->get();
        
        return view('backend.pages.locations.edit', compact('location', 'annonces', 'locataires'));
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'annonce_id' => 'required|exists:annonces,id',
            'locataire_id' => 'required|exists:users,id',
            'loyer_mensuel' => 'required|numeric|min:0',
            'nombre_cautions' => 'required|integer|min:0',
            'caution' => 'nullable|numeric|min:0',
            'montant_frais_agence' => 'nullable|numeric|min:0',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'statut' => 'required|in:demande_client,fiche_envoyee,visite_planifiee,en_attente_paiement,actif,termine,resilie',
            'jour_paiement' => 'required|integer|min:1|max:31',
            'conditions' => 'nullable|string',
        ]);

        $location->update($validated);

        Alert::success('Succès', 'Location modifiée avec succès');
        return redirect()->route('backend.locations.show', $location);
    }

    public function destroy(Location $location)
    {
        $annonce = $location->annonce;
        $annonce->update(['statut' => 'disponible']);
        
        $location->delete();

        Alert::success('Succès', 'Location supprimée avec succès');
        return redirect()->route('backend.locations.index');
    }

    // === ACTIONS DU WORKFLOW ===

    /**
     * Envoyer la fiche au client
     */
    public function envoyerFiche(Request $request, Location $location)
    {
        $request->validate([
            'note_admin' => 'nullable|string',
            'message_email' => 'nullable|string',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // Max 10MB par fichier
        ]);

        $location->update([
            'statut' => 'fiche_envoyee',
            'note_admin' => $request->note_admin,
        ]);

        // Gestion des documents uploadés
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $location->addMedia($document)
                    ->toMediaCollection('documents_fiche');
            }
        }

        // TODO: Envoyer email avec fiche à remplir et documents
        // Utiliser $request->message_email pour le message personnalisé
        // Inclure les documents uploadés en pièces jointes

        Alert::success('Succès', 'Fiche envoyée au locataire par email avec ' . ($request->hasFile('documents') ? count($request->file('documents')) : 0) . ' document(s). Vous pouvez maintenant planifier une visite.');
        return back();
    }

    /**
     * Marquer la fiche comme envoyée (sans envoyer d'email)
     * Utile quand la fiche a été envoyée manuellement
     */
    public function marquerFicheEnvoyee(Location $location)
    {
        if (!in_array($location->statut, ['brouillon', 'demande_client'])) {
            Alert::error('Erreur', 'Cette action n\'est disponible que pour les locations en brouillon ou demande client.');
            return back();
        }

        $location->update([
            'statut' => 'fiche_envoyee',
        ]);

        Alert::success('Succès', 'Location marquée comme "Fiche envoyée". Vous pouvez maintenant planifier une visite.');
        return back();
    }

    /**
     * Planifier une visite
     */
    public function planifierVisite(Request $request, Location $location)
    {
        $request->validate([
            'date_visite' => 'required|date',
            'note_admin' => 'nullable|string',
        ]);

        $location->update([
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
    public function visiteEffectuee(Request $request, Location $location)
    {
        $request->validate([
            'compte_rendu_visite' => 'required|string',
            'client_interesse' => 'required|boolean',
            'note_admin' => 'nullable|string',
        ]);

        if (!$request->client_interesse) {
            $location->update([
                'statut' => 'resilie',
                'compte_rendu_visite' => $request->compte_rendu_visite,
                'note_admin' => $request->note_admin,
                'date_finalisation' => now(),
            ]);
            Alert::warning('Annulé', 'Location marquée comme résilée - Client non intéressé après la visite');
            return back();
        }

        $location->update([
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
    public function configurerPaiement(Request $request, Location $location)
    {
        $request->validate([
            'loyer_mensuel' => 'required|numeric|min:0',
            'nombre_cautions' => 'required|integer|min:0',
            'avance_sur_loyer' => 'required|integer|min:0|max:12',
            'montant_frais_agence' => 'nullable|numeric|min:0',
            'commission_agence' => 'nullable|numeric|min:0',
            'type_commission' => 'nullable|in:pourcentage,montant',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'jour_paiement' => 'required|integer|min:1|max:31',
            'conditions' => 'nullable|string',
            'note_admin' => 'nullable|string',
        ]);

        // Calculer automatiquement la caution (nombre de cautions * loyer mensuel)
        $caution = $request->loyer_mensuel * $request->nombre_cautions;
        
        // Calculer le montant de l'avance (avance_sur_loyer * loyer mensuel)
        $montantAvance = $request->loyer_mensuel * $request->avance_sur_loyer;

        $location->update([
            'statut' => 'en_attente_paiement',
            'loyer_mensuel' => $request->loyer_mensuel,
            'nombre_cautions' => $request->nombre_cautions,
            'caution' => $caution,
            'avance_sur_loyer' => $request->avance_sur_loyer,
            'montant_avance' => $montantAvance,
            'montant_frais_agence' => $request->montant_frais_agence ?? 0,
            'commission_agence' => $request->commission_agence ?? 0,
            'type_commission' => $request->type_commission ?? 'montant',
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'jour_paiement' => $request->jour_paiement,
            'conditions' => $request->conditions,
            'note_admin' => $request->note_admin,
        ]);

        $montantPremierPaiement = $caution + $montantAvance + ($request->montant_frais_agence ?? 0);
        Alert::success('Succès', 'Configuration du paiement enregistrée. Premier paiement requis : ' . number_format($montantPremierPaiement, 0, ',', ' ') . ' FCFA (Caution + Avance ' . $request->avance_sur_loyer . ' mois + Frais d\'agence)');
        return back();
    }

    /**
     * Enregistrer le premier paiement (caution + avance + frais)
     */
    public function enregistrerPremierPaiement(Request $request, Location $location)
    {
        $validated = $request->validate([
            'montant_caution' => 'nullable|numeric|min:0',
            'montant_avance' => 'nullable|numeric|min:0',
            'montant_frais' => 'nullable|numeric|min:0',
            'date_paiement' => 'required|date',
            'methode_paiement' => 'required|in:espèces,virement,chèque,carte_bancaire,mobile_money,autre',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Enregistrer le paiement de la caution
        if ($validated['montant_caution'] > 0) {
            $location->paiements()->create([
                'type_paiement' => 'caution',
                'statut' => 'paye',
                'montant' => $validated['montant_caution'],
                'date_paiement' => $validated['date_paiement'],
                'methode_paiement' => $validated['methode_paiement'],
                'reference' => $validated['reference'],
                'notes' => 'Paiement de la caution',
            ]);
        }

        // Enregistrer le paiement de l'avance
        if ($validated['montant_avance'] > 0) {
            $location->paiements()->create([
                'type_paiement' => 'avance',
                'statut' => 'paye',
                'montant' => $validated['montant_avance'],
                'date_paiement' => $validated['date_paiement'],
                'methode_paiement' => $validated['methode_paiement'],
                'reference' => $validated['reference'],
                'notes' => 'Avance sur loyer (' . $location->avance_sur_loyer . ' mois)',
            ]);
        }

        // Enregistrer le paiement des frais d'agence
        if ($validated['montant_frais'] > 0) {
            $location->paiements()->create([
                'type_paiement' => 'frais_agence',
                'statut' => 'paye',
                'montant' => $validated['montant_frais'],
                'date_paiement' => $validated['date_paiement'],
                'methode_paiement' => $validated['methode_paiement'],
                'reference' => $validated['reference'],
                'notes' => 'Frais d\'agence',
            ]);
        }

        $montantTotal = ($validated['montant_caution'] ?? 0) + ($validated['montant_avance'] ?? 0) + ($validated['montant_frais'] ?? 0);
        Alert::success('Succès', 'Premier paiement de ' . number_format($montantTotal, 0, ',', ' ') . ' FCFA enregistré avec succès.');
        return back();
    }

    /**
     * Valider le premier paiement complet et générer les échéances
     */
    public function validerPremierPaiement(Request $request, Location $location)
    {
        // Vérifier que le premier paiement est complet
        if (!$location->premierPaiementComplet()) {
            Alert::error('Erreur', 'Le premier paiement n\'est pas complet. Veuillez enregistrer tous les paiements requis avant de valider.');
            return back();
        }

        // Générer les échéances
        $location->genererEcheances();

        // Activer la location
        $location->update([
            'statut' => 'actif',
            'date_finalisation' => now(),
        ]);

        // Marquer le bien comme loué
        $location->annonce->update([
            'statut' => 'loué',
            'statut_publication' => 0,
        ]);

        Alert::success('Succès', 'Premier paiement validé ! Location activée et échéances générées. Les ' . $location->avance_sur_loyer . ' premières échéances sont marquées comme payées.');
        return back();
    }

    /**
     * Enregistrer un paiement de loyer sur une échéance
     */
    public function enregistrerPaiementLoyer(Request $request, Echeance $echeance)
    {
        $validated = $request->validate([
            'montant' => 'required|numeric|min:0',
            'date_paiement' => 'required|date',
            'methode_paiement' => 'required|in:espèces,virement,chèque,carte_bancaire,mobile_money,autre',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
            'commission_agence' => 'nullable|numeric|min:0',
            'type_commission' => 'nullable|in:pourcentage,montant',
        ]);

        // Vérifier que le montant ne dépasse pas le reste à payer
        $montantRestant = $echeance->montantRestant();
        if ($validated['montant'] > $montantRestant) {
            Alert::error('Erreur', 'Le montant du paiement (' . number_format($validated['montant'], 0, ',', ' ') . ' FCFA) dépasse le montant restant à payer (' . number_format($montantRestant, 0, ',', ' ') . ' FCFA)');
            return back();
        }

        // Créer le paiement
        $paiement = $echeance->location->paiements()->create([
            'echeance_id' => $echeance->id,
            'type_paiement' => 'loyer',
            'statut' => 'paye',
            'montant' => $validated['montant'],
            'commission_agence' => $validated['commission_agence'] ?? 0,
            'type_commission' => $validated['type_commission'] ?? 'montant',
            'date_paiement' => $validated['date_paiement'],
            'methode_paiement' => $validated['methode_paiement'],
            'reference' => $validated['reference'],
            'notes' => $validated['notes'],
        ]);

        // Mettre à jour l'échéance
        $echeance->montant_paye += $validated['montant'];
        $echeance->save();
        $echeance->mettreAJourStatut();

        $messageCommission = ($validated['commission_agence'] ?? 0) > 0 ? ' (Commission: ' . number_format($validated['commission_agence'], 0, ',', ' ') . ' FCFA)' : '';
        Alert::success('Succès', 'Paiement de loyer de ' . number_format($validated['montant'], 0, ',', ' ') . ' FCFA enregistré avec succès' . $messageCommission);
        return back();
    }

    /**
     * Résilier la location
     */
    public function resilierLocation(Request $request, Location $location)
    {
        $request->validate([
            'note_admin' => 'required|string',
        ]);

        $location->update([
            'statut' => 'resilie',
            'note_admin' => $request->note_admin,
        ]);

        Alert::success('Succès', 'Location résiliée');
        return back();
    }

    /**
     * Générer de nouvelles échéances pour prolonger la location
     */
    public function genererNouvellesEcheances(Request $request, Location $location)
    {
        $request->validate([
            'nombre_mois' => 'required|integer|min:1|max:24',
        ]);

        if ($location->statut !== 'actif') {
            Alert::error('Erreur', 'Seules les locations actives peuvent avoir de nouvelles échéances générées.');
            return back();
        }

        $nombreEcheancesCreees = $location->genererEcheancesSuivantes($request->nombre_mois);

        if ($nombreEcheancesCreees > 0) {
            Alert::success('Succès', $nombreEcheancesCreees . ' nouvelles échéances générées pour prolonger la location.');
        } else {
            Alert::error('Erreur', 'Impossible de générer de nouvelles échéances. Vérifiez qu\'il existe déjà des échéances.');
        }

        return back();
    }

    public function createFromDemande($demandeId)
    {
        $demande = \App\Models\DemandeInteret::with(['annonce', 'user'])->findOrFail($demandeId);
        
        // Vérifier si une location existe déjà pour cette demande
        if ($demande->location) {
            Alert::warning('Attention', 'Une location existe déjà pour cette demande');
            return redirect()->route('backend.locations.show', $demande->location);
        }

        return view('backend.pages.locations.create-from-demande', compact('demande'));
    }

    public function storeFromDemande(Request $request, $demandeId)
    {
        $demande = \App\Models\DemandeInteret::findOrFail($demandeId);
        
        $validated = $request->validate([
            'loyer_mensuel' => 'required|numeric|min:0',
            'nombre_cautions' => 'required|integer|min:0',
            'caution' => 'nullable|numeric|min:0',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'jour_paiement' => 'required|integer|min:1|max:31',
            'conditions' => 'nullable|string',
        ]);

        $validated['demande_interet_id'] = $demande->id;
        $validated['annonce_id'] = $demande->annonce_id;
        $validated['locataire_id'] = $demande->user_id;
        $validated['statut'] = 'actif';

        $location = Location::create($validated);

        // Mettre à jour le statut de l'annonce
        $demande->annonce->update(['statut' => 'loue']);
        
        // Mettre à jour le statut de la demande
        $demande->update(['statut' => 'paiement_valide']);

        // Créer les échéances
        $this->genererEcheances($location);
        
        // Si un paiement a été effectué dans la demande, créer un paiement pour la première échéance
        if ($demande->details_paiement && isset($demande->details_paiement['montant']) && $demande->details_paiement['montant'] > 0) {
            $premiereEcheance = $location->echeances()->orderBy('date_echeance', 'asc')->first();
            
            if ($premiereEcheance) {
                $montantPaye = $demande->details_paiement['montant'];
                
                // Créer le paiement
                \App\Models\Paiement::create([
                    'echeance_id' => $premiereEcheance->id,
                    'montant' => $montantPaye,
                    'date_paiement' => $demande->details_paiement['date_paiement'] ?? now(),
                    'mode_paiement' => $demande->details_paiement['mode_paiement'] ?? 'Non spécifié',
                    'reference' => $demande->details_paiement['reference'] ?? null,
                    'notes' => $demande->details_paiement['notes'] ?? 'Paiement initial depuis la demande',
                ]);
                
                // Mettre à jour l'échéance
                $premiereEcheance->montant_paye = $montantPaye;
                $premiereEcheance->save();
                $premiereEcheance->updateStatut();
            }
        }

        Alert::success('Succès', 'Location créée avec succès depuis la demande. Le paiement initial a été pris en compte.');
        return redirect()->route('backend.locations.show', $location);
    }
}
