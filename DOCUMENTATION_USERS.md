# Documentation - Gestion des Utilisateurs avec Spatie

## Modifications apportées

### 1. **Modèle User** (`app/Models/User.php`)

#### Implémentation de Spatie Media Library
- Ajout de l'interface `HasMedia`
- Ajout du trait `InteractsWithMedia`
- Configuration de 3 collections de médias :
  - **avatar** : Photo de profil (1 seul fichier)
  - **piece_identite** : Pièces d'identité (JPG, PNG, PDF)
  - **documents** : Autres documents (JPG, PNG, PDF, DOC, DOCX)

```php
public function registerMediaCollections(): void
{
    $this->addMediaCollection('avatar')->singleFile();
    $this->addMediaCollection('piece_identite')
        ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'application/pdf']);
    $this->addMediaCollection('documents')
        ->acceptsMimeTypes([...]);
}
```

### 2. **Contrôleur UserController** (`app/Http/Controllers/backend/UserController.php`)

#### Utilisation des rôles Spatie
- Remplacement du champ `role` par la relation `roles` de Spatie
- Utilisation de `assignRole()` pour la création
- Utilisation de `syncRoles()` pour la mise à jour

#### Gestion des médias
- Upload d'avatar avec `addMediaFromRequest('avatar')->toMediaCollection('avatar')`
- Upload multiple de pièces d'identité
- Upload multiple de documents
- Suppression de médias spécifiques via `deleteMedia()`

#### Nouvelle méthode
```php
public function deleteMedia(Request $request)
{
    // Supprime un document spécifique via AJAX
}
```

### 3. **Routes** (`routes/web.php`)

Ajout de la route pour supprimer les médias :
```php
route::post('delete-media', 'deleteMedia')->name('backend.users.delete-media');
```

### 4. **Vues**

#### `create.blade.php`
- Sélection de rôle dynamique depuis la base de données (table `roles`)
- Champs d'upload pour :
  - Avatar (fichier unique)
  - Pièces d'identité (multiple)
  - Documents (multiple)

#### `edit.blade.php`
- Affichage des documents existants avec aperçu
- Possibilité de supprimer les documents individuellement (bouton X)
- Ajout de nouveaux documents
- Prévisualisation pour images et icônes pour PDF/DOC

#### `index.blade.php`
- Affichage de l'avatar depuis Spatie Media
- Affichage du rôle depuis la relation `roles`
- Filtrage par rôle (utilise `whereHas('roles')`)

#### `show.blade.php`
- Affichage complet de l'avatar
- Section "Documents" avec :
  - Liste des pièces d'identité
  - Liste des autres documents
  - Aperçu cliquable (miniatures pour images, icônes pour fichiers)
  - Informations de taille de fichier

## Utilisation

### Créer un utilisateur avec documents

```php
$user = User::create([...]);
$user->assignRole($role);

// Avatar
$user->addMediaFromRequest('avatar')->toMediaCollection('avatar');

// Pièces d'identité
foreach ($request->file('piece_identite') as $file) {
    $user->addMedia($file)->toMediaCollection('piece_identite');
}
```

### Récupérer les documents

```php
// Avatar
$avatarUrl = $user->getFirstMediaUrl('avatar');

// Toutes les pièces d'identité
$pieces = $user->getMedia('piece_identite');

// Vérifier si un utilisateur a des documents
if ($user->hasMedia('documents')) {
    // ...
}
```

### Supprimer des documents

```php
// Supprimer toute une collection
$user->clearMediaCollection('avatar');

// Supprimer un média spécifique
$media = Media::find($id);
$media->delete();
```

## Types de fichiers acceptés

### Avatar
- JPG, JPEG, PNG, GIF
- Taille max : 2 MB

### Pièces d'identité
- JPG, JPEG, PNG, PDF
- Taille max : 5 MB par fichier

### Documents
- JPG, JPEG, PNG, PDF, DOC, DOCX
- Taille max : 5 MB par fichier

## Sécurité

- Validation des types MIME côté serveur
- Validation de la taille des fichiers
- Utilisation de Spatie Media Library pour un stockage sécurisé
- Les fichiers sont stockés dans `storage/app/public/media/`

## Points importants

1. **Ne pas utiliser le champ `role`** de la table `users` - utiliser la relation `roles` de Spatie
2. **Ne pas utiliser le champ `avatar`** - utiliser la collection media `'avatar'`
3. Les médias sont automatiquement supprimés quand l'utilisateur est supprimé (via le contrôleur)
4. Tous les rôles doivent exister dans la table `roles` de Spatie
