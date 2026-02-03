# Documentation de l'Implémentation du Workflow Intégré

## 📋 Vue d'ensemble

Le workflow de gestion des demandes d'intérêt a été **complètement intégré** directement dans les modules **Ventes** et **Locations**. Les clients manifestent désormais leur intérêt directement, et l'ensemble du processus (de la demande initiale à la remise des clés) est géré dans un seul module.

## 🎯 Objectifs Atteints

✅ Suppression du module DemandeInteret comme module séparé  
✅ Intégration complète du workflow dans Ventes et Locations  
✅ Interface utilisateur moderne avec barre de progression  
✅ 5 étapes clairement définies du workflow  
✅ Actions spécifiques à chaque étape  
✅ Génération automatique des échéances pour les locations  

## 📊 Statuts du Workflow

### Pour les VENTES
1. **demande_client** - Client a manifesté son intérêt (0%)
2. **fiche_envoyee** - Fiche envoyée au client par email (20%)
3. **visite_planifiee** - Visite planifiée (40%)
4. **en_attente_paiement** - En attente du paiement (60%)
5. **paiement_valide** - Paiement validé, vente finalisée (100%)
6. **annule** - Vente annulée

### Pour les LOCATIONS
1. **demande_client** - Client a manifesté son intérêt (0%)
2. **fiche_envoyee** - Fiche envoyée au client par email (17%)
3. **visite_planifiee** - Visite planifiée (33%)
4. **en_attente_paiement** - En attente du paiement (50%)
5. **actif** - Location active (100%)
6. **termine** - Location terminée normalement
7. **resilie** - Location résiliée

## 🔧 Modifications Techniques

### 1. Base de Données

#### Migration: `2026_02_02_150000_add_workflow_to_ventes_locations.php`

Ajout de champs dans les tables `ventes` et `locations` :
- `message_client` - Message initial du client
- `date_visite` - Date/heure de visite planifiée
- `compte_rendu_visite` - Compte rendu après la visite
- `note_admin` - Notes internes de l'administrateur
- `montant_caution` - Montant de la caution
- `montant_frais_agence` - Frais d'agence
- `date_finalisation` - Date de finalisation
- Modification des ENUM `statut` pour inclure les nouveaux statuts

### 2. Models

#### `app/Models/Vente.php`
```php
// Nouveaux champs fillable
'message_client', 'date_visite', 'compte_rendu_visite', 
'note_admin', 'montant_caution', 'montant_frais_agence', 
'date_finalisation'

// Attributs calculés
- getStatutBadgeAttribute() : Retourne le badge HTML selon le statut
- getProgressionAttribute() : Retourne le % de progression (0-100)
```

#### `app/Models/Location.php`
```php
// Même structure que Vente avec statuts adaptés
- Génération automatique des échéances lors de l'activation
```

### 3. Controllers

#### `app/Http/Controllers/VenteController.php`
Actions ajoutées :
- `envoyerFiche()` - Envoie la fiche au client, passe à "fiche_envoyee"
- `planifierVisite()` - Planifie la visite, passe à "visite_planifiee"
- `visiteEffectuee()` - Enregistre le compte rendu de visite
  - Si client intéressé : continue le workflow
  - Si client non intéressé : annule la vente
- `configurerPaiement()` - Configure les montants, passe à "en_attente_paiement"
- `validerPaiement()` - Valide le paiement, finalise la vente
  - Marque le bien comme "vendu"
  - Dépublie l'annonce
  - Enregistre date_finalisation
- `annulerVente()` - Annule la vente à tout moment

#### `app/Http/Controllers/LocationController.php`
Actions similaires avec spécificités :
- `envoyerFiche()`
- `planifierVisite()`
- `visiteEffectuee()`
- `configurerPaiement()` - Inclut loyer, caution, période, jour de paiement
- `validerPaiement()` - Active la location
  - Marque le bien comme "loué"
  - Dépublie l'annonce
  - **Génère automatiquement les échéances** via `genererEcheances()`
- `resilierLocation()` - Résilie la location

#### `app/Http/Controllers/frontend/PropertyController.php`
Méthode `contact()` modifiée :
```php
// Création directe de Vente ou Location selon type_transaction
if ($request->type_transaction === 'Vente') {
    Vente::create([
        'annonce_id' => $annonce->id,
        'client_id' => auth()->id(),
        'prix_vente' => $annonce->prix,
        'statut' => 'demande_client',
        'message_client' => $request->message,
    ]);
} else {
    Location::create([...]);
}
```

### 4. Routes

#### `routes/web.php`
Routes ajoutées pour les actions du workflow :

**Ventes :**
```php
Route::post('{vente}/envoyer-fiche', 'envoyerFiche')->name('backend.ventes.envoyer-fiche');
Route::post('{vente}/planifier-visite', 'planifierVisite')->name('backend.ventes.planifier-visite');
Route::post('{vente}/visite-effectuee', 'visiteEffectuee')->name('backend.ventes.visite-effectuee');
Route::post('{vente}/configurer-paiement', 'configurerPaiement')->name('backend.ventes.configurer-paiement');
Route::post('{vente}/valider-paiement', 'validerPaiement')->name('backend.ventes.valider-paiement');
Route::post('{vente}/annuler', 'annulerVente')->name('backend.ventes.annuler');
```

**Locations :**
```php
Route::post('{location}/envoyer-fiche', 'envoyerFiche')->name('backend.locations.envoyer-fiche');
Route::post('{location}/planifier-visite', 'planifierVisite')->name('backend.locations.planifier-visite');
Route::post('{location}/visite-effectuee', 'visiteEffectuee')->name('backend.locations.visite-effectuee');
Route::post('{location}/configurer-paiement', 'configurerPaiement')->name('backend.locations.configurer-paiement');
Route::post('{location}/valider-paiement', 'validerPaiement')->name('backend.locations.valider-paiement');
Route::post('{location}/resilier', 'resilierLocation')->name('backend.locations.resilier');
```

### 5. Vues

#### `resources/views/backend/pages/ventes/show.blade.php`
Nouvelle interface comprenant :

**Section Progression :**
- Barre de progression visuelle (0-100%)
- Badge de statut coloré
- Date de finalisation (si complété)

**Section Workflow :**
- Affichage des 5 étapes avec état (complété/actif/en attente)
- Message initial du client
- Boutons d'action contextuels selon l'étape
- Icônes de validation pour les étapes complétées
- Affichage des informations saisies (date visite, compte rendu, montants)

**Modals :**
1. `envoyerFicheModal` - Formulaire d'envoi de fiche
2. `planifierVisiteModal` - Formulaire de planification (date/heure)
3. `visiteEffectueeModal` - Formulaire de compte rendu (client intéressé ?)
4. `configurerPaiementModal` - Configuration des montants (prix, caution, frais)
5. `validerPaiementModal` - Confirmation finale de paiement
6. `annulerVenteModal` - Annulation avec raison

**Sidebar :**
- Informations du bien (image, titre, localisation)
- Informations du client (nom, email, téléphone)
- Actions (modifier, supprimer)

#### `resources/views/backend/pages/locations/show.blade.php`
Structure similaire avec adaptations :
- Tableau des échéances de paiement (si actif)
- Modal de résiliation (au lieu d'annulation)
- Configuration paiement incluant : loyer, nombre de cautions, date début/fin, jour de paiement

## 🎨 Design et UX

### Barre de Progression
```css
.progress-bar-custom {
    height: 25px;
    font-size: 14px;
}
```

### Étapes du Workflow
```css
.workflow-step {
    padding: 15px;
    border-left: 4px solid #e9ecef;
    margin-bottom: 15px;
    border-radius: 4px;
    background: #fff;
}
.workflow-step.active {
    border-left-color: #0ab39c;  /* Vert - Étape en cours */
    background: #f0fdf4;
}
.workflow-step.completed {
    border-left-color: #299cdb;  /* Bleu - Étape complétée */
    background: #f0f9ff;
}
```

### Badges de Statut
- `demande_client` : Badge warning (jaune)
- `fiche_envoyee` : Badge info (bleu clair)
- `visite_planifiee` : Badge primary (bleu)
- `en_attente_paiement` : Badge warning (jaune)
- `paiement_valide` / `actif` : Badge success (vert)
- `annule` / `resilie` : Badge danger (rouge)

## 📝 Workflow Détaillé

### Scénario Vente Complète

1. **Client manifeste son intérêt** (Frontend)
   - Via formulaire de contact sur une annonce
   - `PropertyController::contact()` crée une `Vente` avec statut `demande_client`
   - Message du client enregistré dans `message_client`

2. **Admin envoie la fiche** (Backend)
   - Ouvre la vente → Clique "Envoyer la fiche"
   - Modal avec note optionnelle
   - `VenteController::envoyerFiche()` → statut `fiche_envoyee`
   - TODO: Email automatique au client

3. **Admin planifie la visite**
   - Clique "Planifier la visite"
   - Choisit date/heure
   - `VenteController::planifierVisite()` → statut `visite_planifiee`

4. **Admin enregistre le résultat de la visite**
   - Clique "Marquer comme effectuée"
   - Renseigne compte rendu et si client intéressé
   - Si OUI : `visiteEffectuee()` enregistre le compte rendu
   - Si NON : Vente passée à `annule` automatiquement

5. **Admin configure le paiement**
   - Clique "Configurer le paiement"
   - Renseigne : prix de vente, caution, frais d'agence
   - `VenteController::configurerPaiement()` → statut `en_attente_paiement`

6. **Admin valide le paiement**
   - Une fois paiement reçu, clique "Valider le paiement"
   - `VenteController::validerPaiement()` :
     - Statut → `paiement_valide`
     - Enregistre `date_finalisation`
     - Annonce : statut → `vendu`, `statut_publication` → 0
   - **Vente finalisée !**

### Scénario Location Complète

Étapes 1-4 identiques à la vente.

5. **Admin configure le paiement**
   - Renseigne : loyer mensuel, nombre cautions, montant caution, frais agence
   - Renseigne : date début, date fin (optionnelle), jour de paiement (1-31)
   - `LocationController::configurerPaiement()` → statut `en_attente_paiement`

6. **Admin valide le paiement et active**
   - `LocationController::validerPaiement()` :
     - Statut → `actif`
     - Enregistre `date_finalisation`
     - Annonce : statut → `loué`, `statut_publication` → 0
     - **Appelle `genererEcheances()` automatiquement**
   - Échéances créées dans la table `echeances`

7. **Gestion des échéances** (Location active)
   - Affichage des échéances dans la vue
   - Boutons pour marquer comme payées
   - Suivi des paiements mensuels

8. **Résiliation ou Fin**
   - Admin peut résilier : `resilierLocation()` → statut `resilie`
   - Ou location se termine naturellement → statut `termine`

## ⚠️ Points d'Attention

### Module DemandeInteret
- Le module **existe toujours** pour les données historiques
- Ne doit plus être utilisé pour les nouvelles demandes
- Les vues frontend ne créent plus de `DemandeInteret`
- Les vues backend conservent les anciennes demandes en lecture seule

### Génération des Échéances
Pour les locations, les échéances sont **générées automatiquement** lors de :
- Validation du paiement (`validerPaiement()`)
- Basées sur : `date_debut`, `date_fin`, `jour_paiement`, `loyer_mensuel`

### Notifications Email
🔴 **À IMPLÉMENTER** :
- Email lors de l'envoi de la fiche
- Email de confirmation de visite
- Email de validation de paiement
- Utiliser les classes Mail existantes et les adapter

## 🔄 Migrations Exécutées

```bash
php artisan migrate
```

1. `2026_02_02_000000_simplify_demande_interets_workflow.php`
   - Simplification de la table `demande_interets` (7 statuts au lieu de 10)

2. `2026_02_02_150000_add_workflow_to_ventes_locations.php`
   - Ajout des champs workflow aux tables `ventes` et `locations`
   - Mise à jour des ENUM `statut`

## 📦 Fichiers Modifiés/Créés

### Modifiés
- `app/Models/Vente.php`
- `app/Models/Location.php`
- `app/Http/Controllers/VenteController.php`
- `app/Http/Controllers/LocationController.php`
- `app/Http/Controllers/frontend/PropertyController.php`
- `routes/web.php`

### Créés
- `database/migrations/2026_02_02_150000_add_workflow_to_ventes_locations.php`
- `resources/views/backend/pages/ventes/show.blade.php` (remplacé)
- `resources/views/backend/pages/locations/show.blade.php` (remplacé)

### Archivés (backup)
- `resources/views/backend/pages/ventes/show_old.blade.php`
- `resources/views/backend/pages/locations/show_old.blade.php`

## 🚀 Prochaines Étapes Recommandées

1. **Notifications Email**
   - Implémenter `VenteFicheEnvoyeeMail`
   - Implémenter `LocationFicheEnvoyeeMail`
   - Implémenter `VisitePlanifieeMail`
   - Implémenter `PaiementValideMail`

2. **Dashboard Admin**
   - Widget de suivi des workflows en cours
   - Statistiques par étape
   - Alertes pour visites à venir

3. **Dashboard Client**
   - Vue de ses ventes/locations en cours
   - Suivi de la progression
   - Documents téléchargeables

4. **Amélioration des vues index**
   - Filtres par statut
   - Recherche avancée
   - Export Excel/PDF

5. **Tests**
   - Tests unitaires pour chaque action du workflow
   - Tests d'intégration du parcours complet
   - Tests de validation des données

## 📞 Support

Pour toute question ou problème lié au workflow intégré, consultez :
- Ce document
- `WORKFLOW_INTEGRE_VENTES_LOCATIONS.md` (schéma détaillé)
- Les commentaires dans les contrôleurs

---

**Date de mise en œuvre** : 2026-02-02  
**Version** : 1.0  
**Statut** : ✅ Implémentation complète
