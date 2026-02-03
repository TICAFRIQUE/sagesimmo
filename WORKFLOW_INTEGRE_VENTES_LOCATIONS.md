# Workflow Intégré Ventes/Locations

## Vue d'ensemble

Le workflow de gestion des demandes clients est maintenant **intégré directement dans les modules Ventes et Locations**. Il n'y a plus de module de demandes séparé.

## Workflow Unique pour Vente et Location

### 1. **demande_client** (Statut initial)
- **Déclenchement :** Client clique sur "Je suis intéressé" sur une annonce
- **Action système :** Création automatique d'une Vente ou Location selon le type de bien
- **Données enregistrées :** `message_client`, `client_id/locataire_id`, `annonce_id`

### 2. **fiche_envoyee**
- **Action admin :** Envoyer une fiche d'information à remplir par email
- **Objectif :** Recueillir les informations nécessaires du client

### 3. **visite_planifiee**
- **Action admin :** Planifier une date de visite (après retour positif de la fiche)
- **Données enregistrées :** `date_visite`, `note_admin`

### 4. **en_attente_paiement**
- **Déclenchement :** Après visite, si client intéressé
- **Données enregistrées :** 
  - **Pour Vente :** `prix_vente`, `montant_caution`, `montant_frais_agence`, `commission_agence`
  - **Pour Location :** `loyer_mensuel`, `caution`, `montant_frais_agence`, `nombre_cautions`

### 5. **Finalisation**
- **Pour Vente :** Statut passe à `paiement_valide`
  - **Actions :** Bien marqué comme "vendu", dépublié
  - **Données :** `date_finalisation`, `date_signature`
  
- **Pour Location :** Statut passe à `actif`
  - **Actions :** Bien marqué comme "loué", dépublié, création automatique des échéances
  - **Données :** `date_finalisation`, `date_debut`, `date_fin`, `jour_paiement`

### 6. **Annulation/Résiliation**
- **Vente :** `annule` - Demande abandonnée
- **Location :** `resilie` - Contrat résilié avant terme
- **Location :** `termine` - Contrat arrivé à échéance

## Architecture Technique

### Tables Mises à Jour

#### Table `ventes`
**Nouveaux champs :**
```php
- message_client (text)           // Message initial du client
- date_visite (datetime)          // Date de la visite
- compte_rendu_visite (text)      // Compte-rendu de visite
- note_admin (text)               // Notes internes admin
- montant_caution (decimal)       // Caution versée
- montant_frais_agence (decimal)  // Frais d'agence
- date_finalisation (datetime)    // Date de finalisation
```

**Statuts :**
- `demande_client`, `fiche_envoyee`, `visite_planifiee`, `en_attente_paiement`, `paiement_valide`, `annule`

#### Table `locations`
**Nouveaux champs :**
```php
- message_client (text)           // Message initial du client
- date_visite (datetime)          // Date de la visite
- compte_rendu_visite (text)      // Compte-rendu de visite
- note_admin (text)               // Notes internes admin
- montant_frais_agence (decimal)  // Frais d'agence
- date_finalisation (datetime)    // Date de finalisation
```

**Statuts :**
- `demande_client`, `fiche_envoyee`, `visite_planifiee`, `en_attente_paiement`, `actif`, `termine`, `resilie`

### Modèles

**Vente.php & Location.php**
- Méthode `getStatutBadgeAttribute()` - Badge HTML coloré
- Méthode `getProgressionAttribute()` - Progression 0-100%

## Flux Client

1. **Client consulte une annonce** → Clique sur "Je suis intéressé"
2. **Système crée automatiquement** → Vente ou Location avec statut `demande_client`
3. **Admin voit la nouvelle demande** → Dans l'interface Ventes ou Locations
4. **Admin envoie la fiche** → Email avec formulaire à remplir
5. **Admin planifie visite** → Après retour positif
6. **Visite effectuée** → Admin saisit compte-rendu
7. **Configuration paiement** → Si client intéressé
8. **Validation paiement** → Remise des clés, bien marqué loué/vendu

## Interface Admin

### Dans `/admin/ventes` et `/admin/locations`

**Filtres disponibles :**
- Par statut du workflow
- Par client
- Par bien

**Actions disponibles selon le statut :**
- `demande_client` → Envoyer fiche / Annuler
- `fiche_envoyee` → Planifier visite / Annuler
- `visite_planifiee` → Marquer visite effectuée
- `en_attente_paiement` → Valider paiement / Annuler
- `paiement_valide`/`actif` → Gérer paiements, échéances

## Interface Client

### Dans `/mon-espace`

Le client peut voir :
- Ses demandes en cours (ventes/locations en statut workflow)
- L'historique avec timeline de progression
- Ses biens loués (locations actives)
- Ses biens achetés (ventes finalisées)
- Le suivi de ses paiements pour les locations

## Avantages du Nouveau Système

✅ **Simplifié** : Plus de module séparé, tout dans Ventes/Locations  
✅ **Unifié** : Même workflow pour vente et location  
✅ **Traçable** : Historique complet dans chaque dossier  
✅ **Évolutif** : Facile d'ajouter des statuts ou champs  
✅ **Intuitif** : Admin gère tout au même endroit  

## Migration des Données

La migration `2026_02_02_150000_add_workflow_to_ventes_locations.php` :
- Ajoute les nouveaux champs aux tables existantes
- Met à jour les statuts ENUM
- Convertit automatiquement les anciens statuts
- Conserve toutes les données existantes

## Prochaines Étapes

1. ✅ Migration exécutée
2. ⏳ Mettre à jour les contrôleurs Vente/Location avec les actions du workflow
3. ⏳ Créer les vues pour gérer le workflow dans l'interface admin
4. ⏳ Adapter le dashboard client
5. ⏳ Implémenter l'envoi d'emails automatiques

## Notes Importantes

- **Le module DemandeInteret reste** pour l'historique des anciennes demandes
- **Nouvelles demandes** = Créées directement en Vente/Location
- **Pas de double saisie** : Un seul dossier du début à la fin
- **Workflow flexible** : Possibilité de sauter des étapes si besoin
