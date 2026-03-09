<?php

namespace App\Http\Controllers\Backend;

use App\Models\Module;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ModuleController extends Controller
{
    //


    public function index()
    {

        $data_module = Module::get();
        $data_module->sortBy('name');


        // $title = 'Delete User!';
        // $text = "Are you sure you want to delete?";
        // confirmDelete($title, $text);

        return view('backend.pages.module.index', compact('data_module'));
    }


    public function store(Request $request)
    {

        // Créer ou récupérer le module
        $module = Module::firstOrCreate([
            'name' => $request['name'],
        ]);

        // Définir les permissions pour ce module
        $permissions = [
            'creer-' . $module->name,
            'voir-' . $module->name,
            'modifier-' . $module->name,
            'supprimer-' . $module->name,
        ];

        // Créer les permissions et les associer au module
        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate([
                'name' => $permissionName,
                'module_id' => $module->id,  // Associer à un module                
                'guard_name' => 'web',
            ]);
        }

        // // Attribuer les permissions au rôle développeur par défaut
        // $role = Role::where('name', 'developpeur')->first();
        // $role->syncPermissions($permissions);

        Alert::success('Operation réussi', 'Success Message');

        return back();
    }


    public function update(Request $request, $id)
    {
        // Validation de la requête
        $request->validate([
            'name' => 'required|string|max:255|unique:modules,name,' . $id,
        ]);

        // Trouver le module existant
        $module = Module::findOrFail($id);

        // Si le nom du module a changé
        if ($module->name !== $request['name']) {

            // Définir les nouvelles permissions basées sur le nouveau nom
            $permissions = [
                'creer-' . $request['name'],
                'voir-' . $request['name'],
                'modifier-' . $request['name'],
                'supprimer-' . $request['name'],
            ];

            // Récupérer les permissions associées au module
            $existingPermissions = Permission::where('module_id', $module->id)->get();

            // Mettre à jour chaque permission
            foreach ($existingPermissions as $key => $permission) {
                if (isset($permissions[$key])) {
                    // Mettre à jour le nom de la permission
                    $permission->update([
                        'name' => $permissions[$key], // Le nouveau nom de la permission
                        'guard_name' => 'web',        // Garde toujours 'web'
                    ]);
                }
            }

            // Mettre à jour le nom du module
            $module->update([
                'name' => $request['name'],
            ]);
        }



        Alert::success('Opération réussie', 'Le module a été mis à jour avec succès');
        return back();
    }


    public function delete($id)
    {

        //supprimer les permissions associé au module
        Permission::where('module_id', $id)->forceDelete();

        Module::find($id)->forceDelete();
        return response()->json([
            'status' => 200,
        ]);
    }
}

