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
            'loyer_mensuel' => 'required|numeric|min:0',
            'nombre_cautions' => 'required|integer|min:0',
            'caution' => 'nullable|numeric|min:0',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'jour_paiement' => 'required|integer|min:1|max:31',
            'conditions' => 'nullable|string',
        ]);

        $validated['statut'] = 'actif';
        $location = Location::create($validated);

        // Mettre à jour le statut de l'annonce
        $annonce = Annonce::find($validated['annonce_id']);
        $annonce->update(['statut' => 'loué']);

        // Créer les échéances pour les 12 prochains mois
        $this->genererEcheances($location);

        Alert::success('Succès', 'Location enregistrée avec succès');
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
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'statut' => 'required|in:actif,terminé,résilié',
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
        return redirect()->route('locations.index');
    }

    public function addPaiement(Request $request, Location $location)
    {
        $validated = $request->validate([
            'echeance_id' => 'nullable|exists:echeances,id',
            'montant' => 'required|numeric|min:0',
            'date_paiement' => 'required|date',
            'methode_paiement' => 'required|in:espèces,virement,chèque,carte_bancaire,autre',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Si une échéance est spécifiée, vérifier que le montant ne dépasse pas le montant restant
        if (isset($validated['echeance_id'])) {
            $echeance = Echeance::find($validated['echeance_id']);
            $montantRestant = $echeance->montant_du - $echeance->montant_paye;
            
            if ($validated['montant'] > $montantRestant) {
                Alert::error('Erreur', 'Le montant du paiement (' . number_format($validated['montant'], 0, ',', ' ') . ' FCFA) dépasse le montant restant à payer (' . number_format($montantRestant, 0, ',', ' ') . ' FCFA)');
                return redirect()->back()->withInput();
            }
        }

        // Créer le paiement
        $paiement = $location->paiements()->create($validated);

        // Si une échéance est spécifiée, mettre à jour son montant payé
        if (isset($validated['echeance_id'])) {
            $echeance = Echeance::find($validated['echeance_id']);
            $echeance->montant_paye += $validated['montant'];
            $echeance->updateStatut();
        }

        Alert::success('Succès', 'Paiement ajouté avec succès');
        return redirect()->route('backend.locations.show', $location);
    }

    private function genererEcheances(Location $location)
    {
        $dateDebut = Carbon::parse($location->date_debut);
        $dateFin = $location->date_fin ? Carbon::parse($location->date_fin) : $dateDebut->copy()->addYear();
        
        // Calculer la première échéance basée sur jour_paiement
        $jourPaiement = (int) $location->jour_paiement;
        $dateEcheance = Carbon::create(
            $dateDebut->year,
            $dateDebut->month,
            min($jourPaiement, $dateDebut->daysInMonth),
            0, 0, 0
        );
        
        // Si le jour de paiement du mois en cours est déjà passé, commencer au mois suivant
        if ($dateEcheance < $dateDebut) {
            $dateEcheance->addMonth();
            $dateEcheance->day = (int) min($jourPaiement, $dateEcheance->daysInMonth);
        }
        
        while ($dateEcheance <= $dateFin) {
            Echeance::create([
                'location_id' => $location->id,
                'date_echeance' => $dateEcheance->copy(),
                'montant_du' => $location->loyer_mensuel,
                'montant_paye' => 0,
                'statut' => 'en_attente',
            ]);
            
            // Ajouter un mois pour la prochaine échéance
            $dateEcheance->addMonth();
            // Ajuster le jour si le mois a moins de jours (ex: 31 -> 28/29 février)
            $dateEcheance->day = (int) min($jourPaiement, $dateEcheance->daysInMonth);
        }
    }

    public function updateEcheance(Request $request, Echeance $echeance)
    {
        $validated = $request->validate([
            'montant_paye' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $echeance->update($validated);
        $echeance->updateStatut();

        Alert::success('Succès', 'Échéance mise à jour avec succès');
        return redirect()->back();
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
