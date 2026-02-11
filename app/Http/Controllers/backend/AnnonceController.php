<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Models\TypeBien;
use App\Models\Equipement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AnnonceController extends Controller
{
    /**
     * Afficher la liste des annonces
     */
    public function index(Request $request)
    {
        $query = Annonce::with(['proprietaire', 'createdBy', 'media', 'typeBien', 'equipements']);

        // Filtre par type de propriétaire
        if ($request->filled('type_proprietaire')) {
            if ($request->type_proprietaire === 'agence') {
                $query->where('est_bien_agence', true);
            } elseif ($request->type_proprietaire === 'externe') {
                $query->where('est_bien_agence', false);
                
                // Filtre par propriétaire spécifique (uniquement pour les biens externes)
                if ($request->filled('proprietaire_id')) {
                    $query->where('proprietaire_id', $request->proprietaire_id);
                }
            }
        } elseif ($request->filled('proprietaire_id')) {
            // Si pas de type spécifié mais un propriétaire, filtrer par propriétaire
            $query->where('proprietaire_id', $request->proprietaire_id);
        }

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $annonces = $query->orderBy('created_at', 'desc')->get();
        
        // Récupérer la liste des propriétaires pour le filtre
        $proprietaires = User::proprietaires()->orderBy('username')->get();

        return view('backend.pages.annonces.index', compact('annonces', 'proprietaires'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $typeBiens = TypeBien::actif()->ordered()->get();
        $equipements = Equipement::actif()->ordered()->get();
        $villes = config('ville-commune');
        
        // Récupérer uniquement les utilisateurs ayant le rôle de propriétaire
        $proprietaires = User::proprietaires()->orderBy('username')->get();

        return view('backend.pages.annonces.create', compact('typeBiens', 'equipements', 'villes', 'proprietaires'));
    }

    /**
     * Enregistrer une nouvelle annonce
     */
    public function store(Request $request)
    {
        // Règles de validation de base
        $rules = [
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'type_transaction' => 'required|in:vente,location',
            'type_bien_id' => 'required|exists:type_biens,id',
            'est_bien_agence' => 'nullable|boolean',
            'prix' => 'required|numeric|min:0',
            'surface' => 'nullable|numeric|min:0',
            'nombre_chambres' => 'nullable|integer|min:0',
            'nombre_salles_bain' => 'nullable|integer|min:0',
            'nombre_pieces' => 'nullable|integer|min:0',
            'etage' => 'nullable|integer',
            'adresse' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'commune' => 'nullable|string|max:255',
            'quartier' => 'nullable|string|max:255',
            'code_postal' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'statut' => 'required|in:disponible,loue,vendu,en_attente',
            'date_disponibilite' => 'nullable|date',
            'annee_construction' => 'nullable|integer|min:1800|max:' . (date('Y') + 5),
            'image_principale' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ];

        // Si ce n'est pas un bien de l'agence, le propriétaire est obligatoire
        if (!$request->has('est_bien_agence') || !$request->est_bien_agence) {
            $rules['proprietaire_id'] = 'required|exists:users,id';
        } else {
            $rules['proprietaire_id'] = 'nullable|exists:users,id';
        }

        $validator = Validator::make($request->all(), $rules, [
            'titre.required' => 'Le titre est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'type_transaction.required' => 'Le type de transaction est obligatoire.',
            'type_bien_id.required' => 'Le type de bien est obligatoire.',
            'proprietaire_id.required' => 'Le propriétaire est obligatoire pour un bien externe.',
            'proprietaire_id.exists' => 'Le propriétaire sélectionné n\'est pas valide.',
            'type_bien_id.exists' => 'Le type de bien sélectionné n\'est pas valide.',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.numeric' => 'Le prix doit être un nombre.',
            'adresse.required' => 'L\'adresse est obligatoire.',
            'ville.required' => 'La ville est obligatoire.',
            'statut.required' => 'Le statut est obligatoire.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except(['image_principale', 'images', 'documents', 'equipements', 'commission', 'type_commission']);
        $data['created_by_id'] = Auth::id(); // L'utilisateur connecté qui crée l'annonce
        $data['reference'] = Annonce::genererReference();
        $data['en_vedette'] = $request->has('en_vedette');
        $data['est_bien_agence'] = $request->has('est_bien_agence') && $request->est_bien_agence;
        
        // Si c'est un bien de l'agence et qu'aucun propriétaire n'est spécifié, on met null
        if ($data['est_bien_agence'] && empty($data['proprietaire_id'])) {
            $data['proprietaire_id'] = null;
        }

        $annonce = Annonce::create($data);

        // Attacher les équipements
        if ($request->has('equipements')) {
            $annonce->equipements()->attach($request->equipements);
        }

        // Gérer l'image principale
        if ($request->hasFile('image_principale')) {
            $annonce->addMediaFromRequest('image_principale')
                ->toMediaCollection('image_principale');
        }

        // Gérer les images supplémentaires
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $annonce->addMedia($image)
                    ->toMediaCollection('images');
            }
        }

        // Gérer les documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $annonce->addMedia($document)
                    ->toMediaCollection('documents');
            }
        }

        return redirect()->route('backend.annonces.index')
            ->with('success', 'Annonce créée avec succès.');
    }

    /**
     * Afficher une annonce
     */
    public function show(Annonce $annonce)
    {
        $annonce->load([
            'proprietaire', 
            'createdBy', 
            'media', 
            'typeBien', 
            'equipements',
            'locations.locataire',
            'locations.echeances',
            'locations.paiements',
            'ventes.client',
            'ventes.paiements'
        ]);
        return view('backend.pages.annonces.show', compact('annonce'));
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit(Annonce $annonce)
    {
        $typeBiens = TypeBien::actif()->ordered()->get();
        $equipements = Equipement::actif()->ordered()->get();
        $villes = config('ville-commune');
        $annonce->load(['typeBien', 'equipements']);
        
        // Récupérer uniquement les utilisateurs ayant le rôle de propriétaire
        $proprietaires = User::proprietaires()->orderBy('username')->get();

        return view('backend.pages.annonces.edit', compact('annonce', 'typeBiens', 'equipements', 'villes', 'proprietaires'));
    }

    /**
     * Mettre à jour une annonce
     */
    public function update(Request $request, Annonce $annonce)
    {
        // Règles de validation de base
        $rules = [
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'type_transaction' => 'required|in:vente,location',
            'type_bien_id' => 'required|exists:type_biens,id',
            'est_bien_agence' => 'nullable|boolean',
            'prix' => 'required|numeric|min:0',
            'surface' => 'nullable|numeric|min:0',
            'nombre_chambres' => 'nullable|integer|min:0',
            'nombre_salles_bain' => 'nullable|integer|min:0',
            'nombre_pieces' => 'nullable|integer|min:0',
            'etage' => 'nullable|integer',
            'adresse' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'commune' => 'nullable|string|max:255',
            'quartier' => 'nullable|string|max:255',
            'code_postal' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'statut' => 'required|in:disponible,loue,vendu,en_attente',
            'date_disponibilite' => 'nullable|date',
            'annee_construction' => 'nullable|integer|min:1800|max:' . (date('Y') + 5),
            'image_principale' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ];

        // Si ce n'est pas un bien de l'agence, le propriétaire est obligatoire
        if (!$request->has('est_bien_agence') || !$request->est_bien_agence) {
            $rules['proprietaire_id'] = 'required|exists:users,id';
        } else {
            $rules['proprietaire_id'] = 'nullable|exists:users,id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except(['image_principale', 'images', 'documents', 'equipements', 'commission', 'type_commission']);
        $data['en_vedette'] = $request->has('en_vedette');
        $data['est_bien_agence'] = $request->has('est_bien_agence') && $request->est_bien_agence;
        
        // Si c'est un bien de l'agence et qu'aucun propriétaire n'est spécifié, on met null
        if ($data['est_bien_agence'] && empty($data['proprietaire_id'])) {
            $data['proprietaire_id'] = null;
        }

        $annonce->update($data);

        // Synchroniser les équipements
        if ($request->has('equipements')) {
            $annonce->equipements()->sync($request->equipements);
        } else {
            $annonce->equipements()->detach();
        }

        // Gérer l'image principale
        if ($request->hasFile('image_principale')) {
            $annonce->clearMediaCollection('image_principale');
            $annonce->addMediaFromRequest('image_principale')
                ->toMediaCollection('image_principale');
        }

        // Gérer les images supplémentaires
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $annonce->addMedia($image)
                    ->toMediaCollection('images');
            }
        }

        // Gérer les documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $annonce->addMedia($document)
                    ->toMediaCollection('documents');
            }
        }

        return redirect()->route('backend.annonces.index')
            ->with('success', 'Annonce mise à jour avec succès.');
    }



    /**
     * Supprimer une image
     */
    public function deleteImage(Request $request)
    {
        $mediaId = $request->input('media_id');
        $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::find($mediaId);

        if ($media) {
            $media->delete();
            return response()->json(['success' => true, 'message' => 'Image supprimée avec succès.']);
        }

        return response()->json(['success' => false, 'message' => 'Image non trouvée.'], 404);
    }

    /**
     * Changer le statut en vedette
     */
    public function toggleVedette(Annonce $annonce)
    {
        $annonce->update(['en_vedette' => !$annonce->en_vedette]);

        return redirect()->back()
            ->with('success', 'Statut vedette mis à jour avec succès.');
    }


    /**
     * Supprimer une annonce
     */
    public function destroy(Annonce $annonce)
    {
        //supprimer les médias associés
        $annonce->clearMediaCollection('image_principale');
        $annonce->clearMediaCollection('images');
        $annonce->clearMediaCollection('documents');
        $annonce->delete();

        return response()->json([
            'status' => 200,
        ] , 200);
    }
}
