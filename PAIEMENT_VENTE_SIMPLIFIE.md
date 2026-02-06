# Système de paiement simplifié pour les ventes

## Date de mise à jour : 4 février 2026

## Résumé des changements

Le système de paiement des ventes a été simplifié pour :
- **Supprimer** la caution et les frais d'agence du calcul
- **Permettre** uniquement des paiements partiels sur le prix de vente
- **Configurer** la commission une seule fois lors du premier paiement
- **Calculer** la commission sur le montant total de la vente (pas proportionnellement)

## Structure de la base de données

### Table `ventes`

Nouveaux champs ajoutés :
- `commission_configuree` (boolean) : Indique si la commission a été configurée
- `montant_commission_total` (decimal) : Montant total de la commission en FCFA

## Fonctionnement

### 1. Paiements partiels

- Les clients peuvent payer en plusieurs fois
- Chaque paiement est enregistré dans la table `paiements`
- Le montant total à payer = **prix_vente uniquement**
- Plus de caution ni de frais d'agence à payer

### 2. Configuration de la commission

#### Premier paiement
Lors du premier paiement, l'administrateur peut configurer :
- **Type de commission** : Pourcentage ou Montant fixe
- **Valeur** : Par exemple 5% ou 500 000 FCFA

Cette configuration calcule automatiquement le `montant_commission_total` qui reste fixe.

#### Paiements suivants
La commission est déjà configurée et ne peut plus être modifiée.

### 3. Perception de la commission

La commission n'est **perçue qu'une seule fois** lors de la finalisation de la vente :
- Statut = `paiement_valide`
- Le client a payé la totalité du prix de vente
- La commission totale est alors considérée comme perçue

## Interface utilisateur

### Modal d'ajout de paiement

**Si commission non configurée :**
- Section "Configuration de la commission" apparaît
- Champs : Type (pourcentage/montant) et Valeur
- Aperçu du montant calculé en temps réel

**Si commission configurée :**
- Affichage de la commission configurée en lecture seule
- Indication : "Commission configurée: X% (Y FCFA)" ou "X FCFA"

### Page de détails (show)

#### Section Commission
- **Non configurée** : Badge warning "Commission non configurée"
- **Configurée** : Affichage en vert avec le montant total
- **Perçue** : Badge success si statut = paiement_valide

#### Historique des paiements
Tableau simplifié avec 3 colonnes :
- Date
- Montant
- Méthode de paiement (avec référence et notes)

### Page index

Colonne Commission affiche :
- Montant total de la commission
- Type si pourcentage : "(5%)"
- Statut : "✓ Perçue" (vert) ou "En attente" (jaune)

## Méthodes du modèle Vente

### `montantTotalAPayer()`
Retourne uniquement le `prix_vente`

### `calculerCommission()`
- Si commission configurée : retourne `montant_commission_total`
- Sinon : calcule selon le type (pourcentage ou fixe)

### `totalCommissionsPercues()`
- Si statut = 'paiement_valide' ET commission configurée : retourne `montant_commission_total`
- Sinon : retourne 0

## Workflow

```
1. Demande client
   ↓
2. Envoi fiche
   ↓
3. Visite planifiée
   ↓
4. En attente paiement
   ↓
5. Premier paiement → Configuration commission
   ↓
6. Paiements partiels suivants
   ↓
7. Paiement complet (100%)
   ↓
8. Validation finale → Commission perçue
   ↓
9. Statut: paiement_valide
```

## Avantages

✅ **Simplicité** : Pas de calculs complexes de caution/frais
✅ **Flexibilité** : Paiements partiels selon les besoins du client
✅ **Clarté** : Commission configurée une fois, visible partout
✅ **Traçabilité** : Historique immuable des paiements
✅ **Transparence** : Client et admin voient la même information

## Fichiers modifiés

### Modèles
- `app/Models/Vente.php`

### Contrôleurs
- `app/Http/Controllers/VenteController.php`

### Vues
- `resources/views/backend/pages/ventes/show.blade.php`
- `resources/views/backend/pages/ventes/index.blade.php`

### Migrations
- `database/migrations/2026_02_04_000002_simplify_ventes_paiement.php`

## Notes techniques

- La commission est calculée sur le prix de vente total (pas sur les paiements individuels)
- Les paiements sont polymorphiques (table `paiements` avec `payable_type` et `payable_id`)
- L'historique des paiements est immuable (pas de modification/suppression)
- JavaScript calcule l'aperçu de la commission en temps réel dans le modal
