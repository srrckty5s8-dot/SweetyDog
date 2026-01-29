# Guide de migration MVC - Option 3 (Routes nommées)

## Résumé des changements

Le projet a été migré vers un système de routage avancé avec routes nommées, similaire à Laravel ou Symfony.

### Fichiers créés/modifiés

✅ **app/Core/Router.php** - Complètement remplacé
- Ancien : Routeur simple utilisant `?c=controller&a=action`
- Nouveau : Routeur avancé avec routes nommées et pattern matching

✅ **app/helpers.php** - Créé
- Fonctions utilitaires : `route()`, `url()`, `redirect()`, etc.

✅ **app/routes.php** - Créé
- Configuration centralisée de toutes les routes
- 13 routes couvrant auth, clients, animaux, rendez-vous, paramètres

✅ **public/index.php** - Mis à jour
- Charge les helpers
- Initialise le routeur
- Expose `$GLOBALS['router']` aux vues

## Utilisation des routes nommées

### Avant (ancien système)
```php
<a href="index.php?c=client&a=edit&id=<?= $client['id'] ?>">Modifier</a>
<a href="index.php?c=client&a=delete&id=<?= $client['id'] ?>">Supprimer</a>
```

### Après (nouveau système)
```php
<a href="<?= route('clients.edit', ['id' => $client['id']]) ?>">Modifier</a>
<a href="<?= route('clients.delete', ['id' => $client['id']]) ?>">Supprimer</a>
```

## Fonctions helper disponibles

```php
// Générer une URL depuis une route nommée
route('clients.index')                           // /clients
route('clients.edit', ['id' => 5])              // /clients/5/edit
route('animals.tracking', ['id' => 10])         // /animals/10/tracking

// URL absolue
url('/clients')                                   // /sweetydog/clients

// Redirection
redirect('clients.index')
redirect('clients.edit', ['id' => 5])

// Utilitaires
currentUrl()                                      // Récupère l'URL actuelle
isCurrentRoute('clients.index')                  // Vérifie si c'est la route actuelle
param('id')                                       // Récupère un paramètre GET/POST
e($value)                                         // Échappe pour éviter XSS

// Flash messages
flashMessage('success', 'Client créé!')
getFlashMessage('success')
```

## Routes disponibles

### Authentification
- `auth.login` → GET `/auth/login`
- `auth.logout` → GET `/auth/logout`

### Clients
- `clients.index` → GET `/clients`
- `clients.create` → GET `/clients/create`
- `clients.store` → POST `/clients`
- `clients.edit` → GET `/clients/{id}/edit`
- `clients.update` → PUT/POST `/clients/{id}`
- `clients.delete` → DELETE/POST `/clients/{id}/delete`

### Animaux
- `animals.edit` → GET `/animals/{id}/edit`
- `animals.update` → PUT/POST `/animals/{id}`
- `animals.tracking` → GET `/animals/{id}/tracking`

### Rendez-vous
- `appointments.index` → GET `/appointments`
- `appointments.create` → GET `/appointments/create`
- `appointments.delete` → DELETE/POST `/appointments/{id}/delete`

### Paramètres
- `settings.index` → GET `/settings`

## Migration des vues

Pour migrer une vue du système ancien au nouveau :

1. **Remplacer les liens simple**
```php
// Avant
<a href="index.php?c=client&a=list">Clients</a>

// Après
<a href="<?= route('clients.index') ?>">Clients</a>
```

2. **Remplacer les formulaires**
```php
// Avant
<form method="POST" action="index.php?c=client&a=add">

// Après
<form method="POST" action="<?= route('clients.store') ?>">
```

3. **Remplacer les actions avec ID**
```php
// Avant
<a href="index.php?c=client&a=edit&id=<?= $id ?>">Éditer</a>

// Après
<a href="<?= route('clients.edit', ['id' => $id]) ?>">Éditer</a>
```

## Vérifications de base

1. ✅ Routeur charge les routes depuis routes.php
2. ✅ Helpers.php contient toutes les fonctions utilitaires
3. ✅ public/index.php initialise le routeur correctement
4. ✅ Les routes correspondent aux contrôleurs existants

## Prochaines étapes

1. Mettre à jour toutes les vues pour utiliser `route()` au lieu de `?c=&a=`
2. Modifier les formulaires pour utiliser les nouvelles routes
3. Tester chaque route dans le navigateur
4. Documenter les nouvelles pratiques pour l'équipe

## Notes d'implémentation

- Le pattern `/clients/{id}/edit` est converti en regex `^/clients/([^/]+)/edit$`
- Les paramètres sont extraits automatiquement et passés aux actions du contrôleur
- Les routes sont chargées depuis `app/routes.php` au démarrage
- Les helpers sont globaux et disponibles dans toutes les vues

---

**Status** : 🟡 Implémentation en cours
- ✅ Routeur remplacé
- ✅ Helpers créés
- ✅ public/index.php mis à jour
- ⏳ Prochaine étape : Mettre à jour les vues
