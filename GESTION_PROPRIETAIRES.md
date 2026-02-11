# Gestion des Propriétaires - Biens de l'Agence vs Biens Externes

## Vue d'ensemble

Le système permet maintenant à l'agence de créer des annonces pour deux types de biens :

1. **Biens de l'agence** : Biens qui appartiennent directement à l'agence immobilière
2. **Biens externes** : Biens appartenant à des propriétaires externes

Cette fonctionnalité permet une meilleure gestion des actifs immobiliers et une distinction claire entre les biens dont l'agence est propriétaire et ceux qu'elle gère pour le compte de tiers.

## Fonctionnalités

### 1. Création d'annonce

Lors de la création d'une nouvelle annonce, l'utilisateur peut choisir le type de propriétaire :

- **Propriétaire externe** : Un propriétaire doit être sélectionné dans la liste des utilisateurs
- **Bien de l'agence** : Aucun propriétaire externe n'est requis

#### Interface utilisateur

Dans le formulaire de création/modification d'annonce :
- Des boutons radio permettent de choisir entre "Propriétaire externe" et "Bien de l'agence"
- Le champ de sélection du propriétaire s'affiche/se masque automatiquement selon le choix
- Si "Bien de l'agence" est sélectionné, le champ propriétaire devient optionnel

### 2. Validation

La validation des données s'adapte automatiquement :
- Pour un **bien externe** : le champ `proprietaire_id` est **obligatoire**
- Pour un **bien de l'agence** : le champ `proprietaire_id` est **optionnel**

### 3. Affichage

#### Liste des annonces
- Un badge vert avec icône de bâtiment indique "Agence" pour les biens de l'agence
- Le nom du propriétaire s'affiche pour les biens externes
- Info-bulle présente au survol pour clarifier le type de bien

#### Détails de l'annonce
- Section "Type de propriété" avec badge distinctif :
  - Badge vert pour "Bien de l'agence"
  - Badge bleu pour "Bien d'un propriétaire externe"
- Les informations du propriétaire ne s'affichent que pour les biens externes

## Structure de la base de données

### Champ ajouté

```sql
est_bien_agence BOOLEAN DEFAULT false
```

**Description** : Indique si le bien appartient à l'agence (true) ou à un propriétaire externe (false)

### Modification

```sql
proprietaire_id BIGINT UNSIGNED NULLABLE
```

Le champ `proprietaire_id` est maintenant nullable pour permettre les biens de l'agence sans propriétaire externe assigné.

## Utilisation dans le code

### Scopes disponibles

Le modèle `Annonce` fournit plusieurs scopes pour filtrer les annonces :

```php
// Récupérer uniquement les biens de l'agence
$biensAgence = Annonce::bienAgence()->get();

// Récupérer uniquement les biens externes
$biensExternes = Annonce::bienExterne()->get();
```

### Méthodes utilitaires

```php
// Vérifier si un bien appartient à l'agence
if ($annonce->appartientAgence()) {
    // Logique spécifique aux biens de l'agence
}

// Obtenir le nom du propriétaire (retourne "Agence" si c'est un bien de l'agence)
$nomProprietaire = $annonce->nom_proprietaire;
```

### Relations

```php
// Propriétaire du bien (peut être null pour les biens de l'agence)
$annonce->proprietaire;

// Utilisateur qui a créé l'annonce
$annonce->createdBy;
```

## Cas d'usage

### Scénario 1 : Vente d'un bien de l'agence

L'agence possède un immeuble et souhaite le vendre :
1. Créer une nouvelle annonce
2. Sélectionner "Bien de l'agence"
3. Remplir les détails du bien
4. Le bien sera clairement identifié comme appartenant à l'agence

**Avantages** :
- Pas besoin de créer un utilisateur fictif "Agence"
- Meilleure séparation des comptes et des biens
- Rapports et statistiques plus précis

### Scénario 2 : Gestion d'un bien pour un client

Un propriétaire externe confie son bien à l'agence :
1. Créer une nouvelle annonce
2. Sélectionner "Propriétaire externe"
3. Choisir le propriétaire dans la liste
4. Remplir les détails du bien
5. Le bien sera associé au propriétaire externe

**Avantages** :
- Traçabilité complète
- Le propriétaire peut être notifié des activités
- Commission et partage des revenus facilités

## Rapports et analyses

Cette distinction permet de générer des rapports spécifiques :

- **Portefeuille de l'agence** : Liste de tous les biens appartenant à l'agence
- **Biens gérés** : Biens appartenant à des propriétaires externes
- **Performance par type** : Comparer les performances entre biens propres et biens gérés
- **Commissions** : Calculer automatiquement les commissions uniquement sur les biens externes

## Migration

Pour mettre à jour les données existantes :

```bash
php artisan migrate
```

Cette commande ajoute le champ `est_bien_agence` (par défaut à `false`) et rend `proprietaire_id` nullable.

### Mise à jour des données existantes (si nécessaire)

Si vous souhaitez marquer certains biens existants comme appartenant à l'agence :

```php
use App\Models\Annonce;

// Exemple : marquer des annonces spécifiques comme biens de l'agence
Annonce::whereIn('id', [1, 2, 3])->update([
    'est_bien_agence' => true,
    'proprietaire_id' => null
]);
```

## Permissions et sécurité

- Seuls les utilisateurs avec les permissions appropriées peuvent créer/modifier des annonces
- L'utilisateur qui crée l'annonce est enregistré dans `created_by_id`
- La distinction propriétaire/agence est indépendante des permissions utilisateur

## Extensions futures possibles

1. **Module de gestion de patrimoine** : Tableau de bord dédié aux biens de l'agence
2. **Alertes différenciées** : Notifications spécifiques selon le type de bien
3. **Comptabilité séparée** : Gestion financière distincte pour les biens propres et gérés
4. **Contrats automatiques** : Génération de contrats différents selon le type de propriétaire

## Support

Pour toute question ou problème concernant cette fonctionnalité, veuillez consulter :
- Le code du modèle : `/app/Models/Annonce.php`
- Le contrôleur : `/app/Http/Controllers/backend/AnnonceController.php`
- Les vues : `/resources/views/backend/pages/annonces/`
