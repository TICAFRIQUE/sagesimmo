# Workflow des Ventes - Système de Paiement

## 📋 Vue d'ensemble

Le système de gestion des ventes a été implémenté avec les mêmes principes que les locations, mais adapté aux spécificités d'une vente immobilière.

## ✅ Caractéristiques principales

### 1. **Pas d'échéances mensuelles**
- Les ventes n'utilisent pas de système d'échéances comme les locations
- Le paiement est basé sur le montant total de la vente

### 2. **Paiement partiel autorisé**
- Le client peut effectuer plusieurs paiements jusqu'à atteindre le montant total
- Chaque paiement est enregistré avec son type (acompte, solde, caution, frais d'agence)
- Progression en temps réel du montant payé

### 3. **Historique immuable**
- Tous les paiements sont conservés dans l'historique
- Impossibilité de modifier ou supprimer un paiement (traçabilité complète)
- Chaque paiement contient: date, montant, méthode, référence, notes

### 4. **Bien vendu seulement après paiement total**
- Le bien ne peut être marqué comme "vendu" que si le paiement est complet
- Validation automatique du montant total avant finalisation
- Le bien est automatiquement dépublié après la vente

### 5. **Commission traçable**
- Chaque paiement enregistre la commission proportionnelle
- Le système calcule automatiquement la commission sur chaque paiement
- Suivi du total des commissions perçues

## 🔄 Workflow étape par étape

### Étape 1: Demande client
- Un client manifeste son intérêt pour un bien
- Création de la vente avec statut `demande_client`

### Étape 2: Envoi de la fiche
- L'admin envoie la fiche d'information au client
- Possibilité de joindre des documents
- Statut passe à `fiche_envoyee`

### Étape 3: Planification de la visite
- L'admin planifie une visite avec le client
- Date et heure de la visite enregistrées
- Statut passe à `visite_planifiee`

### Étape 4: Visite effectuée
- Enregistrement du compte rendu de visite
- Si le client est intéressé: passage à `en_attente_paiement`
- Si non intéressé: vente annulée

### Étape 5: Configuration du paiement
- Définition du prix de vente final
- Configuration de la caution (si applicable)
- Configuration des frais d'agence
- Configuration de la commission (pourcentage ou montant fixe)
- Calcul automatique du montant total à payer

### Étape 6: Ajout des paiements
- Le client effectue un ou plusieurs paiements
- Pour chaque paiement:
  - Type: acompte, solde, caution, frais d'agence
  - Montant (ne peut pas dépasser le reste à payer)
  - Date de paiement
  - Méthode: espèces, virement, chèque, carte bancaire, autre
  - Référence (optionnelle)
  - Notes (optionnelles)
- Le système calcule automatiquement:
  - La commission proportionnelle sur ce paiement
  - Le montant total payé
  - Le reste à payer
  - Le pourcentage de progression

### Étape 7: Validation du paiement complet
- Vérification automatique que le paiement est complet
- Si le montant total est payé:
  - Finalisation de la vente
  - Remise des clés au client
  - Le bien est marqué comme `vendu`
  - Le bien est dépublié automatiquement
  - Statut passe à `paiement_valide`

## 💰 Gestion des commissions

### Calcul de la commission

La commission peut être configurée de deux façons:

1. **Pourcentage**: 
   - Commission = (Prix de vente × Pourcentage) / 100
   - Exemple: 5% sur 10,000,000 FCFA = 500,000 FCFA

2. **Montant fixe**:
   - Commission = Montant défini
   - Exemple: 300,000 FCFA fixe

### Traçabilité de la commission

Chaque paiement enregistre:
- Le montant de la commission associée
- Le type de commission (pourcentage ou fixe)
- Commission proportionnelle au montant payé

**Exemple**: 
- Prix de vente: 10,000,000 FCFA
- Commission: 5% = 500,000 FCFA
- Paiement 1: 3,000,000 FCFA → Commission: 150,000 FCFA (30%)
- Paiement 2: 7,000,000 FCFA → Commission: 350,000 FCFA (70%)
- Total commission: 500,000 FCFA ✅

## 📊 Modèles de données

### Modèle Vente

**Nouvelles méthodes**:

```php
// Montant total à payer (prix + caution + frais)
$vente->montantTotalAPayer()

// Montant déjà payé
$vente->montantTotalPaye()

// Reste à payer
$vente->resteAPayer()

// Vérifier si le paiement est complet
$vente->estEntierementPaye()

// Pourcentage de paiement
$vente->pourcentagePaiement()

// Total des commissions perçues
$vente->totalCommissionsPercues()

// Commission attendue sur la vente
$vente->getCommissionAttendue()
```

### Modèle Paiement

**Champs pour les ventes**:
- `payable_type`: 'App\Models\Vente'
- `payable_id`: ID de la vente
- `type_paiement`: 'acompte', 'solde', 'caution', 'frais_agence'
- `montant`: Montant du paiement
- `commission_agence`: Commission sur ce paiement
- `type_commission`: 'pourcentage' ou 'montant'
- `date_paiement`: Date du paiement
- `methode_paiement`: Méthode utilisée
- `reference`: Référence de transaction
- `notes`: Notes sur le paiement
- `statut`: 'paye' (toujours payé pour les ventes)

**Méthodes**:
```php
// Montant de la commission en FCFA
$paiement->montant_commission

// Commission formatée
$paiement->commission_formattee
```

## 🛡️ Règles de validation

### Ajout de paiement
1. ✅ Le montant ne peut pas dépasser le reste à payer
2. ✅ La date de paiement est obligatoire
3. ✅ La méthode de paiement est obligatoire
4. ✅ Le type de paiement est obligatoire
5. ✅ Tous les paiements sont immuables (pas de modification/suppression)

### Validation du paiement
1. ✅ Le paiement doit être complet (reste à payer = 0)
2. ✅ Le bien est automatiquement marqué comme vendu
3. ✅ Le bien est automatiquement dépublié
4. ✅ La date de finalisation est enregistrée

## 🎨 Interface utilisateur

### Page de détail de la vente

**Section 1: Progression du workflow**
- Barre de progression visuelle
- Badge de statut coloré
- Pourcentage de complétion

**Section 2: Étapes du workflow**
- Affichage des étapes avec état (complété/actif/en attente)
- Actions disponibles selon l'étape
- Boutons pour faire progresser le workflow

**Section 3: Configuration du paiement**
- Affichage du montant total à payer
- Détail des composantes (prix + caution + frais)
- Commission calculée automatiquement

**Section 4: Progression du paiement**
- Barre de progression du paiement
- Montant payé vs reste à payer
- Tableau d'historique des paiements avec:
  - Date
  - Type de paiement
  - Montant
  - Commission
  - Méthode de paiement

**Section 5: Récapitulatif final** (après validation)
- Montant total perçu
- Nombre de paiements effectués
- Commission totale perçue
- Date de finalisation

## 🔐 Sécurité et intégrité

### Protection des données
- ✅ Aucun paiement ne peut être modifié ou supprimé
- ✅ Historique complet et immuable
- ✅ Traçabilité totale des commissions
- ✅ Validation stricte des montants

### Contrôles automatiques
- ✅ Empêche le dépassement du montant total
- ✅ Vérifie le paiement complet avant validation
- ✅ Calcule automatiquement les commissions
- ✅ Met à jour le statut du bien automatiquement

## 📱 Messages et notifications

### Alerts utilisateur

**Ajout de paiement**:
- "Paiement de X FCFA ajouté. Reste à payer : Y FCFA"
- "Paiement complet ! Vous pouvez valider la vente."

**Erreurs**:
- "Le montant dépasse le reste à payer"
- "Le paiement n'est pas complet. Il reste X FCFA à payer"

**Validation finale**:
- "Vente finalisée avec succès ! Montant total payé : X FCFA en Y paiement(s). Commission perçue : Z FCFA."

## 📈 Statistiques et rapports

Le système permet de suivre:
- Montant total des ventes
- Commission totale perçue
- Nombre de paiements par vente
- Méthodes de paiement utilisées
- Durée moyenne entre la demande et la finalisation

## 🔄 Différences avec les Locations

| Caractéristique | Ventes | Locations |
|----------------|--------|-----------|
| Échéances mensuelles | ❌ Non | ✅ Oui |
| Paiements partiels | ✅ Oui | ✅ Oui |
| Statut final du bien | Vendu + Dépublié | Loué |
| Durée | Ponctuelle | Récurrente |
| Commission | Sur montant total | Sur loyers |
| Types de paiement | Acompte, Solde, Caution | Loyer, Caution, Avance |

## ✅ Résumé des fonctionnalités

- ✅ Pas d'échéances mensuelles
- ✅ Paiement partiel autorisé
- ✅ Historique immuable
- ✅ Bien vendu seulement après paiement total
- ✅ Commission traçable
- ✅ Calcul automatique des commissions proportionnelles
- ✅ Progression visuelle du paiement
- ✅ Validation stricte des montants
- ✅ Protection contre les dépassements
- ✅ Dépublication automatique après vente
- ✅ Récapitulatif financier complet

## 🚀 Utilisation

1. Créer une vente depuis une demande d'intérêt
2. Envoyer la fiche au client
3. Planifier et effectuer la visite
4. Configurer le paiement (prix, commission, frais)
5. Ajouter les paiements au fur et à mesure
6. Valider lorsque le paiement est complet
7. Remettre les clés au client

Le système guide l'utilisateur à chaque étape et empêche les erreurs grâce aux validations automatiques.
