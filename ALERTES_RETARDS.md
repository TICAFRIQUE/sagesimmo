# Système de Gestion des Alertes, Retards et Impayés

## Vue d'ensemble

Le système de gestion des alertes permet de suivre automatiquement les échéances de loyer en retard ou impayées, et d'alerter les administrateurs.

## Statuts des échéances

- **`a_echeance`** : Échéance à venir ou en cours (pas encore dépassée)
- **`partiel`** : Paiement partiel reçu (avant la date d'échéance)
- **`en_retard`** : Date d'échéance dépassée, paiement incomplet ou inexistant (< 30 jours)
- **`impaye`** : Plus de 30 jours de retard
- **`paye`** : Échéance entièrement payée
- **`cloture`** : Échéance clôturée administrativement

## Fonctionnalités

### 1. Mise à jour automatique des statuts

**Commande Artisan :**
```bash
php artisan echeances:verifier-retards
```

Cette commande :
- Vérifie toutes les échéances non payées
- Met à jour automatiquement les statuts selon les dates
- Affiche un rapport détaillé (nombre en retard, impayées, etc.)

**Planification automatique :**
La commande s'exécute automatiquement tous les jours à 1h du matin via le scheduler Laravel.

Pour activer le scheduler sur votre serveur, ajoutez cette ligne au crontab :
```bash
* * * * * cd /chemin/vers/sage_immo && php artisan schedule:run >> /dev/null 2>&1
```

### 2. Dashboard des alertes

**Accès :** Menu Admin > "ALERTES & RETARDS"

**URL :** `/admin/alertes`

Le dashboard affiche :

#### Cartes statistiques
- **Impayées (>30j)** : Nombre et montant total des échéances impayées
- **En retard** : Nombre et montant des échéances en retard (< 30 jours)
- **À venir (7j)** : Échéances des 7 prochains jours
- **Total en attente** : Montant total à récupérer

#### Tableaux détaillés
1. **Échéances impayées** (fond rouge)
   - Plus de 30 jours de retard
   - Priorité urgente
   - Informations : locataire, bien, montants, nombre de jours de retard

2. **Échéances en retard** (fond jaune)
   - Moins de 30 jours de retard
   - À surveiller
   - Même niveau de détail

3. **Échéances à venir** (fond bleu)
   - 7 prochains jours
   - Pour anticipation et rappels préventifs

### 3. Badge de notification dans le menu

Un badge rouge apparaît automatiquement dans le menu "ALERTES & RETARDS" indiquant le nombre total d'échéances en retard ou impayées.

### 4. Méthodes du modèle Echeance

```php
// Mettre à jour le statut automatiquement
$echeance->mettreAJourStatut();

// Vérifier si en retard
$echeance->estEnRetard(); // true/false

// Obtenir le nombre de jours de retard
$echeance->joursDeRetard(); // 0, 5, 35, etc.

// Obtenir le niveau de priorité (1=urgent, 4=ok)
$echeance->niveauPriorite(); // 1, 2, 3 ou 4

// Montant restant à payer
$echeance->montantRestant(); // 50000.00

// Badge HTML formaté
$echeance->statut_badge; // <span class="badge bg-warning">...</span>
```

### 5. Scopes de requête

```php
// Toutes les échéances en retard
$enRetard = Echeance::enRetard()->get();

// Toutes les échéances impayées (>30 jours)
$impayees = Echeance::impayees()->get();

// Échéances à venir dans les 7 prochains jours
$aVenir = Echeance::aVenir(7)->get();

// Échéances à venir dans les 14 prochains jours
$aVenir = Echeance::aVenir(14)->get();
```

## Workflow de gestion des retards

### Détection automatique
1. Chaque jour à 1h : le scheduler exécute `echeances:verifier-retards`
2. Les échéances dépassées passent de `a_echeance` à `en_retard`
3. Après 30 jours, le statut passe de `en_retard` à `impaye`

### Actions manuelles
1. L'admin consulte le dashboard "ALERTES & RETARDS"
2. Il voit les échéances problématiques triées par urgence
3. Il clique sur "Voir" pour accéder à la location
4. Il peut enregistrer un paiement ou prendre d'autres mesures

### Actualisation manuelle
Bouton "Actualiser les statuts" en haut du dashboard pour forcer une mise à jour immédiate.

## Personnalisation

### Modifier le seuil d'impayé
Par défaut : 30 jours. Pour changer, éditez `app/Models/Echeance.php` ligne ~55 :

```php
if ($joursRetard > 30) { // Changez 30 par votre valeur
    $this->statut = 'impaye';
}
```

### Modifier la période "À venir"
Par défaut : 7 jours. Dans le contrôleur `AlerteController.php` ligne ~31 :

```php
$aVenir = Echeance::aVenir(7)->get(); // Changez 7
```

### Modifier l'horaire du scheduler
Dans `routes/console.php` :

```php
Schedule::command('echeances:verifier-retards')->dailyAt('01:00'); // Changez l'heure
```

Options disponibles :
- `->hourly()` : toutes les heures
- `->daily()` : tous les jours à minuit
- `->dailyAt('13:00')` : tous les jours à 13h
- `->twiceDaily(1, 13)` : 2 fois par jour (1h et 13h)

## Notifications futures (à implémenter)

Pour aller plus loin, vous pourriez ajouter :

1. **Emails automatiques** aux locataires en retard
2. **SMS** de rappel avant échéance
3. **Notifications** push dans l'app
4. **Export PDF** des échéances en retard
5. **Rapport mensuel** envoyé au propriétaire

## Commandes utiles

```bash
# Vérifier les retards manuellement
php artisan echeances:verifier-retards

# Tester le scheduler localement
php artisan schedule:run

# Voir toutes les tâches planifiées
php artisan schedule:list
```

## Support

Pour toute question ou personnalisation supplémentaire, consultez la documentation Laravel sur :
- [Task Scheduling](https://laravel.com/docs/scheduling)
- [Eloquent Scopes](https://laravel.com/docs/eloquent#local-scopes)
- [Artisan Commands](https://laravel.com/docs/artisan)
