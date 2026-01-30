<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\TypeBien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeBienController extends Controller
{
    /**
     * Afficher la liste des types de biens
     */
    public function index()
    {
        $typeBiens = TypeBien::ordered()->get();
        return view('backend.pages.type-biens.index', compact('typeBiens'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('backend.pages.type-biens.create');
    }

    /**
     * Enregistrer un nouveau type de bien
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:type_biens,nom',
            'description' => 'nullable|string',
            'icone' => 'nullable|string|max:255',
            'ordre' => 'nullable|integer|min:0',
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'nom.unique' => 'Ce type de bien existe déjà.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['actif'] = $request->has('actif') ? 1 : 0;
        
        TypeBien::create($data);

        return redirect()->route('backend.type-biens.index')
            ->with('success', 'Type de bien créé avec succès.');
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit($id)
    {
        $typeBien = TypeBien::findOrFail($id);
        return view('backend.pages.type-biens.edit', compact('typeBien'));
    }

    /**
     * Mettre à jour un type de bien
     */
    public function update(Request $request, $id)
    {
        $typeBien = TypeBien::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:type_biens,nom,' . $id,
            'description' => 'nullable|string',
            'icone' => 'nullable|string|max:255',
            'ordre' => 'nullable|integer|min:0',
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'nom.unique' => 'Ce type de bien existe déjà.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['actif'] = $request->has('actif') ? 1 : 0;
        
        $typeBien->update($data);

        return redirect()->route('backend.type-biens.index')
            ->with('success', 'Type de bien mis à jour avec succès.');
    }

    /**
     * Supprimer un type de bien
     */
    public function destroy($id)
    {
        $typeBien = TypeBien::findOrFail($id);
        
        // Vérifier s'il y a des annonces liées
        if ($typeBien->annonces()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer ce type de bien car il est utilisé dans des annonces.');
        }

        $typeBien->delete();

        return response()->json([
            'status' => 200,
        ] , 200);
    }

    /**
     * Obtenir les communes d'une ville (AJAX)
     */
    public function getCommunes(Request $request)
    {
        $ville = $request->input('ville');
        $villes = config('ville-commune');
        
        if (isset($villes[$ville])) {
            return response()->json([
                'success' => true,
                'communes' => $villes[$ville]
            ]);
        }
        
        return response()->json([
            'success' => false,
            'communes' => []
        ]);
    }
}
