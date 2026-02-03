# Workflow Simplifié des Demandes d'Intérêt

## Vue d'ensemble

Ce système gère le processus simplifié de demandes d'intérêt pour les biens immobiliers (location ou vente), depuis la manifestation d'intérêt jusqu'à la remise des clés.

## Processus Complet (7 Étapes)

### 1. Nouvelle Demande (`nouvelle`)
**Déclenchement :** Client clique sur "Je suis intéressé" (nécessite connexion/inscription)  
**Actions admin disponibles :**
- Envoyer le contrat par email
- Clôturer la demande

### 2. Contrat Envoyé (`contrat_envoye`)
**Déclenchement :** Admin uploade et envoie le contrat PDF par email au client  
**Données enregistrées :** Fichier PDF dans collection `contrat`  
**Actions admin disponibles :**
- Planifier une visite (après accord du client en externe)

### 3. Visite Planifiée (`visite_planifiee`)
**Déclenchement :** Admin planifie une date de visite après accord du client  
**Données enregistrées :** `date_visite`, `note_admin`  
**Actions admin disponibles :**
- Marquer la visite comme effectuée (avec compte-rendu)

### 4. Visite Effectuée (`visite_effectuee`)
**Déclenchement :** Admin confirme que la visite a eu lieu  
**Données enregistrées :** `compte_rendu_visite`, `client_interesse_apres_visite`  
**Actions admin disponibles :**
- Configurer le paiement (si client intéressé)
- Clôturer si client non intéressé  
**Note :** Si `client_interesse_apres_visite = 0`, la demande est automatiquement clôturée

### 5. Paiement en Attente (`paiement_en_attente`)
**Déclenchement :** Admin configure les montants du paiement  
**Données enregistrées :**
- `montant_caution`
- `montant_loyer_premier` (premier loyer ou acompte vente)
- `montant_frais_agence`
- `montant_total_paiement` (calcul automatique)

**Actions admin disponibles :**
- Valider le paiement et remettre les clés

### 6. Paiement Validé - Clés Remises (`paiement_valide`)
**Déclenchement :** Admin confirme réception du paiement et remise des clés  
**Actions automatiques :**
- Bien marqué comme "loué" ou "vendu"
- Bien dépublié (statut_publication = 0)
- `date_finalisation` enregistrée

**Données enregistrées :**
- `details_paiement` (note, référence)
- `statut_paiement` = "valide"

### 7. Clôturé (`cloture`)
**Déclenchements possibles :**
- Admin refuse/clôture la demande initiale
- Client non intéressé après visite
- Abandon du processus

**Données enregistrées :** `motif_cloture`

## Suivi après Finalisation

### Pour les Locations
- Un suivi d'échéances de loyer est automatiquement créé
- Le client peut voir ses échéances à venir
- Le client peut effectuer ses paiements mensuels
- L'admin gère le calendrier des paiements

### Pour les Ventes
- Aucun suivi supplémentaire nécessaire
- Transaction finalisée après paiement et remise des clés

## Architecture Technique

### Modèle : `DemandeInteret`

**Champs principaux :**
```php
- user_id, annonce_id, message
- statut (enum 7 valeurs)
- date_visite, note_admin
- compte_rendu_visite, client_interesse_apres_visite
- contrat_url
- montant_caution, montant_loyer_premier, montant_frais_agence, montant_total_paiement
- statut_paiement, details_paiement
- date_finalisation, motif_cloture
```

**Collections Media (Spatie):**
- `contrat` : Single (PDF uniquement) - Contrat envoyé par email

**Attributs calculés :**
- `statut_badge` : Badge HTML coloré
- `progression` : 0-100% selon le statut
- `is_cloture` : Boolean
- `is_en_cours` : Boolean

### Contrôleur Backend : `DemandeInteretController`

**Méthodes disponibles :**
1. `envoyerContrat($request, $id)` - POST admin/demandes/{id}/envoyer-contrat
2. `planifierVisite($request, $id)` - POST admin/demandes/{id}/planifier-visite
3. `visiteEffectuee($request, $id)` - POST admin/demandes/{id}/visite-effectuee
4. `configurerPaiement($request, $id)` - POST admin/demandes/{id}/configurer-paiement
5. `validerPaiement($request, $id)` - POST admin/demandes/{id}/valider-paiement
6. `cloturerDemande($request, $id)` - POST admin/demandes/{id}/cloturer

### Contrôleur Frontend : `DashboardClientController`

**Méthodes :**
- `index()` : Dashboard avec statistiques
  - `demandesEnCours` : Compter les demandes non clôturées
  - `demandesFinalisees` : Compter les `paiement_valide`
  - `prochainesVisites` : Liste des visites futures
  - `biensLoues` : Liste des biens loués
  - `biensAchetes` : Liste des biens achetés
  
- `demandes()` : Voir l'historique de toutes ses demandes
- `showDemande($id)` : Détails d'une demande avec timeline du workflow

## Interface Utilisateur

### Backend Admin (`/admin/demandes/{id}`)

**Éléments affichés :**
1. Barre de progression visuelle (6 étapes)
2. Badge de statut actuel
3. Informations client (nom, email, téléphone)
4. Informations du bien (image, titre, prix)
5. Message initial du client
6. Détails selon le statut :
   - Date de visite (si planifiée)
   - Compte-rendu de visite
   - Contrat PDF (téléchargeable)
   - Tableau récapitulatif du paiement
7. **Section "Actions disponibles"** : Carte avec boutons contextuels selon le statut

**Modaux disponibles :**
- `envoyerContratModal`
- `planifierVisiteModal`
- `visiteEffectueeModal`
- `configurerPaiementModal`
- `validerPaiementModal`
- `cloturerDemandeModal`

### Frontend Client (`/mon-espace/demandes/{id}`)

**Éléments affichés :**
1. Timeline animée du workflow (6 étapes)
2. Badge de statut
3. Barre de progression (%)
4. Informations du bien
5. Message envoyé
6. Contrat téléchargeable (si envoyé)
7. Détails du paiement (si configuré)
8. Historique complet de la demande

**Dashboard Client (`/mon-espace`)**
- Statistiques : Total demandes, En cours, Finalisées, Visites à venir
- Dernières demandes
- Prochaines visites
- Biens loués : Liste avec suivi des échéances
- Biens achetés : Liste des propriétés achetées

## Notifications Email (À implémenter)

**Événements à notifier au client :**
1. Demande reçue (nouvelle)
2. Contrat envoyé (avec pièce jointe PDF)
3. Visite planifiée (date et heure)
4. Paiement configuré (montants détaillés)
5. Transaction finalisée (confirmation remise des clés)
6. Clôture (avec motif)

**Événements à notifier à l'agence :**
1. Nouvelle demande reçue
2. Accord client (à suivre manuellement)

## Règles Métier

1. **Authentification obligatoire** : Client doit être connecté pour soumettre une demande
2. **Statut initial** : Toute nouvelle demande commence à `nouvelle`
3. **Clôture automatique** : Si `client_interesse_apres_visite = 0`, passe directement à `cloture`
4. **Bien dépublié** : Seulement quand statut = `paiement_valide`, le bien est marqué loué/vendu et dépublié
5. **Progression linéaire** : Les statuts suivent l'ordre défini (sauf clôture possible à tout moment)
6. **Une seule demande active** : Un client ne peut avoir qu'une demande en cours par bien
7. **Suivi des locations** : Pour les locations, un système d'échéances est créé automatiquement après `paiement_valide`

## Workflow Simplifié - Vue d'ensemble

```
Client manifeste intérêt (connexion requise)
         ↓
[1. Nouvelle] → Admin envoie contrat par email
         ↓
[2. Contrat envoyé] → Accord client (externe), Admin planifie visite
         ↓
[3. Visite planifiée] → Visite effectuée
         ↓
[4. Visite effectuée] → Client intéressé?
         ↓ OUI                    ↓ NON
[5. Paiement en attente]     [7. Clôturé]
         ↓
[6. Paiement validé]
    Remise des clés
    ↓
    Location → Suivi échéances
    Vente → Fin du processus
```

## Différences avec l'ancien workflow

**Étapes supprimées :**
- Demande de pièces justificatives
- Réception et validation des documents
- Upload de documents par le client

**Simplifié :**
- Le contrat est envoyé directement par email après la manifestation d'intérêt
- La visite est planifiée après accord externe (par téléphone, email, etc.)
- Pas de gestion de documents supplémentaires
- Un seul statut de clôture au lieu de deux

**Raisons de la simplification :**
- Processus plus rapide et plus fluide
- Moins d'étapes techniques pour le client
- Gestion des accords en externe (plus flexible)
- Focus sur l'essentiel : visite → paiement → remise des clés

## Migration des Données

**Fichier :** `2026_02_02_000000_simplify_demande_interets_workflow.php`

**Commande :** `php artisan migrate`

**Modifications :**
- Suppression des colonnes : `pieces_demandees`, `pieces_fournies`, `documents_urls`, `raison_refus_dossier`, `date_signature_contrat`, `commission_agence`, `type_commission`, `motif_refus`
- Ajout colonne : `motif_cloture`
- Mise à jour enum `statut` (7 valeurs au lieu de 10)
- Conversion automatique des anciens statuts vers les nouveaux

## Routes à Mettre à Jour

**Backend (admin) :**
```php
// Nouvelles routes
POST /admin/demandes/{id}/envoyer-contrat
POST /admin/demandes/{id}/planifier-visite
POST /admin/demandes/{id}/visite-effectuee
POST /admin/demandes/{id}/configurer-paiement
POST /admin/demandes/{id}/valider-paiement
POST /admin/demandes/{id}/cloturer

// Routes supprimées
// POST /admin/demandes/{id}/demander-pieces
// POST /admin/demandes/{id}/documents-recus
// POST /admin/demandes/{id}/valider-dossier
// POST /admin/demandes/{id}/refuser-dossier
// POST /admin/demandes/{id}/generer-contrat
```

**Frontend (client) :**
```php
GET /mon-espace (dashboard avec biens loués/achetés)
GET /mon-espace/demandes
GET /mon-espace/demandes/{id}
DELETE /mon-espace/demandes/{id} (annuler si nouvelle)

// Route supprimée
// POST /mon-espace/demandes/{id}/upload-documents
```

## Points d'Attention

⚠️ **IMPORTANT** :
- La validation du paiement est **irréversible** et dépublie le bien
- Les contrats sont envoyés par email au client
- Les contrats doivent être des PDF uniquement
- Penser à sauvegarder les notes admin à chaque étape pour traçabilité
- L'accord du client (après envoi du contrat) se fait en externe (téléphone, email, WhatsApp, etc.)

## Améliorations Futures

- [ ] Notifications email automatiques à chaque étape
- [ ] Génération PDF de contrat automatique (template)
- [ ] Signature électronique du contrat
- [ ] Paiement en ligne intégré
- [ ] Export Excel des demandes
- [ ] Statistiques avancées (taux de conversion, durée moyenne)
- [ ] Calendar view des visites
- [ ] Rappels automatiques (visite approche, paiement en attente)
- [ ] WhatsApp integration pour notifications
