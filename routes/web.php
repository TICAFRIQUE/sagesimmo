<?php

use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\AlerteController;
use App\Http\Controllers\Backend\AnnonceController;
// use App\Http\Controllers\Backend\CommandeServiceController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\EquipementController;
use App\Http\Controllers\Backend\ModuleController;
// use App\Http\Controllers\Backend\NewsLettersController;
use App\Http\Controllers\Backend\NotificationController;
use App\Http\Controllers\Backend\ParametreController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\TypeBienController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\frontend\AuthController;
// use App\Http\Controllers\frontend\BaseController;
// use App\Http\Controllers\frontend\HebergementController;
// use App\Http\Controllers\frontend\NomDomaineController;
use App\Http\Controllers\frontend\DashboardClientController;
use App\Http\Controllers\frontend\HomeController;
use App\Http\Controllers\frontend\PropertyController;
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
    Route::post('/{slug}/contact', 'contact')->name('properties.contact');
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

    // Espaces spécifiques par rôle
    Route::get('/proprietaire', 'espaceProprietaire')->name('client.proprietaire');
    Route::get('/proprietaire/locations', 'espaceProprietaireLocations')->name('client.proprietaire.locations');
    Route::get('/proprietaire/ventes', 'espaceProprietaireVentes')->name('client.proprietaire.ventes');
    Route::get('/proprietaire/historique', 'espaceProprietaireHistorique')->name('client.proprietaire.historique');

    Route::get('/locataire', 'espaceLocataire')->name('client.locataire');
    Route::get('/locataire/location/{id}/workflow', 'workflowLocation')->name('client.locataire.workflow');
    Route::get('/locataire/location/{id}/echeances', 'echeancesLocation')->name('client.locataire.echeances');

    Route::get('/acheteur', 'espaceAcheteur')->name('client.acheteur');
    Route::get('/acheteur/vente/{id}/workflow', 'workflowVente')->name('client.acheteur.workflow');
    Route::get('/acheteur/vente/{id}/situation-financiere', 'situationFinanciereVente')->name('client.acheteur.situation-financiere');
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

    // Alertes et retards
    Route::prefix('alertes')->name('backend.alertes.')->controller(AlerteController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('mettre-a-jour', 'mettreAJourStatuts')->name('mettre-a-jour');
    });

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

    // Gestion des notifications
    Route::prefix('notifications')->controller(NotificationController::class)->group(function () {
        route::get('', 'index')->name('backend.notifications.index');
        route::get('{id}/read', 'markAsRead')->name('backend.notifications.read');
        route::post('read-all', 'markAllAsRead')->name('backend.notifications.read-all');
        route::get('unread', 'unread')->name('backend.notifications.unread');
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



    // Gestion des ventes
    Route::prefix('ventes')->controller(\App\Http\Controllers\VenteController::class)->group(function () {
        route::get('', 'index')->name('backend.ventes.index');
        route::get('create', 'create')->name('backend.ventes.create');
        route::post('store', 'store')->name('backend.ventes.store');
        route::get('{vente}', 'show')->name('backend.ventes.show');
        route::get('{vente}/fiche', 'fiche')->name('backend.ventes.fiche');
        route::get('{vente}/edit', 'edit')->name('backend.ventes.edit');
        route::put('{vente}', 'update')->name('backend.ventes.update');
        route::delete('{vente}', 'destroy')->name('backend.ventes.destroy');
        route::post('{vente}/paiement', 'addPaiement')->name('backend.ventes.add-paiement');

        // Actions du workflow
        route::post('{vente}/envoyer-fiche', 'envoyerFiche')->name('backend.ventes.envoyer-fiche');
        route::post('{vente}/confirmer-retour-prospect', 'confirmerRetourProspect')->name('backend.ventes.confirmer-retour-prospect');
        route::post('{vente}/planifier-visite', 'planifierVisite')->name('backend.ventes.planifier-visite');
        route::post('{vente}/visite-effectuee', 'visiteEffectuee')->name('backend.ventes.visite-effectuee');
        route::post('{vente}/configurer-paiement', 'configurerPaiement')->name('backend.ventes.configurer-paiement');
        route::post('{vente}/valider-paiement', 'validerPaiement')->name('backend.ventes.valider-paiement');
        route::post('{vente}/annuler', 'annulerVente')->name('backend.ventes.annuler');
    });

    // Gestion des locations
    Route::prefix('locations')->controller(\App\Http\Controllers\LocationController::class)->group(function () {
        route::get('', 'index')->name('backend.locations.index');
        route::get('create', 'create')->name('backend.locations.create');
        route::post('store', 'store')->name('backend.locations.store');
        route::get('{location}', 'show')->name('backend.locations.show');
        route::get('{location}/edit', 'edit')->name('backend.locations.edit');
        route::put('{location}', 'update')->name('backend.locations.update');
        route::delete('{location}', 'destroy')->name('backend.locations.destroy');

        // Actions du workflow
        route::post('{location}/envoyer-fiche', 'envoyerFiche')->name('backend.locations.envoyer-fiche');
        route::post('{location}/marquer-fiche-envoyee', 'marquerFicheEnvoyee')->name('backend.locations.marquer-fiche-envoyee');
        route::post('{location}/confirmer-retour-prospect', 'confirmerRetourProspect')->name('backend.locations.confirmer-retour-prospect');
        route::post('{location}/planifier-visite', 'planifierVisite')->name('backend.locations.planifier-visite');
        route::post('{location}/visite-effectuee', 'visiteEffectuee')->name('backend.locations.visite-effectuee');
        route::post('{location}/configurer-paiement', 'configurerPaiement')->name('backend.locations.configurer-paiement');
        route::post('{location}/enregistrer-premier-paiement', 'enregistrerPremierPaiement')->name('backend.locations.enregistrer-premier-paiement');
        route::post('{location}/valider-premier-paiement', 'validerPremierPaiement')->name('backend.locations.valider-premier-paiement');
        route::post('echeance/{echeance}/enregistrer-paiement-loyer', 'enregistrerPaiementLoyer')->name('backend.locations.enregistrer-paiement-loyer');
        route::post('{location}/generer-nouvelles-echeances', 'genererNouvellesEcheances')->name('backend.locations.generer-nouvelles-echeances');
        route::post('{location}/resilier', 'resilierLocation')->name('backend.locations.resilier');
        route::get('paiement/{paiement}/recu', 'genererRecuPaiement')->name('backend.locations.recu-paiement');
    });

    // Rapports et statistiques
    Route::prefix('rapports')->group(function () {
        Route::get('commissions', [\App\Http\Controllers\RapportController::class, 'commissions'])->name('backend.rapports.commissions');
        Route::get('statistiques', [\App\Http\Controllers\RapportController::class, 'statistiques'])->name('backend.rapports.statistiques');

        // Rapports propriétaire et agence - ADMIN ONLY
        Route::get('proprietaire/pdf', [\App\Http\Controllers\RapportController::class, 'telechargerRapportProprietaire'])->name('backend.rapports.proprietaire.pdf');
        Route::get('proprietaire/pdf-global', [\App\Http\Controllers\RapportController::class, 'telechargerRapportGlobal'])->name('backend.rapports.proprietaire.pdf.global');
        Route::get('proprietaire', [\App\Http\Controllers\RapportController::class, 'rapportProprietaire'])->name('backend.rapports.proprietaire');
        Route::get('agence', [\App\Http\Controllers\RapportController::class, 'rapportAgence'])->name('backend.rapports.agence');

        // Rapports locataires - ADMIN ONLY
        Route::get('locataire/pdf', [\App\Http\Controllers\RapportController::class, 'telechargerRapportLocataire'])->name('backend.rapports.locataire.pdf');
        Route::get('locataire/pdf-global', [\App\Http\Controllers\RapportController::class, 'telechargerRapportLocataireGlobal'])->name('backend.rapports.locataire.pdf.global');
        Route::get('locataire', [\App\Http\Controllers\RapportController::class, 'rapportLocataire'])->name('backend.rapports.locataire');

        // Rapports acheteurs - ADMIN ONLY
        Route::get('acheteur/pdf', [\App\Http\Controllers\RapportController::class, 'telechargerRapportAcheteur'])->name('backend.rapports.acheteur.pdf');
        Route::get('acheteur/pdf-global', [\App\Http\Controllers\RapportController::class, 'telechargerRapportAcheteurGlobal'])->name('backend.rapports.acheteur.pdf.global');
        Route::get('acheteur', [\App\Http\Controllers\RapportController::class, 'rapportAcheteur'])->name('backend.rapports.acheteur');
    });

    // Gestion des charges - ADMIN ONLY
    Route::prefix('charges')->name('backend.charges.')->group(function () {
        Route::get('/', [\App\Http\Controllers\RapportController::class, 'chargesIndex'])->name('index');
        Route::get('create', [\App\Http\Controllers\RapportController::class, 'chargesCreate'])->name('create');
        Route::post('/', [\App\Http\Controllers\RapportController::class, 'chargesStore'])->name('store');
        Route::get('{charge}/edit', [\App\Http\Controllers\RapportController::class, 'chargesEdit'])->name('edit');
        Route::put('{charge}', [\App\Http\Controllers\RapportController::class, 'chargesUpdate'])->name('update');
        Route::delete('delete/{charge}', [\App\Http\Controllers\RapportController::class, 'chargesDestroy'])->name('destroy');
    });

    // Gestion des versements - ADMIN ONLY
    Route::prefix('versements')->name('backend.versements.')->group(function () {
        Route::get('/', [\App\Http\Controllers\VersementController::class, 'index'])->name('index');
        Route::get('create', [\App\Http\Controllers\VersementController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\VersementController::class, 'store'])->name('store');
        Route::get('{versement}/edit', [\App\Http\Controllers\VersementController::class, 'edit'])->name('edit');
        Route::put('{versement}', [\App\Http\Controllers\VersementController::class, 'update'])->name('update');
        Route::patch('{versement}/cancel', [\App\Http\Controllers\VersementController::class, 'cancel'])->name('cancel');
        Route::delete('{versement}', [\App\Http\Controllers\VersementController::class, 'destroy'])->name('destroy');
    });
});

