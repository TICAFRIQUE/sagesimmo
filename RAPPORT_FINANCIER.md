# 📊 Module de Rapport Financier par Période

## Vue d'ensemble

Ce module permet une gestion complète des rapports financiers pour une plateforme immobilière. Il offre deux perspectives principales :

1. **Rapport Propriétaire** : Affiche les revenus d'un propriétaire spécifique
2. **Rapport Agence** : Affiche les revenus et commissions de l'agence

---

## 🏗️ Architecture

### Modèles

#### `Charge` (App\Models\Charge)
Enregistre toutes les charges liées à un bien immobilier :
- **Champs** :
  - `annonce_id` : Référence au bien
  - `type_charge` : maintenance | reparation | taxe | autre
  - `montant` : Montant en FCFA
  - `date_charge` : Date de la charge
  - `description` : Description détaillée
  - `reference` : Numéro de facture/référence

- **Relations** :
  - `annonce()` : BelongsTo Annonce
  - `proprietaire()` : Via l'annonce

### Services

#### `RapportProprietaireService`
Calcule les rapports financiers des propriétaires :

```php
use App\Services\RapportProprietaireService;

$service = new RapportProprietaireService();
$rapport = $service->genererRapport(
    $proprietaire,
    Carbon::parse('2025-01-01'),
    Carbon::parse('2025-12-31')
);
```

**Résultat** :
```php
[
    'proprietaire' => User,
    'date_debut' => Carbon,
    'date_fin' => Carbon,
    'periode' => 'String formatted',
    'biens' => Collection,  // Détail par bien
    'nombre_biens' => int,
    'total_brut_encaisse' => float,
    'total_charges' => float,
    'total_commission_agence' => float,
    'revenue_net' => float,
    'detail_charges' => array  // Résumé des charges
]
```

#### `RapportAgenceService`
Calcule les rapports financiers de l'agence :

```php
use App\Services\RapportAgenceService;

$service = new RapportAgenceService();
$rapport = $service->genererRapport(
    Carbon::parse('2025-01-01'),
    Carbon::parse('2025-12-31')
);
```

**Résultat** :
```php
[
    'date_debut' => Carbon,
    'date_fin' => Carbon,
    'periode' => 'String formatted',
    'total_loyers_encaisses' => float,
    'total_ventes_encaissees' => float,
    'total_encaisse' => float,
    'detail_loyers' => array,  // Détails commissions loyers
    'detail_ventes' => array,  // Détails commissions ventes
    'total_commissions' => float,  // REVENU AGENCE
    'commissions_loyers' => float,
    'commissions_ventes' => float,
    'detail_par_bien' => Collection,
    'detail_par_proprietaire' => Collection,
    'detail_par_type' => array
]
```

---

## 🎮 Contrôle (RapportController)

### Méthodes Publiques

#### `rapportProprietaire(Request $request)`
Affiche le rapport financier d'un propriétaire.

**Paramètres** :
- `proprietaire_id` (optionnel) : ID du propriétaire
- `date_debut` : Date début au format Y-m-d
- `date_fin` : Date fin au format Y-m-d

**Accès** : Admin (peut voir tous) + Propriétaire (voit le sien)

**Route** : `GET /admin/rapports/proprietaire`

```blade
// Utilisation dans blade
<a href="{{ route('rapports.proprietaire', ['proprietaire_id' => $prop->id, 'date_debut' => '2025-01-01', 'date_fin' => '2025-12-31']) }}">
    Voir rapport
</a>
```

#### `rapportAgence(Request $request)`
Affiche le rapport financier de l'agence.

**Paramètres** :
- `date_debut` : Date début au format Y-m-d
- `date_fin` : Date fin au format Y-m-d

**Accès** : Admin only

**Route** : `GET /admin/rapports/agence`

#### Gestion des Charges

```php
// Liste des charges
GET /admin/charges
// Paramètres : annonce_id, type_charge, date_debut, date_fin

// Créer une charge
GET /admin/charges/create
POST /admin/charges

// Éditer une charge
GET /admin/charges/{charge}/edit
PUT /admin/charges/{charge}

// Supprimer une charge
DELETE /admin/charges/{charge}
```

---

## 📊 Formules de Calcul

### Revenu Net du Propriétaire

```
Revenu Net = Total Encaissé - Commission Agence - Total Charges

Où :
- Total Encaissé = Loyers Encaissés + Ventes Encaissées
- Commission Agence = Commissions prélevées sur loyers + ventes
- Total Charges = Somme des charges (maintenance, réparation, taxes, autres)
```

### Revenu de l'Agence

```
Revenu Agence = Total Commissions Perçues

Où :
- Commission Loyer = Commission × Montant du Loyer (selon type %)
- Commission Vente = Commission × Prix de Vente (selon type %)
- Total Commissions = Commission Loyers + Commission Ventes
```

---

## 📋 Cas d'Usage

### Cas 1 : Propriétaire veut voir ses revenus annuels

```php
// Login en tant que propriétaire
// Accéder à : /admin/rapports/proprietaire
// Le système affiche automatiquement SES biens
// Il peut filtrer par période
```

**Résultat affiché** :
```
Bien: Maison à Dakar
├─ Loyers encaissés: 1 200 000 F
├─ Commission agence: 60 000 F
├─ Charges (maintenance): 50 000 F
└─ Revenu Net: 1 090 000 F

Bien: Appartement à Thiès
├─ Loyers encaissés: 800 000 F
├─ Commission agence: 40 000 F
├─ Charges (réparation): 150 000 F
└─ Revenu Net: 610 000 F

TOTAL REVENU NET PROPRIÉTAIRE: 1 700 000 F
```

### Cas 2 : Admin veut voir les revenus de l'agence

```php
// Login en tant qu'admin
// Accéder à : /admin/rapports/agence
// Filtrer par période (mois, trimestre, année)
```

**Résultat affiché** :
```
Total Encaissé: 50 000 000 F
├─ Loyers: 30 000 000 F
└─ Ventes: 20 000 000 F

Commissions Perçues (REVENU AGENCE): 2 500 000 F
├─ Commission Loyers: 1 500 000 F
└─ Commission Ventes: 1 000 000 F

Top Biens: Maison Plateau (800k), Appart Medina (750k)...
Top Propriétaires: Sidy Diallo (600k), Fatou Seck (500k)...
```

### Cas 3 : Enregistrer une charge de maintenance

```php
// Login en tant qu'admin ou propriétaire
// Aller à : /admin/charges/create

[Formulaire]
Bien: Maison à Dakar
Type: Maintenance
Montant: 75 000 F
Date: 2025-02-15
Référence: FAC-2025-042
Description: Ramonage de la cheminée

// Cette charge apparaît dans le rapport propriétaire
// Et est déduite du revenu net
```

---

## 🔐 Contrôle d'Accès

| Ressource | Admin | Propriétaire | Locataire | Client |
|-----------|-------|--------------|-----------|--------|
| Rapport Propriétaire (tous) | ✅ | ❌ | ❌ | ❌ |
| Rapport Propriétaire (le sien) | ✅ | ✅ | ❌ | ❌ |
| Rapport Agence | ✅ | ❌ | ❌ | ❌ |
| Créer Charge | ✅ | ✅* | ❌ | ❌ |
| Éditer Charge | ✅ | ✅* | ❌ | ❌ |
| Supprimer Charge | ✅ | ✅* | ❌ | ❌ |

*Propriétaire : uniquement pour ses propres biens

---

## 🗄️ Migration Exemple

```bash
php artisan migrate
```

Crée la table `charges` avec structure :
```sql
CREATE TABLE charges (
    id BIGINT PRIMARY KEY,
    annonce_id BIGINT NOT NULL,
    type_charge ENUM('maintenance', 'reparation', 'taxe', 'autre'),
    montant DECIMAL(12,2),
    date_charge DATE,
    description TEXT,
    reference VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (annonce_id) REFERENCES annonces(id) ON DELETE CASCADE
);
```

---

## 📱 Endpoints Disponibles

### Rapports
- `GET /admin/rapports/proprietaire` - Rapport propriétaire
- `GET /admin/rapports/agence` - Rapport agence
- `GET /admin/rapports/commissions` - Rapport commissions (existant)
- `GET /admin/rapports/statistiques` - Statistiques (existant)

### Charges
- `GET /admin/charges` - Liste des charges (pagée)
- `GET /admin/charges/create` - Formulaire création
- `POST /admin/charges` - Enregistrer charge
- `GET /admin/charges/{charge}/edit` - Formulaire édition
- `PUT /admin/charges/{charge}` - Mettre à jour charge
- `DELETE /admin/charges/{charge}` - Supprimer charge

---

## 🎨 Vues Blade Créées

1. **`rapports/proprietaire.blade.php`** - Rapport détaillé propriétaire
2. **`rapports/agence.blade.php`** - Rapport financier agence
3. **`rapports/charges/index.blade.php`** - Liste des charges
4. **`rapports/charges/create.blade.php`** - Créer charge
5. **`rapports/charges/edit.blade.php`** - Éditer charge

---

## 💾 Base de Données : Relation

```
User (propriétaire)
  ↓
Annonce (bien)
  ↓
Charge (charges)
```

```
User (propriétaire)
  ↓
Annonce (bien)
  ↓
Location/Vente
  ↓
Paiement (avec commission_agence)
```

---

## 🔄 Flux de Données

### Rapport Propriétaire

```
Propriétaire
    ↓
Annonces (WHERE proprietaire_id)
    ├─ Loyers (Paiement WHERE type='loyer')
    ├─ Ventes (Paiement WHERE type='vente')
    ├─ Charges (Charge WHERE annonce_id)
    └─ Commissions (Paiement.commission_agence)
    ↓
Service calcule:
    - Total Brut = Loyers + Ventes
    - Total Charges = SUM(charge.montant)
    - Total Commission = SUM(paiement.commission)
    - Revenu Net = Total Brut - Charges - Commission
```

### Rapport Agence

```
Tous les Paiements (status='paye')
    ├─ Loyers (Paiement WHERE type='loyer')
    ├─ Ventes (Paiement WHERE type='vente')
    ↓
Service calcule:
    - Total Encaissé = SUM(paiement.montant)
    - Total Commission = SUM(paiement.commission)
    - Groupé par bien, propriétaire, type
    ↓
Rapport Agence = Total Commissions
```

---

## 🚀 Installation

1. **Créer la migration** :
   ```bash
   php artisan migrate
   ```

2. **Modèles créés** :
   - `App\Models\Charge`

3. **Services créés** :
   - `App\Services\RapportProprietaireService`
   - `App\Services\RapportAgenceService`

4. **Routes ajoutées** :
   - Voir section "Endpoints Disponibles"

5. **Vues créées** :
   - 5 fichiers blade (voir section "Vues Blade")

---

## 📝 Exemple d'Utilisation Programmatique

```php
<?php

use App\Services\RapportProprietaireService;
use App\Services\RapportAgenceService;
use App\Models\User;
use Carbon\Carbon;

// Rapport d'un propriétaire
$proprietaire = User::findOrFail(123);
$service = new RapportProprietaireService();
$rapport = $service->genererRapport(
    $proprietaire, 
    Carbon::parse('2025-01-01'),
    Carbon::parse('2025-12-31')
);

echo "Propriétaire: " . $rapport['proprietaire']->username;
echo "Revenu Net: " . $rapport['revenue_net'] . " F";

// Rapport de l'agence
$serviceAgence = new RapportAgenceService();
$rapportAgence = $serviceAgence->genererRapport(
    Carbon::parse('2025-01-01'),
    Carbon::parse('2025-12-31')
);

echo "Commissions Agence: " . $rapportAgence['total_commissions'] . " F";
```

---

## 🐛 Débogage

### Afficher les charges d'un bien
```blade
@foreach($bien->charges as $charge)
    {{ $charge->type_charge }}: {{ number_format($charge->montant, 0) }} F
@endforeach
```

### Vérifier les permissions
```php
// Si admin: voir tous
// Si propriétaire: voir ses biens seulement
Auth::user()->role === 'admin' // true pour admin
Auth::user()->role === 'proprietaire' // true pour propriétaire
```

---

## ✅ Checklist de Déploiement

- [ ] Exécuter la migration : `php artisan migrate`
- [ ] Tester rapport propriétaire (admin)
- [ ] Tester rapport propriétaire (propriétaire)
- [ ] Tester rapport agence
- [ ] Créer une charge
- [ ] Éditer une charge
- [ ] Supprimer une charge
- [ ] Vérifier les calculs
- [ ] Tester impression (print)
- [ ] Vérifier les accès (permissions)

