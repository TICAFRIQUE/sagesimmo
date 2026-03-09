<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    //

    /**
     * Affiche la liste des rôles avec leurs permissions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name', 'ASC')->get();
        $modules_with_permissions = Module::with('permissions')->get();
        $modules_with_permissions =  $modules_with_permissions->sortBy('name');


        return view('backend.pages.role-permission.index', compact('roles', 'modules_with_permissions'));
    }

    /**
     * Affiche le formulaire de création d'un rôle.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        // Récupérer l'utilisateur connecté
        $user = Auth::user();
        $userRole = $user->roles->first(); // Supposons que l'utilisateur n'a qu'un seul rôle
        $userPermissions = $userRole ? $userRole->permissions : collect();
        // Vous pouvez passer ces permissions à la vue si nécessaire
        $modules_with_permissions = Module::with('permissions')->orderBy('name', 'ASC')->get();

        return view('backend.pages.role-permission.create', compact('modules_with_permissions'));
    }



    public function store(Request $request)
    {
        try {
            // dd($request->toArray());
            // Valider la requête pour s'assurer que le nom du rôle et les permissions sont fournis
            $request->validate([
                'name' => 'required|string|max:255',
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,id|min:1', // Vérifie que chaque permission existe dans la base de données
            ]);

            // Créer ou récupérer le rôle
            $role = Role::firstOrCreate([
                'name' => $request->input('name'),
                'guard_name' => 'web',
            ]);

            // Assigner les permissions au rôle
            if (!empty($request->input('permissions'))) {
                // Récupérer les permissions par ID
                $permissions = Permission::whereIn('id', $request->input('permissions'))->get();

                // Assigner les permissions récupérées au rôle
                $role->syncPermissions($permissions);
            }

            Alert::success('Operation réussi', 'Success Message');

            return redirect()->route('permission.index');
            return back();
        } catch (\Throwable $e) {

            return $e->getMessage();
        }
    }


    /**
     * Affiche le formulaire de modification d'un rôle
     *
     * @param int $id L'ID du rôle à modifier
     *
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        try {
            // Récupérer le rôle avec ses permissions
            $role = Role::with('permissions')->findOrFail($id);

            // Récupérer tous les modules avec leurs permissions
            $modules_with_permissions = Module::with('permissions')->orderBy('name', 'ASC')->get();

            // Retourner la vue avec les données
            return view('backend.pages.role-permission.edit', compact('role', 'modules_with_permissions'));
        } catch (\Exception $e) {
            Alert::error('Erreur', 'Une erreur est survenue lors de la récupération du rôle.');
            return back();
        }
    }



    /**
     * Met à jour un rôle et ses permissions
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            // Valider les données du formulaire
            $request->validate([
                'name' => 'required|string|max:255',
                'permissions' => 'array'
            ]);

            // Récupérer le rôle
            $role = Role::findOrFail($id);

            // Mettre à jour le nom du rôle
            $role->name = $request->input('name');
            $role->save();

            // Mettre à jour les permissions
            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->input('permissions'))->get();
                $role->permissions()->sync($permissions->pluck('id'));
                
            } else {
                // Si aucune permission n'est sélectionnée, on retire toutes les permissions
                $role->permissions()->detach();
            }

            Alert::success('Opération réussie', 'Le rôle et ses permissions ont été mis à jour avec succès.');
            return redirect()->route('permission.index');
        } catch (\Exception $e) {
            Alert::error('Erreur', 'Une erreur est survenue lors de la mise à jour du rôle et de ses permissions.');
            return back()->withInput();
        }
    }



    /**
     * Supprime un rôle et ses permissions
     *
     * @param  int  $id L'ID du role à supprimer
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function delete($id)
    {
        try {
            // Récupérer le rôle
            $role = Role::findOrFail($id);

            // Supprimer toutes les permissions associées au rôle
            $role->syncPermissions([]);

            // Supprimer le rôle
            $role->delete();

            return response()->json([
                'status' => 200,
            ]);// Retourne un code HTTP 200 (OK)

            // Alert::success('Opération réussie', 'Le rôle et ses permissions ont été supprimés avec succès.');
            // return redirect()->route('permission.index');
        } catch (\Exception $e) {
            Alert::error('Erreur', 'Une erreur est survenue lors de la suppression du rôle et de ses permissions.');
            return back();
        }
    }
}

