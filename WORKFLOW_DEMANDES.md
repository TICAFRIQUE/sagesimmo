# Workflow des Demandes d'Intérêt

## Vue d'ensemble

Ce système gère l'intégralité du processus de demandes d'intérêt pour les biens immobiliers, depuis l'intérêt initial jusqu'à la finalisation de la transaction.

## Processus Complet (10 Étapes)

### 1. Nouvelle Demande (`nouvelle`)
**Déclenchement :** Client clique sur "Je suis intéressé" (nécessite connexion/inscription)  
**Actions admin disponibles :**
- Planifier une visite
- Refuser la demande (clôture avec `cloture_refus`)

### 2. Visite Planifiée (`visite_planifiee`)
**Déclenchement :** Admin planifie une date de visite  
**Données enregistrées :** `date_visite`, `note_admin`  
**Actions admin disponibles :**
- Marquer la visite comme effectuée (avec compte-rendu)

### 3. Visite Effectuée (`visite_effectuee`)
**Déclenchement :** Admin confirme que la visite a eu lieu  
**Données enregistrées :** `compte_rendu_visite`, `client_interesse_apres_visite`  
**Actions admin disponibles :**
- Demander des pièces justificatives
- Clôturer si client non intéressé (`cloture_non_interesse`)  
**Note :** Si `client_interesse_apres_visite = 0`, la demande est automatiquement clôturée

### 4. Documents Reçus (`documents_recus`)
**Déclenchement automatique :** Client uploade les documents demandés via son espace  
**OU Admin confirme manuellement la réception**  
**Données enregistrées :** `pieces_demandees` (liste), fichiers dans collection `documents_client`  
**Actions admin disponibles :**
- Valider le dossier
- Refuser le dossier (clôture avec raison)

### 5. Dossier Validé (`dossier_valide`)
**Déclenchement :** Admin vérifie et valide les documents  
**Actions admin disponibles :**
- Générer et uploader le contrat PDF

### 6. Contrat Généré (`contrat_genere`)
**Déclenchement :** Admin uploade le contrat signé  
**Données enregistrées :** Fichier PDF dans collection `contrat`, `date_signature_contrat`  
**Actions admin disponibles :**
- Configurer le paiement (montants)

### 7. Paiement en Attente (`paiement_en_attente`)
**Déclenchement :** Admin configure les montants  
**Données enregistrées :**
- `montant_caution`
- `montant_loyer_premier` (premier loyer ou acompte vente)
- `montant_frais_agence`
- `montant_total_paiement` (calcul automatique)

**Actions admin disponibles :**
- Valider le paiement (finalisation)

### 8. Paiement Validé (`paiement_valide`)
**Déclenchement :** Admin confirme réception du paiement  
**Actions automatiques :**
- Bien marqué comme "loué" ou "vendu"
- Bien dépublié (statut_publication = 0)
- `date_finalisation` enregistrée

**Données enregistrées :**
- `commission_agence`
- `details_paiement` (mode, référence)
- `statut_paiement` = "valide"

### 9-10. Clôtures (`cloture_refus`, `cloture_non_interesse`)
**Déclenchements possibles :**
- Admin refuse la demande initiale
- Client non intéressé après visite
- Dossier refusé (documents insuffisants)

**Données enregistrées :** `motif_refus`

## Architecture Technique

### Modèle : `DemandeInteret`

**Champs principaux :**
```php
- user_id, annonce_id, message
- statut (enum 10 valeurs)
- date_visite, pieces_demandees, note_admin
- compte_rendu_visite
- contrat_url, date_signature_contrat
- montant_caution, montant_loyer_premier, montant_frais_agence, montant_total_paiement
- commission_agence, statut_paiement, details_paiement
- date_finalisation, motif_refus
```

**Collections Media (Spatie):**
- `documents_client` : Multiple (PDF, images)
- `contrat` : Single (PDF uniquement)

**Attributs calculés :**
- `statut_badge` : Badge HTML coloré
- `progression` : 0-100% selon le statut
- `is_cloture` : Boolean
- `is_en_cours` : Boolean

### Contrôleur Backend : `DemandeInteretController`

**Méthodes disponibles :**
1. `planifierVisite($request, $id)` - POST admin/demandes/{id}/planifier-visite
2. `visiteEffectuee($request, $id)` - POST admin/demandes/{id}/visite-effectuee
3. `demanderPieces($request, $id)` - POST admin/demandes/{id}/demander-pieces
4. `documentsRecus($request, $id)` - POST admin/demandes/{id}/documents-recus
5. `validerDossier($request, $id)` - POST admin/demandes/{id}/valider-dossier
6. `refuserDossier($request, $id)` - POST admin/demandes/{id}/refuser-dossier
7. `genererContrat($request, $id)` - POST admin/demandes/{id}/generer-contrat
8. `configurerPaiement($request, $id)` - POST admin/demandes/{id}/configurer-paiement
9. `validerPaiement($request, $id)` - POST admin/demandes/{id}/valider-paiement
10. `changerStatut($request, $id)` - POST admin/demandes/{id}/changer-statut (clôtures)

### Contrôleur Frontend : `DashboardClientController`

**Méthodes :**
- `index()` : Dashboard avec statistiques
  - `demandesEnCours` : Compter les demandes non clôturées
  - `demandesFinalisees` : Compter les `paiement_valide`
  - `prochainesVisites` : Liste des visites futures
  
- `uploadDocuments($request, $id)` : Upload multiple de fichiers
  - Enregistre dans collection `documents_client`
  - Change automatiquement statut vers `documents_recus`

## Interface Utilisateur

### Backend Admin (`/admin/demandes/{id}`)

**Éléments affichés :**
1. Barre de progression visuelle (8 étapes)
2. Badge de statut actuel
3. Informations client (nom, email, téléphone)
4. Informations du bien (image, titre, prix)
5. Message initial du client
6. Détails selon le statut :
   - Date de visite (si planifiée)
   - Compte-rendu de visite
   - Pièces demandées
   - Documents uploadés (liste téléchargeable)
   - Contrat PDF (téléchargeable)
   - Tableau récapitulatif du paiement
7. **Section "Actions disponibles"** : Carte avec boutons contextuels selon le statut

**Modaux disponibles :**
- `planifierVisiteModal`
- `visiteEffectueeModal`
- `demanderPiecesModal`
- `documentsRecusModal`
- `validerDossierModal`
- `refuserDossierModal`
- `genererContratModal`
- `configurerPaiementModal`
- `validerPaiementModal`

### Frontend Client (`/mon-espace/demandes/{id}`)

**Éléments affichés :**
1. Timeline animée du workflow (10 étapes)
2. Badge de statut
3. Barre de progression (%)
4. Informations du bien
5. Message envoyé
6. **Formulaire d'upload de documents** (si `pieces_demandees` existe et statut = `visite_effectuee`)
7. Liste des documents uploadés
8. Contrat téléchargeable (si généré)
9. Détails du paiement (si configuré)

## Notifications Email (À implémenter)

**Événements à notifier au client :**
1. Demande reçue (nouvelle)
2. Visite planifiée (date)
3. Pièces demandées (liste)
4. Dossier validé
5. Contrat disponible
6. Paiement configuré (montants)
7. Transaction finalisée
8. Clôture (refus, non intéressé)

**Événements à notifier à l'agence :**
1. Nouvelle demande reçue
2. Documents uploadés par client
3. Paiement attendu

## Règles Métier

1. **Authentification obligatoire** : Client doit être connecté pour soumettre une demande
2. **Statut initial** : Toute nouvelle demande commence à `nouvelle`
3. **Clôture automatique** : Si `client_interesse_apres_visite = 0`, passe directement à `cloture_non_interesse`
4. **Upload automatique** : Quand client uploade des documents, statut passe automatiquement à `documents_recus`
5. **Bien dépublié** : Seulement quand statut = `paiement_valide`, le bien est marqué loué/vendu et dépublié
6. **Progression linéaire** : Les statuts suivent généralement l'ordre défini (sauf clôtures)
7. **Une seule demande active** : Un client ne peut avoir qu'une demande en cours par bien

## Exemples d'Usage

### Scénario 1 : Location réussie
```
nouvelle → planifier visite → visite effectuée (intéressé)
→ demander pièces → client uploade → documents_recus
→ valider dossier → generer contrat → configurer paiement
→ valider paiement (BIEN LOUÉ ET DÉPUBLIÉ)
```

### Scénario 2 : Client non intéressé après visite
```
nouvelle → planifier visite → visite effectuée (non intéressé)
→ cloture_non_interesse (AUTOMATIQUE)
```

### Scénario 3 : Dossier incomplet
```
nouvelle → planifier visite → visite effectuée → demander pièces
→ client uploade → documents_recus → refuser dossier
→ cloture_refus
```

## Migration

**Fichier :** `2026_01_28_000002_update_demande_interets_table_workflow.php`

**Commande :** `php artisan migrate`

**Modifications :**
- Drop ancien enum `statut` (3 valeurs)
- Création nouveau enum `statut` (10 valeurs)
- Ajout 14 nouveaux champs pour le workflow complet

## Dépendances

- **Spatie Media Library** : `spatie/laravel-medialibrary`
- **Laravel 11** : Framework base
- **Bootstrap 5** : Interface utilisateur
- **RemixIcon** : Icônes

## Points d'Attention

⚠️ **IMPORTANT** :
- La validation du paiement est **irréversible** et dépublie le bien
- Les documents client sont stockés dans `storage/app/public/media`
- Les contrats doivent être des PDF uniquement
- Penser à sauvegarder les notes admin à chaque étape pour traçabilité
- Commission agence saisie lors de la validation paiement (pour comptabilité)

## Améliorations Futures

- [ ] Notifications email automatiques
- [ ] Génération PDF de contrat automatique (template)
- [ ] Signature électronique du contrat
- [ ] Paiement en ligne intégré
- [ ] Export Excel des demandes
- [ ] Statistiques avancées (taux de conversion, durée moyenne)
- [ ] Calendar view des visites
- [ ] Rappels automatiques (visite approche, documents manquants)
