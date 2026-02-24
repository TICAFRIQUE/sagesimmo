# 🏛️ Architecture Technique - Module Rapport Financier

## Diagramme de Classes

```
┌─────────────────────────────────────────┐
│           User (Propriétaire)           │
│                                         │
│ + username                              │
│ + email                                 │
│ + role = 'proprietaire'                 │
├─────────────────────────────────────────┤
│ + annonces(): HasMany                   │
│ + locations(): HasMany                  │
│ + ventes(): HasMany                     │
└────────────┬────────────────────────────┘
             │
             │ 1:N
             ↓
┌─────────────────────────────────────────┐
│          Annonce (Bien)                 │
│                                         │
│ + titre                                 │
│ + adresse                               │
│ + proprietaire_id                       │
│ + commission_agence                     │
│ + type_commission (montant|%)           │
├─────────────────────────────────────────┤
│ + proprietaire(): BelongsTo             │
│ + locations(): HasMany                  │
│ + ventes(): HasMany                     │
│ + charges(): HasMany                    │
└────┬─────────────────────┬──────────────┘
     │                     │
     │ 1:N                 │ 1:N
     ↓                     ↓
┌─────────────────┐  ┌─────────────────────────┐
│   Location      │  │      Charge             │
│                 │  │                         │
│ + loyer_mensuel │  │ + type_charge           │
│ + commission    │  │ + montant               │
│ + statut        │  │ + date_charge           │
└────────┬────────┘  │ + description           │
         │           │ + reference             │
         │           └─────────────────────────┘
         │ 1:N
         ↓
┌─────────────────────────────────────────┐
│          Paiement                       │
│                                         │
│ + payable_type (Location|Vente)         │
│ + payable_id                            │
│ + montant                               │
│ + commission_agence                     │
│ + type_commission                       │
│ + date_paiement                         │
│ + statut (paye|pending|failed)          │
├─────────────────────────────────────────┤
│ + payable(): MorphTo                    │
└─────────────────────────────────────────┘
```

---

## Flux de Données - Rapport Propriétaire

```
                    REQUEST
                      │
                      ↓
        /admin/rapports/proprietaire?date_debut=...
                      │
                      ↓
          RapportController::rapportProprietaire()
                      │
        ┌─────────────┴──────────────┐
        │                            │
        ↓                            ↓
    Validation              Authentification
    (dates)                 (Auth::user())
        │                            │
        └─────────────┬──────────────┘
                      ↓
        Récupérer User propriétaire
                      │
                      ↓
        RapportProprietaireService
        ::genererRapport($proprietaire, $debut, $fin)
                      │
        ┌─────────────┴─────────────┬──────────────┐
        │                           │              │
        ↓                           ↓              ↓
    Biens du               Calculer par        Charges
    propriétaire           bien:               globales
    WHERE                  ├─ Loyers
    proprietaire_id=X      ├─ Ventes
        │                  ├─ Commission
        │                  ├─ Charges
        │                  └─ Net
        │                           │
        └───────────────┬───────────┘
                        │
                        ↓
                   Agrégation
                   ├─ Total Brut
                   ├─ Total Charges
                   ├─ Total Commission
                   └─ Revenu Net
                        │
                        ↓
                    Return Array
                        │
                        ↓
                  View: proprietaire.blade.php
                        │
                        ↓
                    HTML Rapport
                        │
                        ↓
                      Response
```

---

## Flux de Données - Rapport Agence

```
                    REQUEST
                      │
                      ↓
        /admin/rapports/agence?date_debut=...
                      │
                      ↓
          RapportController::rapportAgence()
                      │
        ┌─────────────┴──────────────┐
        │                            │
        ↓                            ↓
    Validation              Authentification
    (dates, admin)          (Auth user must be admin)
        │                            │
        └─────────────┬──────────────┘
                      ↓
        RapportAgenceService
        ::genererRapport($debut, $fin)
                      │
    ┌───────────────┬─┴───────────────┬──────────────┐
    │               │                 │              │
    ↓               ↓                 ↓              ↓
Loyers         Ventes            Detail par    Detail par
Encaissés      Encaissées        Bien          Propriétaire
(status=paye)  (status=paye)     ├─Annonce     ├─ Propriétaire
├─Paiements    ├─Paiements       ├─Adresse     ├─ Nom
├─Commission   ├─Commission      ├─Total       ├─ Total
└─Total        └─Total           ├─Commission  ├─ Commission
                                 └─ Nombre     └─ Nombre
                │                  │             │
                └──────┬───────────┴─────────────┘
                       │
                       ↓
                   Agrégation
                   ├─ Total Encaissé
                   ├─ Total Commission (REVENU AGENCE)
                   ├─ Commissions Loyers
                   └─ Commissions Ventes
                       │
                       ↓
                    Return Array
                       │
                       ↓
                  View: agence.blade.php
                       │
                       ↓
                    HTML Rapport
                       │
                       ↓
                      Response
```

---

## Flux d'Enregistrement de Charge

```
              REQUEST
                │
                ↓
  POST /admin/charges (chargesStore)
                │
    ┌───────────┴────────────┐
    │                        │
    ↓                        ↓
Validation              Autorisation
├─ annonce_id          (est-ce son bien?)
├─ montant > 0         Si Propriétaire
├─ type_charge             └─ Vérifier
├─ date_charge            proprietaire_id
└─ dates format
        │                  │
        └────────┬─────────┘
                 │
                 ↓
        Charge::create([...])
                 │
                 ↓
        INSERT INTO charges (...)
                 │
                 ↓
        Charge enregistrée
                 │
                 ↓
        Redirection vers
        route('charges.index')
                 │
                 ↓
              Response
```

---

## Modèle Données - Table Charges

```
charges
├─ id: BIGINT (PK)
├─ annonce_id: BIGINT (FK → annonces.id)
├─ type_charge: ENUM
│  ├─ 'maintenance'
│  ├─ 'reparation'
│  ├─ 'taxe'
│  └─ 'autre'
├─ montant: DECIMAL(12,2)
├─ date_charge: DATE
├─ description: TEXT (nullable)
├─ reference: VARCHAR(255) (nullable)
├─ notes: TEXT (nullable)
├─ created_at: TIMESTAMP
├─ updated_at: TIMESTAMP
│
└─ Indexes
   ├─ PRIMARY (id)
   ├─ FOREIGN (annonce_id)
   ├─ INDEX (date_charge)
   └─ INDEX (type_charge)
```

---

## Calculs Mathématiques

### Pour Chaque Bien du Rapport Propriétaire

```
┌─ ENCAISSEMENTS
│  ├─ Loyers: SUM(Paiement.montant WHERE type='loyer' AND statut='paye')
│  └─ Ventes: SUM(Paiement.montant WHERE type='vente' AND statut='paye')
│  → Total Brut = Loyers + Ventes
│
├─ DÉDUCTIONS
│  ├─ Charges: SUM(Charge.montant WHERE annonce_id=X)
│  └─ Commission: SUM(Paiement.commission_agence)
│
└─ RÉSULTAT
   Revenu Net = Total Brut - Charges - Commission
```

### Pour Rapport Agence Global

```
┌─ TRANSACTIONS LOYERS
│  Total Paiements = SUM(Paiement.montant)
│  Commission = SUM(Paiement.commission_agence)
│
├─ TRANSACTIONS VENTES
│  Total Paiements = SUM(Paiement.montant)
│  Commission = SUM(Paiement.commission_agence)
│
└─ REVENU AGENCE
   Total Commission = Commission Loyers + Commission Ventes
```

---

## Séquence d'Appels - Rapport Propriétaire

```
1. User → GET /admin/rapports/proprietaire
   ↓
2. RapportController::rapportProprietaire()
   ├─ Validation de la requête
   ├─ Déterminer le propriétaire
   └─ Instancier RapportProprietaireService
   ↓
3. Service::genererRapport($proprietaire, $début, $fin)
   ├─ Récupérer tous les biens
   ├─ Pour chaque bien:
   │  ├─ Service::calculerRapportBien()
   │  │  ├─ Service::calculerEncaissementLoyers()
   │  │  │  └─ Paiement::query()->...->get()
   │  │  ├─ Service::calculerEncaissementVentes()
   │  │  │  └─ Paiement::query()->...->get()
   │  │  ├─ Service::chargesByBien()
   │  │  │  └─ Charge::query()->...->get()
   │  │  ├─ Service::commissionsByBien()
   │  │  │  └─ Paiement::query()->...->get()
   │  │  └─ Calculer: Net = Brut - Commission - Charges
   │  └─ Retourner array bien
   ├─ Agréger tous les biens
   ├─ Détail charges par type
   └─ Retourner rapport complet
   ↓
4. View: proprietaire.blade.php
   ├─ Afficher résumé (KPIs)
   ├─ Afficher détail par bien
   ├─ Afficher résumé charges
   ├─ Afficher calcul net
   └─ Afficher options impression
   ↓
5. Response HTML
```

---

## Points Chauds d'Optimisation

### ⚠️ N+1 Queries

**Problème** : Pour chaque bien, on requête les charges, loyers, ventes, commissions.

**Solution** : Uses `with()` eager loading :
```php
$biens->load('locations', 'ventes', 'charges');
```

### ⚠️ Boucles Imbriquées

**Problème** : Structure complexe avec many foreach imbriquées.

**Solution** : Utiliser Collections Laravel `map()`, `groupBy()`, `sum()`.

### ⚠️ Calculs Flottants

**Problème** : Les montants en FCFA peuvent avoir des imprécisions.

**Solution** : Utiliser `DECIMAL(12,2)` en BD et la conversion en float au final.

---

## Variables d'Environnement (Futures)

```env
# Configuration rapports
RAPPORT_DECIMAL_PLACES=2
RAPPORT_DEVISE_SYMBOLE=F
RAPPORT_DECIMAL_SEPARATOR=,
RAPPORT_THOUSANDS_SEPARATOR= 
```

---

## États Possibles

### Paiement
```
[pending] → [echec] 
    ↓
[paye] ← Seul état compté dans rapports
```

### Location
```
[demande] → [confirmee] → [active] → [terminee]
                            ↓
                    (paiements échéancés ici)
```

### Vente
```
[demande] → [confirmee] → [signee] → [terminee]
                            ↓
                    (paiement ici)
```

---

## Sécurité

### Authentification Requise
```php
middleware(['auth', 'admin']) 
// Admin: tous les endpoints
```

### Contrôle d'Accès

**Propriétaire voit:**
- Uniquement ses rapports (proprietaire_id = Auth::id())
- Ses charges (annonce.proprietaire_id = Auth::id())

**Admin voit:**
- Tous les rapports (paramètre proprietaire_id)
- Tous les biens et charges

---

## Extensibilité Future

### 1. Export PDF
```php
// Générer PDF du rapport
$pdf = PDF::loadView('rapport', $data);
return $pdf->download('rapport-2025-01.pdf');
```

### 2. Envoi Email
```php
// Envoyer rapport propriétaire par email
Mail::send(new RapportProprietaireMail($rapport));
```

### 3. Planification
```php
// Job de rapports mensuels
$job = new GenerateMonthlyReports();
$job->dispatch();
```

### 4. Webhooks
```php
// Notifier service externe
WebhookFacade::dispatch('rapport.generated', $rapport);
```

### 5. API REST
```php
// API pour intégrations externes
Route::get('/api/rapports/proprietaire/{id}', [ApiRapportController::class, 'proprietaire']);
```

