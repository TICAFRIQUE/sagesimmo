<?php

use App\Http\Controllers\backend\AdminController;
use App\Http\Controllers\backend\AnnonceController;
use App\Http\Controllers\backend\DashboardController;
use App\Http\Controllers\backend\EquipementController;
use App\Http\Controllers\backend\ModuleController;
use App\Http\Controllers\backend\NewsLettersController;
use App\Http\Controllers\backend\ParametreController;
use App\Http\Controllers\backend\PermissionController;
use App\Http\Controllers\backend\RoleController;
use App\Http\Controllers\backend\TypeBienController;
use App\Http\Controllers\backend\CommandeServiceController;
use App\Http\Controllers\backend\UserController;
use App\Http\Controllers\backend\DemandeInteretController;
use App\Http\Controllers\frontend\BaseController;
use App\Http\Controllers\frontend\HebergementController;
use App\Http\Controllers\frontend\NomDomaineController;use App\Http\Controllers\frontend\DashboardClientController;use App\Http\Controllers\frontend\HomeController;
use App\Http\Controllers\frontend\PropertyController;
use App\Http\Controllers\frontend\AuthController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/

// Page d'accueil
Route::get('/', [HomeController::class, 'index'])->name('home');

// Recherche rapide
Route::get('/search', [HomeController::class, 'search'])->name('search');

// Biens immobiliers
Route::prefix('biens')->controller(PropertyController::class)->group(function () {
    Route::get('/', 'index')->name('properties.index');
    Route::get('/{slug}', 'show')->name('properties.show');
    Route::post('/{slug}/contact', 'contact')->name('properties.contact')->middleware('auth');
});

// Authentification
Route::controller(AuthController::class)->group(function () {
    // Connexion
    Route::get('/connexion', 'showLoginForm')->name('login')->middleware('guest');
    Route::post('/connexion', 'login')->middleware('guest');
    
    // Inscription
    Route::get('/inscription', 'showRegisterForm')->name('register')->middleware('guest');
    Route::post('/inscription', 'register')->middleware('guest');
    
    // Déconnexion
    Route::post('/deconnexion', 'logout')->name('logout')->middleware('auth');
});

// Espace Client (Dashboard)
Route::middleware(['auth'])->prefix('mon-espace')->controller(DashboardClientController::class)->group(function () {
    Route::get('/', 'index')->name('client.dashboard');
    Route::get('/profil', 'profil')->name('client.profil');
    Route::put('/profil', 'updateProfil')->name('client.profil.update');
    Route::post('/profil/change-password', 'changePassword')->name('client.profil.change-password');
    Route::get('/demandes', 'demandes')->name('client.demandes');
    Route::get('/demandes/{id}', 'showDemande')->name('client.demandes.show');
    Route::delete('/demandes/{id}', 'cancelDemande')->name('client.demandes.cancel');
    Route::post('/demandes/{id}/upload-documents', 'uploadDocuments')->name('client.demandes.upload-documents');
});


/*
|--------------------------------------------------------------------------
| Backend Routes
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return view('backend.utility.auth-404-basic');
});

Route::middleware(['admin'])->prefix('admin')->group(function () {

    // login and logout
    Route::controller(AdminController::class)->group(function () {
        route::get('/login', 'login')->name('admin.login')->withoutMiddleware('admin'); // page formulaire de connexion
        route::post('/login', 'login')->name('admin.login')->withoutMiddleware('admin'); // envoi du formulaire
        route::post('/logout', 'logout')->name('admin.logout');
    });



    // dashboard admin
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

    // parametre application
    Route::prefix('parametre')->controller(ParametreController::class)->group(function () {
        route::get('', 'index')->name('parametre.index');
        route::post('store', 'store')->name('parametre.store');
        route::get('maintenance-up', 'maintenanceUp')->name('parametre.maintenance-up');
        route::get('maintenance-down', 'maintenanceDown')->name('parametre.maintenance-down');
        route::get('optimize-clear', 'optimizeClear')->name('parametre.optimize-clear');
        Route::get('download-backup/{file}', 'downloadBackup')->name('setting.download-backup');  // download backup db
    });


    //register admin
    Route::prefix('register')->controller(AdminController::class)->group(function () {
        route::get('', 'index')->name('admin-register.index');
        route::get('create', 'create')->name('admin-register.create');
        route::post('store', 'store')->name('admin-register.store');
        route::get('edit/{id}', 'edit')->name('admin-register.edit');
        route::post('update/{id}', 'update')->name('admin-register.update');
        route::delete('delete/{id}', 'delete')->name('admin-register.delete');
        route::get('profil/{id}', 'profil')->name('admin-register.profil');
        route::post('change-password', 'changePassword')->name('admin-register.new-password');
    });

    //role
    Route::prefix('role')->controller(RoleController::class)->group(function () {
        route::get('', 'index')->name('role.index');
        route::post('store', 'store')->name('role.store');
        route::post('update/{id}', 'update')->name('role.update');
        route::delete('delete/{id}', 'delete')->name('role.delete');
    });

    //permission
    Route::prefix('permission')->controller(PermissionController::class)->group(function () {
        route::get('', 'index')->name('permission.index');
        route::get('create', 'create')->name('permission.create');
        route::post('store', 'store')->name('permission.store');
        route::get('edit{id}', 'edit')->name('permission.edit');
        route::put('update/{id}', 'update')->name('permission.update');
        route::delete('delete/{id}', 'delete')->name('permission.delete');
    });

    //module
    Route::prefix('module')->controller(ModuleController::class)->group(function () {
        route::get('', 'index')->name('module.index');
        route::post('store', 'store')->name('module.store');
        route::post('update/{id}', 'update')->name('module.update');
        route::delete('delete/{id}', 'delete')->name('module.delete');
    });

    // Gestion des annonces immobilières
    Route::prefix('annonces')->controller(AnnonceController::class)->group(function () {
        route::get('', 'index')->name('backend.annonces.index');
        route::get('create', 'create')->name('backend.annonces.create');
        route::post('store', 'store')->name('backend.annonces.store');
        route::get('{annonce}', 'show')->name('backend.annonces.show');
        route::get('{annonce}/edit', 'edit')->name('backend.annonces.edit');
        route::put('{annonce}', 'update')->name('backend.annonces.update');
        route::delete('delete/{annonce}', 'destroy')->name('backend.annonces.destroy');
        route::post('delete-image', 'deleteImage')->name('backend.annonces.delete-image');
        route::post('{annonce}/toggle-vedette', 'toggleVedette')->name('backend.annonces.toggle-vedette');
    });

    // Gestion des types de biens
    Route::prefix('type-biens')->controller(TypeBienController::class)->group(function () {
        route::get('', 'index')->name('backend.type-biens.index');
        route::get('create', 'create')->name('backend.type-biens.create');
        route::post('store', 'store')->name('backend.type-biens.store');
        route::get('edit/{id}', 'edit')->name('backend.type-biens.edit');
        route::put('update/{id}', 'update')->name('backend.type-biens.update');
        route::delete('delete/{id}', 'destroy')->name('backend.type-biens.destroy');
        route::post('get-communes', 'getCommunes')->name('backend.type-biens.get-communes');
    });

    // Gestion des équipements
    Route::prefix('equipements')->controller(EquipementController::class)->group(function () {
        route::get('', 'index')->name('backend.equipements.index');
        route::get('create', 'create')->name('backend.equipements.create');
        route::post('store', 'store')->name('backend.equipements.store');
        route::get('edit/{id}', 'edit')->name('backend.equipements.edit');
        route::put('update/{id}', 'update')->name('backend.equipements.update');
        route::delete('delete/{id}', 'destroy')->name('backend.equipements.destroy');
    });

    // Gestion des utilisateurs (locataires, propriétaires, acheteurs)
    Route::prefix('users')->controller(UserController::class)->group(function () {
        route::get('', 'index')->name('backend.users.index');
        route::get('create', 'create')->name('backend.users.create');
        route::post('store', 'store')->name('backend.users.store');
        route::get('{user}', 'show')->name('backend.users.show');
        route::get('{user}/edit', 'edit')->name('backend.users.edit');
        route::put('{user}', 'update')->name('backend.users.update');
        route::delete('delete/{user}', 'destroy')->name('backend.users.destroy');
        route::post('delete-media', 'deleteMedia')->name('backend.users.delete-media');
    });

    // Gestion des demandes d'intérêt
    Route::prefix('demandes')->controller(DemandeInteretController::class)->group(function () {
        route::get('', 'index')->name('backend.demandes.index');
        route::get('{id}', 'show')->name('backend.demandes.show');
        route::post('{id}/planifier-visite', 'planifierVisite')->name('backend.demandes.planifier-visite');
        route::post('{id}/visite-effectuee', 'visiteEffectuee')->name('backend.demandes.visite-effectuee');
        route::post('{id}/demander-pieces', 'demanderPieces')->name('backend.demandes.demander-pieces');
        route::post('{id}/documents-recus', 'documentsRecus')->name('backend.demandes.documents-recus');
        route::post('{id}/valider-dossier', 'validerDossier')->name('backend.demandes.valider-dossier');
        route::post('{id}/refuser-dossier', 'refuserDossier')->name('backend.demandes.refuser-dossier');
        route::post('{id}/generer-contrat', 'genererContrat')->name('backend.demandes.generer-contrat');
        route::post('{id}/configurer-paiement', 'configurerPaiement')->name('backend.demandes.configurer-paiement');
        route::post('{id}/valider-paiement', 'validerPaiement')->name('backend.demandes.valider-paiement');
        route::post('{id}/changer-statut', 'changerStatut')->name('backend.demandes.changer-statut');
        route::post('{id}/update-note', 'updateNote')->name('backend.demandes.update-note');
        route::delete('{id}', 'destroy')->name('backend.demandes.destroy');
    });

    // Gestion des ventes
    Route::prefix('ventes')->controller(\App\Http\Controllers\VenteController::class)->group(function () {
        route::get('', 'index')->name('backend.ventes.index');
        route::get('create', 'create')->name('backend.ventes.create');
        route::post('store', 'store')->name('backend.ventes.store');
        route::get('demande/{demande}/create', 'createFromDemande')->name('backend.ventes.create-from-demande');
        route::post('demande/{demande}/store', 'storeFromDemande')->name('backend.ventes.store-from-demande');
        route::get('{vente}', 'show')->name('backend.ventes.show');
        route::get('{vente}/edit', 'edit')->name('backend.ventes.edit');
        route::put('{vente}', 'update')->name('backend.ventes.update');
        route::delete('{vente}', 'destroy')->name('backend.ventes.destroy');
        route::post('{vente}/paiement', 'addPaiement')->name('backend.ventes.add-paiement');
    });

    // Gestion des locations
    Route::prefix('locations')->controller(\App\Http\Controllers\LocationController::class)->group(function () {
        route::get('', 'index')->name('backend.locations.index');
        route::get('create', 'create')->name('backend.locations.create');
        route::post('store', 'store')->name('backend.locations.store');
        route::get('demande/{demande}/create', 'createFromDemande')->name('backend.locations.create-from-demande');
        route::post('demande/{demande}/store', 'storeFromDemande')->name('backend.locations.store-from-demande');
        route::get('{location}', 'show')->name('backend.locations.show');
        route::get('{location}/edit', 'edit')->name('backend.locations.edit');
        route::put('{location}', 'update')->name('backend.locations.update');
        route::delete('{location}', 'destroy')->name('backend.locations.destroy');
        route::post('{location}/paiement', 'addPaiement')->name('backend.locations.add-paiement');
        route::put('echeance/{echeance}', 'updateEcheance')->name('backend.locations.update-echeance');
    });
});
