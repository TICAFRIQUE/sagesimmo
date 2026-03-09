<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Equipement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EquipementController extends Controller
{
    /**
     * Afficher la liste des équipements
     */
    public function index()
    {
        $equipements = Equipement::ordered()->get();
        return view('backend.pages.equipements.index', compact('equipements'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('backend.pages.equipements.create');
    }

    /**
     * Enregistrer un nouveau équipement
     */
    public function store(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:equipements,nom',
            'description' => 'nullable|string',
            'icone' => 'nullable|string|max:255',
            'ordre' => 'nullable|integer|min:0',
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'nom.unique' => 'Cet équipement existe déjà.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

       try {
        $data = $request->all();
        $data['actif'] = $request->has('actif') ? 1 : 0;

        Equipement::create($data);

        return redirect()->route('backend.equipements.index')
            ->with('success', 'Équipement créé avec succès.');
       } catch (\Throwable $th) {
        $th->getMessage();
       }
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit($id)
    {
        $equipement = Equipement::findOrFail($id);
        return view('backend.pages.equipements.edit', compact('equipement'));
    }

    /**
     * Mettre à jour un équipement
     */
    public function update(Request $request, $id)
    {
        $equipement = Equipement::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:equipements,nom,' . $id,
            'description' => 'nullable|string',
            'icone' => 'nullable|string|max:255',
            'ordre' => 'nullable|integer|min:0',
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'nom.unique' => 'Cet équipement existe déjà.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['actif'] = $request->has('actif') ? 1 : 0;

        $equipement->update($data);

        return redirect()->route('backend.equipements.index')
            ->with('success', 'Équipement mis à jour avec succès.');
    }

    /**
     * Supprimer un équipement
     */
    public function destroy($id)
    {
        $equipement = Equipement::findOrFail($id);
        $equipement->delete();

       //return response
       return response()->json([
        'status' => 200,
        'message' => 'Équipement supprimé avec succès.'
       ] , 200);
    }
}
