# Guide d'utilisation de Select2

## Présentation

Select2 est maintenant intégré dans l'application Sage Immo, offrant une recherche rapide et une meilleure expérience utilisateur pour tous les champs select.

## Fonctionnalités

- ✅ **Recherche rapide** : Tapez pour filtrer les options
- ✅ **Interface élégante** : Thème Bootstrap 5 intégré
- ✅ **Multi-langues** : Messages en français
- ✅ **Responsive** : S'adapte à tous les écrans
- ✅ **Animations fluides** : Transitions douces
- ✅ **Clear button** : Bouton pour effacer la sélection
- ✅ **Initialisation automatique** : Tous les champs `<select>` sont automatiquement améliorés

## Utilisation

### Automatique (par défaut)

Tous les champs `<select>` sont automatiquement transformés en Select2 :

```blade
<select class="form-select" name="ville">
    <option value="">Sélectionnez une ville</option>
    <option value="dakar">Dakar</option>
    <option value="thies">Thiès</option>
    <option value="saint-louis">Saint-Louis</option>
</select>
```

### Désactiver Select2 sur un champ spécifique

Si vous souhaitez qu'un champ select reste natif, ajoutez la classe `no-select2` :

```blade
<select class="form-select no-select2" name="simple">
    <option value="1">Option 1</option>
    <option value="2">Option 2</option>
</select>
```

### Personnaliser le placeholder

Utilisez l'attribut `data-placeholder` :

```blade
<select class="form-select" name="commune" data-placeholder="Recherchez une commune...">
    <option value=""></option>
    <option value="dakar-plateau">Dakar Plateau</option>
    <option value="parcelles">Parcelles Assainies</option>
</select>
```

### Select multiple

```blade
<select class="form-select" name="equipements[]" multiple>
    <option value="wifi">WiFi</option>
    <option value="parking">Parking</option>
    <option value="piscine">Piscine</option>
    <option value="jardin">Jardin</option>
</select>
```

### Tailles différentes

```blade
<!-- Petit -->
<select class="form-select form-select-sm" name="filtre">
    <option>Option</option>
</select>

<!-- Normal (par défaut) -->
<select class="form-select" name="type">
    <option>Option</option>
</select>

<!-- Grand -->
<select class="form-select form-select-lg" name="priorite">
    <option>Option</option>
</select>
```

## Configuration avancée

### Initialisation manuelle avec options personnalisées

Si vous avez besoin d'options spécifiques pour un select :

```javascript
$('#mon-select-special').select2({
    theme: 'bootstrap-5',
    minimumInputLength: 2, // Recherche après 2 caractères
    maximumSelectionLength: 5, // Maximum 5 sélections
    placeholder: 'Recherchez un élément...',
    allowClear: true,
    ajax: {
        url: '/api/search',
        dataType: 'json',
        delay: 250,
        processResults: function (data) {
            return {
                results: data.items
            };
        }
    }
});
```

### Événements Select2

```javascript
// Quand la sélection change
$('#mon-select').on('select2:select', function (e) {
    var data = e.params.data;
    console.log('Sélectionné:', data.id, data.text);
});

// Quand on ouvre le dropdown
$('#mon-select').on('select2:open', function (e) {
    console.log('Dropdown ouvert');
});

// Quand on ferme le dropdown
$('#mon-select').on('select2:close', function (e) {
    console.log('Dropdown fermé');
});
```

### Recharger dynamiquement les options

```javascript
// Méthode 1 : Réinitialiser complètement
$('#mon-select').select2('destroy');
$('#mon-select').html('<option value="1">Nouvelle option 1</option>...');
$('#mon-select').select2({
    theme: 'bootstrap-5',
    width: '100%'
});

// Méthode 2 : Vider la sélection
$('#mon-select').val(null).trigger('change');
```

## Où est-ce intégré ?

### Frontend
- ✅ Tous les formulaires de recherche de propriétés
- ✅ Page d'accueil (recherche location/vente)
- ✅ Filtres de la liste des annonces
- ✅ Espaces clients (filtres)
- ✅ Formulaires de demande

### Backend
- ✅ Tous les formulaires d'administration
- ✅ Gestion des annonces
- ✅ Gestion des utilisateurs
- ✅ Tous les champs select existants

## Personnalisation du style

Les couleurs utilisent les variables CSS du projet :
- `--primary-color: #43542A` (vert principal)
- `--accent-color: #E84E1B` (orange accent)
- `--border-color: #e2e8f0` (gris bordure)

Pour modifier le style, éditez :
- Frontend : `resources/views/frontend/layouts/partials/styles.blade.php`
- Backend : Créez un fichier CSS personnalisé si nécessaire

## Dépannage

### Select2 ne s'initialise pas

1. Vérifiez que jQuery est chargé avant Select2
2. Vérifiez la console pour des erreurs JavaScript
3. Assurez-vous que le select n'a pas la classe `no-select2`

### Le style ne s'applique pas

1. Videz le cache : `Ctrl + F5`
2. Vérifiez que le thème Bootstrap-5 est bien chargé
3. Inspectez l'élément pour voir quelles classes sont appliquées

### Problème avec du contenu dynamique

Utilisez l'événement `DOMNodeInserted` ou réinitialisez manuellement :

```javascript
// Après insertion de nouveau contenu
$('.nouveau-select').select2({
    theme: 'bootstrap-5',
    width: '100%'
});
```

## Ressources

- Documentation officielle : [https://select2.org/](https://select2.org/)
- Thème Bootstrap 5 : [https://github.com/apalfrey/select2-bootstrap-5-theme](https://github.com/apalfrey/select2-bootstrap-5-theme)
- Exemples : [https://select2.org/examples](https://select2.org/examples)

## Version

- **Select2** : 4.1.0-rc.0
- **Select2 Bootstrap 5 Theme** : 1.3.0
- **jQuery** : 3.7.1

---

**Note** : Cette intégration a été réalisée le 6 février 2026 pour améliorer l'expérience utilisateur sur tous les champs select de l'application.
