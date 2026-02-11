<?php

namespace App\Http\Controllers\backend;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    /**
     * Afficher la liste des utilisateurs
     */
    public function index(Request $request)
    {
        $query = User::with('roles')->whereHas('roles', function ($q) {
            $q->whereIn('name', ['locataire', 'proprietaire', 'acheteur' , 'prospect']);
        }); // Exclure l'administrateur principal

        // Filtre par type d'utilisateur (rôle Spatie)
        if ($request->has('role') && $request->role != 'tous') {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Recherche par nom, email ou téléphone
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('username', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        // // Confirmation de suppression
        // $title = 'Suppression d\'utilisateur';
        // $text = "Êtes-vous sûr de vouloir supprimer cet utilisateur ?";
        // confirmDelete($title, $text);


        return view('backend.pages.users.index', compact('users'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        // Récupérer uniquement les rôles [proprietaire, acheteur, locataire]
        $roles = Role::whereIn('name', ['proprietaire', 'acheteur', 'locataire' , 'prospect'])->get();
        return view('backend.pages.users.create', compact('roles'));
    }

    /**
     * Enregistrer un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'piece_identite.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'documents.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:5120'
        ]);

        $data = $request->except(['password', 'password_confirmation', 'role_id', 'avatar', 'piece_identite', 'documents']);
        $data['password'] = Hash::make($request->password);

        $user = User::create($data);

        // Assigner le rôle Spatie
        $role = Role::findById($request->role_id);
        $user->assignRole($role);

        // Gestion de l'avatar avec Spatie Media
        if ($request->hasFile('avatar')) {
            $user->addMediaFromRequest('avatar')
                ->toMediaCollection('avatar');
        }

        // Gestion des pièces d'identité
        if ($request->hasFile('piece_identite')) {
            foreach ($request->file('piece_identite') as $file) {
                $user->addMedia($file)
                    ->toMediaCollection('piece_identite');
            }
        }

        // Gestion des autres documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $user->addMedia($file)
                    ->toMediaCollection('documents');
            }
        }

        // Alert::success('Utilisateur créé avec succès', 'Succès');
        return redirect()->route('backend.users.index')->with('success', 'Utilisateur créé avec succès');
    }

    /**
     * Afficher les détails d'un utilisateur
     */
    public function show(User $user)
    {
        $user->load([
            'roles', 
            'media',
            'ventes.annonce.media',
            'ventes.paiements',
            'locations.annonce.media',
            'locations.echeances',
            'locations.paiements',
            'annonces.media',
            'annonces.typeBien',
            'annonces.locations.locataire',
            'annonces.locations.echeances',
            'annonces.locations.paiements',
            'annonces.ventes.client',
            'annonces.ventes.paiements'
        ]);

        // Calculer les statistiques financières pour les propriétaires
        $statsFinancieres = null;
        if ($user->hasRole('proprietaire') && $user->annonces->count() > 0) {
            $statsFinancieres = [
                // Revenus locatifs
                'revenus_locatifs_total' => $user->annonces->flatMap(function($annonce) {
                    return $annonce->locations->flatMap->paiements;
                })->sum('montant'),
                
                'revenus_locatifs_attendus' => $user->annonces->flatMap(function($annonce) {
                    return $annonce->locations->flatMap->echeances->where('statut', 'en_attente');
                })->sum('montant'),
                
                'nombre_locations_actives' => $user->annonces->flatMap->locations->where('statut', 'en_cours')->count(),
                
                // Revenus de ventes
                'revenus_ventes_total' => $user->annonces->flatMap(function($annonce) {
                    return $annonce->ventes->flatMap->paiements;
                })->sum('montant'),
                
                'nombre_ventes' => $user->annonces->flatMap->ventes->count(),
                
                // Nombre de biens
                'biens_disponibles' => $user->annonces->where('statut', 'disponible')->count(),
                'biens_loues' => $user->annonces->where('statut', 'loue')->count(),
                'biens_vendus' => $user->annonces->where('statut', 'vendu')->count(),
            ];
        }

        return view('backend.pages.users.show', compact('user', 'statsFinancieres'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(User $user)
    {
        $roles = Role::whereIn('name', ['proprietaire', 'acheteur', 'locataire' , 'prospect'])->get();
        $user->load('roles', 'media');
        return view('backend.pages.users.edit', compact('user', 'roles'));
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'piece_identite.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'documents.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:5120'
        ]);

        $data = $request->except(['password', 'password_confirmation', 'role_id', 'avatar', 'piece_identite', 'documents']);

        // Mise à jour du mot de passe uniquement s'il est fourni
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Mettre à jour le rôle Spatie
        $role = Role::findById($request->role_id);
        $user->syncRoles([$role]);

        // Gestion de l'avatar avec Spatie Media
        if ($request->hasFile('avatar')) {
            $user->clearMediaCollection('avatar');
            $user->addMediaFromRequest('avatar')
                ->toMediaCollection('avatar');
        }

        // Gestion des pièces d'identité
        if ($request->hasFile('piece_identite')) {
            foreach ($request->file('piece_identite') as $file) {
                $user->addMedia($file)
                    ->toMediaCollection('piece_identite');
            }
        }

        // Gestion des autres documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $user->addMedia($file)
                    ->toMediaCollection('documents');
            }
        }

        // Alert::success('Utilisateur modifié avec succès', 'Succès');
        return redirect()->route('backend.users.index')->with('success', 'Utilisateur modifié avec succès');
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy(User $user)
    {
        // Supprimer tous les médias
        $user->clearMediaCollection('avatar');
        $user->clearMediaCollection('piece_identite');
        $user->clearMediaCollection('documents');

        $user->delete();

        //utiliser toast
toast('Utilisateur supprimé avec succès','success');


        // return redirect()->route('backend.users.index')->with('success', 'Utilisateur supprimé avec succès');

        // return response()->json(['status' => 200, 'message' => 'Utilisateur supprimé avec succès'], 200);

        // Alert::success('Utilisateur supprimé avec succès', 'Succès');
        // return redirect()->route('backend.users.index');
    }

    /**
     * Supprimer un média spécifique
     */
    public function deleteMedia(Request $request)
    {
        $request->validate([
            'media_id' => 'required|exists:media,id'
        ]);

        $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($request->media_id);
        $media->delete();

        return response()->json(['success' => true, 'message' => 'Document supprimé avec succès']);
    }
}
