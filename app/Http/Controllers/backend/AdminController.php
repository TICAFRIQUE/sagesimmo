<?php

namespace App\Http\Controllers\backend;

use App\Models\User;
use App\Models\Caisse;
use App\Models\Setting;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Models\HistoriqueCaisse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use RealRashid\SweetAlert\Facades\Alert;

class AdminController extends Controller
{
    /**
     * Afficher le formulaire de connexion ou traiter la connexion
     */
    public function login(Request $request)
    {
        if (request()->method() == 'GET') {
            return view('backend.pages.auth-admin.login');
        } elseif (request()->method() == 'POST') {
            $credentials = $request->validate([
                'email' => ['required',],
                'password' => ['required'],
            ]);
            
            if (Auth::attempt($credentials)) {
                Alert::success('Connexion réussie, Bienvenue ' . Auth::user()->username, 'Succès');
                return redirect()->route('dashboard.index');
            } else {
                return back()->withError('Email ou mot de passe incorrect');
            }
        }
    }

    /**
     * Déconnexion de l'administrateur
     */
    public function logout(Request $request)
    {
        Auth::logout();
        Alert::success('Vous êtes déconnecté', 'Succès');
        return redirect()->route('admin.login');
    }

    /**
     * Liste des utilisateurs administrateurs
     */
    public function index()
    {
        $data_role = Role::whereNotIn('name', ['client', 'proprietaire', 'acheteur', 'locataire'])
            ->orderBy('name')
            ->get();

        $data_admin = User::with(['roles', 'media'])
            ->whereHas('roles', function ($query) {
                $query->whereNotIn('name', ['client', 'proprietaire', 'acheteur', 'locataire']);
            })
            ->latest()
            ->get();

        return view('backend.pages.auth-admin.register.index', compact('data_admin', 'data_role'));
    }

    /**
     * Afficher le formulaire de création d'un administrateur
     */
    public function create()
    {
        $data_role = Role::whereNotIn('name', ['client', 'proprietaire', 'acheteur', 'locataire'])
            ->orderBy('name')
            ->get();

        return view('backend.pages.auth-admin.register.create', compact('data_role'));
    }

    /**
     * Créer un nouvel administrateur
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'username' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'nullable|string|unique:users,phone',
                'role' => 'required|exists:roles,name',
                'password' => 'required|string|min:6|confirmed',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'username.required' => 'Le nom est requis',
                'email.required' => 'L\'email est requis',
                'email.unique' => 'Cet email est déjà utilisé',
                'phone.unique' => 'Ce numéro de téléphone est déjà utilisé',
                'role.required' => 'Le rôle est requis',
                'role.exists' => 'Le rôle sélectionné n\'existe pas',
                'password.required' => 'Le mot de passe est requis',
                'password.min' => 'Le mot de passe doit contenir au moins 6 caractères',
                'password.confirmed' => 'La confirmation du mot de passe ne correspond pas',
                'avatar.image' => 'Le fichier doit être une image',
                'avatar.max' => 'L\'image ne doit pas dépasser 2 Mo',
            ]);

            // Créer l'utilisateur
            $data_user = User::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'], // Stocker le nom du rôle pour une référence rapide
            ]);

            // Assigner le rôle
            $data_user->assignRole($validated['role']);

            // Gestion de l'avatar avec Spatie Media
            if ($request->hasFile('avatar')) {
                $data_user->addMediaFromRequest('avatar')
                    ->toMediaCollection('avatar');
            }

            Alert::success('Administrateur créé avec succès', 'Succès');
            return redirect()->route('admin-register.index');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Alert::error('Erreur de validation', implode(', ', $e->validator->errors()->all()));
            return back()->withInput()->withErrors($e->validator);
        } catch (\Exception $e) {
            Alert::error('Une erreur est survenue lors de la création', $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Afficher le formulaire d'édition d'un administrateur
     */
    public function edit($id)
    {
        $data_admin = User::with(['roles', 'media'])->findOrFail($id);
        
        $data_role = Role::whereNotIn('name', ['client', 'proprietaire', 'acheteur', 'locataire'])
            ->orderBy('name')
            ->get();

        return view('backend.pages.auth-admin.register.edit', compact('data_admin', 'data_role'));
    }

    /**
     * Mettre à jour un administrateur
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'username' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'phone' => 'nullable|string|unique:users,phone,' . $id,
                'role' => 'required|exists:roles,name',
                'password' => 'nullable|string|min:6|confirmed',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'username.required' => 'Le nom est requis',
                'email.required' => 'L\'email est requis',
                'email.unique' => 'Cet email est déjà utilisé',
                'phone.unique' => 'Ce numéro de téléphone est déjà utilisé',
                'role.required' => 'Le rôle est requis',
                'role.exists' => 'Le rôle sélectionné n\'existe pas',
                'password.min' => 'Le mot de passe doit contenir au moins 6 caractères',
                'password.confirmed' => 'La confirmation du mot de passe ne correspond pas',
                'avatar.image' => 'Le fichier doit être une image',
                'avatar.max' => 'L\'image ne doit pas dépasser 2 Mo',
            ]);

            // Mettre à jour les informations
            $updateData = [
                'username' => $validated['username'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'role' => $validated['role'], // Mettre à jour le nom du rôle pour une référence rapide
            ];

            // Mise à jour du mot de passe si fourni
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            // Synchroniser le rôle
            $user->syncRoles([$validated['role']]);

            // Gestion de l'avatar avec Spatie Media
            if ($request->hasFile('avatar')) {
                // Supprimer l'ancien avatar
                $user->clearMediaCollection('avatar');
                // Ajouter le nouveau
                $user->addMediaFromRequest('avatar')
                    ->toMediaCollection('avatar');
            }

            Alert::success('Administrateur mis à jour avec succès', 'Succès');
            return redirect()->route('admin-register.index');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Alert::error('Erreur de validation', implode(', ', $e->validator->errors()->all()));
            return back()->withInput()->withErrors($e->validator);
        } catch (\Exception $e) {
            Alert::error('Une erreur est survenue lors de la mise à jour', $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Supprimer un administrateur
     */
    public function delete($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Empêcher la suppression de son propre compte
            if ($user->id === Auth::id()) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Vous ne pouvez pas supprimer votre propre compte',
                ], 403);
            }

            // Supprimer tous les médias associés
            $user->clearMediaCollection('avatar');
            
            // Supprimer l'utilisateur
            $user->forceDelete();
            
            return response()->json([
                'status' => 200,
                'message' => 'Administrateur supprimé avec succès',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Erreur lors de la suppression : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Afficher le profil d'un administrateur
     */
    public function profil($id)
    {
        $data_admin = User::with(['roles', 'media'])->findOrFail($id);
        $data_role = Role::whereNotIn('name', ['client', 'proprietaire', 'acheteur', 'locataire'])
            ->orderBy('name')
            ->get();
            
        return view('backend.pages.auth-admin.register.profil', compact('data_admin', 'data_role'));
    }

    /**
     * Changer le mot de passe de l'utilisateur connecté
     */
    public function changePassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'old_password' => 'required|string',
                'new_password' => 'required|string|min:6',
            ], [
                'old_password.required' => 'L\'ancien mot de passe est requis',
                'new_password.required' => 'Le nouveau mot de passe est requis',
                'new_password.min' => 'Le nouveau mot de passe doit contenir au moins 6 caractères',
                // 'new_password.confirmed' => 'La confirmation du mot de passe ne correspond pas',
            ]);

            $user = Auth::id() ? User::findOrFail(Auth::id()) : null;

            // Vérifier l'ancien mot de passe
            if (!Hash::check($validated['old_password'], $user->password)) {
                Alert::error('L\'ancien mot de passe est incorrect', 'Erreur');
                return back();
            }

            // Vérifier que le nouveau mot de passe est différent
            if (Hash::check($validated['new_password'], $user->password)) {
                Alert::warning('Le nouveau mot de passe doit être différent de l\'ancien', 'Attention');
                return back();
            }

            // Mettre à jour le mot de passe
            $user->update([
                'password' => Hash::make($validated['new_password'])
            ]);

            Alert::success('Mot de passe modifié avec succès', 'Succès');
            return back();
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Alert::error('Erreur de validation', implode(', ', $e->validator->errors()->all()));
            return back()->withErrors($e->validator);
        } catch (\Exception $e) {
            Alert::error('Une erreur est survenue', $e->getMessage());
            return back();
        }
    }
}
