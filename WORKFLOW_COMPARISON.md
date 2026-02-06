# Comparaison des Workflows : Ventes vs Locations

## 📊 Vue d'ensemble

Ce document compare les deux workflows de paiement implémentés dans le système de gestion immobilière.

## 🏢 Tableau comparatif complet

| Critère | 🏠 Ventes | 🔑 Locations |
|---------|-----------|--------------|
| **Type de transaction** | Ponctuelle | Récurrente |
| **Échéances mensuelles** | ❌ Non | ✅ Oui (générées automatiquement) |
| **Paiements partiels** | ✅ Oui | ✅ Oui |
| **Historique immuable** | ✅ Oui | ✅ Oui |
| **Statut final du bien** | Vendu + Dépublié | Loué (disponible à la fin) |
| **Types de paiement** | Acompte, Solde, Caution, Frais agence | Loyer, Caution, Avance, Frais agence |
| **Commission** | Sur montant total de vente | Sur chaque loyer payé |
| **Calcul commission** | Proportionnelle aux paiements | Proportionnelle à chaque loyer |
| **Validation finale** | Paiement complet requis | Location active dès premier loyer |
| **Durée** | Transaction unique | Contrat avec date de début/fin |

## 🔄 Workflows étape par étape

### 🏠 Workflow Ventes

```
1. Demande client
   ↓
2. Envoi de la fiche
   ↓
3. Planification de la visite
   ↓
4. Visite effectuée
   ↓
5. Configuration du paiement
   ↓
6. Ajout des paiements (un ou plusieurs)
   ↓
7. Validation du paiement complet
   ↓
8. Bien vendu + Dépublié
```

### 🔑 Workflow Locations

```
1. Demande client
   ↓
2. Envoi de la fiche
   ↓
3. Planification de la visite
   ↓
4. Visite effectuée
   ↓
5. Configuration de la location (durée, loyer)
   ↓
6. Génération automatique des échéances mensuelles
   ↓
7. Ajout des paiements (pour chaque échéance)
   ↓
8. Suivi mensuel des paiements
   ↓
9. Bien loué (reste loué jusqu'à la fin du contrat)
```

## 💰 Gestion des paiements

### 🏠 Ventes

**Montant total à payer**:
```
Total = Prix de vente + Caution + Frais d'agence
```

**Types de paiement**:
- **Acompte**: Premier versement partiel
- **Solde**: Paiement du reste
- **Caution**: Garantie (si applicable)
- **Frais d'agence**: Frais administratifs

**Exemple de paiements**:
```
Prix de vente: 10,000,000 FCFA
Commission: 5% = 500,000 FCFA

Paiement 1 (Acompte): 3,000,000 FCFA
  → Commission: 150,000 FCFA (30%)
  → Reste: 7,000,000 FCFA

Paiement 2 (Solde): 7,000,000 FCFA
  → Commission: 350,000 FCFA (70%)
  → Reste: 0 FCFA ✅

Total commission: 500,000 FCFA ✅
```

### 🔑 Locations

**Montant par échéance**:
```
Échéance = Loyer mensuel + (Caution au 1er mois) + (Frais agence au 1er mois)
```

**Types de paiement**:
- **Loyer**: Paiement mensuel récurrent
- **Caution**: Garantie (première échéance)
- **Avance**: Paiement anticipé de plusieurs mois
- **Frais d'agence**: Frais administratifs (première échéance)

**Exemple d'échéances**:
```
Loyer mensuel: 200,000 FCFA
Commission: 10% = 20,000 FCFA par mois
Durée: 12 mois

Mois 1:
  Échéance: 200,000 FCFA + 200,000 (caution) = 400,000 FCFA
  Paiement: 400,000 FCFA
  Commission: 20,000 FCFA

Mois 2:
  Échéance: 200,000 FCFA
  Paiement: 200,000 FCFA
  Commission: 20,000 FCFA

[...continues pour 12 mois]

Total loyers: 2,400,000 FCFA
Total commission: 240,000 FCFA (12 × 20,000)
```

## 📈 Calcul des commissions

### 🏠 Ventes

**Méthode 1: Pourcentage**
```php
Commission totale = (Prix de vente × Pourcentage) / 100

Commission par paiement = Commission totale × (Montant paiement / Prix de vente)
```

**Exemple**:
- Prix: 10,000,000 FCFA
- Commission: 5%
- Commission totale: 500,000 FCFA
- Paiement 1 (30%): 150,000 FCFA de commission
- Paiement 2 (70%): 350,000 FCFA de commission

**Méthode 2: Montant fixe**
```php
Commission totale = Montant défini

Commission par paiement = Commission totale × (Montant paiement / Prix de vente)
```

### 🔑 Locations

**Méthode 1: Pourcentage**
```php
Commission par mois = (Loyer mensuel × Pourcentage) / 100

Total commission = Commission par mois × Nombre de mois
```

**Exemple**:
- Loyer: 200,000 FCFA/mois
- Commission: 10%
- Commission/mois: 20,000 FCFA
- Sur 12 mois: 240,000 FCFA

**Méthode 2: Montant fixe**
```php
Commission par mois = Montant fixe défini

Total commission = Commission par mois × Nombre de mois
```

## 🎯 Règles de validation

### 🏠 Ventes

| Règle | Description |
|-------|-------------|
| **Paiement partiel** | Montant ne peut pas dépasser le reste à payer |
| **Validation finale** | Le paiement doit être complet (100%) |
| **Commission** | Calculée proportionnellement sur chaque paiement |
| **Statut du bien** | Marqué "vendu" et dépublié après paiement complet |
| **Modification** | Aucun paiement ne peut être modifié ou supprimé |

### 🔑 Locations

| Règle | Description |
|-------|-------------|
| **Paiement partiel** | Montant ne peut pas dépasser le montant de l'échéance |
| **Échéances** | Générées automatiquement à l'activation |
| **Commission** | Calculée sur chaque loyer payé |
| **Statut du bien** | Marqué "loué" dès la première échéance payée |
| **Retards** | Alertes automatiques pour échéances impayées |

## 📊 Modèles de données

### Vente

```php
class Vente {
    // Montants
    prix_vente
    montant_caution
    montant_frais_agence
    commission_agence
    type_commission // 'pourcentage' ou 'montant'
    
    // Dates
    date_vente
    date_signature
    date_visite
    date_finalisation
    
    // Statuts
    // 'demande_client', 'fiche_envoyee', 'visite_planifiee',
    // 'en_attente_paiement', 'paiement_valide', 'annule'
    statut
    
    // Méthodes
    montantTotalAPayer()      // Prix + caution + frais
    montantTotalPaye()        // Somme des paiements
    resteAPayer()             // Total - Payé
    estEntierementPaye()      // Booléen
    pourcentagePaiement()     // 0-100%
    totalCommissionsPercues() // Total commissions
}
```

### Location

```php
class Location {
    // Montants
    loyer_mensuel
    montant_caution
    montant_frais_agence
    commission_agence
    type_commission
    
    // Dates
    date_debut
    date_fin
    duree_mois
    date_visite
    date_signature
    
    // Statuts
    // 'demande_client', 'fiche_envoyee', 'visite_planifiee',
    // 'en_attente_paiement', 'location_active', 'terminee', 'annule'
    statut
    
    // Méthodes
    genererEcheances()         // Création des échéances
    montantTotalAttendu()      // Loyers × durée
    montantTotalPaye()         // Somme des paiements
    pourcentagePaiement()      // 0-100%
    echeancesEnRetard()        // Liste des retards
    totalCommissionsPercues()  // Total commissions
}
```

### Paiement (polymorphe)

```php
class Paiement {
    // Relations
    payable_type  // 'App\Models\Vente' ou 'App\Models\Location'
    payable_id
    echeance_id   // null pour ventes, id pour locations
    
    // Données
    type_paiement      // 'acompte', 'solde', 'loyer', etc.
    montant
    commission_agence
    type_commission
    date_paiement
    methode_paiement
    reference
    notes
    statut            // 'paye', 'en_attente', 'en_retard'
    
    // Méthodes
    montant_commission      // Calcul auto de la commission
    commission_formattee    // Affichage formaté
}
```

## 🔐 Sécurité commune

Les deux workflows partagent les mêmes principes de sécurité:

- ✅ **Immuabilité**: Aucun paiement ne peut être modifié ou supprimé
- ✅ **Traçabilité**: Historique complet de tous les paiements
- ✅ **Validation**: Contrôles automatiques des montants
- ✅ **Protection**: Empêche les dépassements de montant
- ✅ **Intégrité**: Commissions calculées automatiquement
- ✅ **Audit**: Toutes les actions sont datées et tracées

## 📱 Interface utilisateur

### Éléments communs

Les deux workflows partagent:
- Barre de progression visuelle
- Affichage des étapes du workflow
- Tableau d'historique des paiements
- Boutons d'action contextuels
- Alertes de validation
- Récapitulatifs financiers

### Éléments spécifiques

**Ventes uniquement**:
- Affichage du montant restant à payer
- Bouton "Valider le paiement complet"
- Message "Bien vendu"

**Locations uniquement**:
- Calendrier des échéances
- Alertes de retard
- Statuts d'échéances (payée, en attente, en retard)
- Bouton "Activer la location"
- Gestion de la durée du contrat

## 📈 Statistiques et rapports

### 🏠 Ventes

```
- Nombre de ventes réalisées
- Montant total des ventes
- Commission totale perçue
- Nombre moyen de paiements par vente
- Durée moyenne de finalisation
- Méthodes de paiement préférées
```

### 🔑 Locations

```
- Nombre de locations actives
- Montant total des loyers perçus
- Commission totale perçue
- Taux de paiement dans les délais
- Nombre d'échéances en retard
- Taux d'occupation des biens
```

## 🚀 Cas d'usage

### 🏠 Vente typique

```
1. Client intéressé par une villa à 15,000,000 FCFA
2. Visite organisée et validée
3. Configuration: Prix 15M + Caution 500K + Frais 200K = 15,700,000 FCFA
4. Commission: 3% = 450,000 FCFA
5. Paiement 1 (Acompte): 5,000,000 FCFA → Commission: 150,000 FCFA
6. Paiement 2 (Solde): 10,700,000 FCFA → Commission: 300,000 FCFA
7. Validation: Bien vendu, clés remises
```

### 🔑 Location typique

```
1. Client intéressé par un appartement à 250,000 FCFA/mois
2. Visite organisée et validée
3. Configuration: Loyer 250K, Durée 24 mois, Commission 10%
4. Génération de 24 échéances mensuelles
5. Mois 1: Paiement 250K + 250K caution = 500K → Commission: 25K
6. Mois 2-24: Paiement 250K/mois → Commission: 25K/mois
7. Total: 6,000,000 FCFA de loyers, 600,000 FCFA de commission
```

## 🎯 Avantages de chaque système

### 🏠 Ventes

✅ Simplicité: Pas de gestion mensuelle
✅ Flexibilité: Paiements partiels libres
✅ Finalité: Transaction unique et claire
✅ Traçabilité: Historique complet des versements

### 🔑 Locations

✅ Récurrence: Revenus mensuels prévisibles
✅ Automatisation: Échéances générées automatiquement
✅ Suivi: Alertes de retard automatiques
✅ Durée: Contrat avec début et fin définis

## 📝 Conclusion

Les deux workflows partagent les mêmes principes fondamentaux:
- ✅ Paiements partiels autorisés
- ✅ Historique immuable
- ✅ Commission traçable
- ✅ Validation stricte

Mais s'adaptent aux spécificités de chaque type de transaction:
- **Ventes**: Transaction unique, paiement total requis
- **Locations**: Paiements récurrents, échéances mensuelles

Le système offre ainsi une gestion complète et professionnelle des transactions immobilières.
