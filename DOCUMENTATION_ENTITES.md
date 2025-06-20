# Documentation des Entités - Gestion Événements

## Vue d'ensemble

Ce document décrit toutes les entités du projet et leurs propriétés disponibles pour éviter les erreurs de propriétés manquantes dans les templates Twig.

## Entités disponibles

### 1. User (Utilisateur)

**Propriétés disponibles :**
- `id` (int) - Identifiant unique
- `email` (string) - Adresse email (unique)
- `roles` (array) - Rôles de l'utilisateur
- `password` (string) - Mot de passe hashé
- `registrations` (Collection) - Inscriptions aux événements

**Utilisation dans les templates :**
```twig
{{ user.email }}           ✅ Correct
{{ user.roles }}          ✅ Correct
{{ user.registrations }}  ✅ Correct
{{ user.firstName }}      ❌ ERREUR - Propriété inexistante
{{ user.lastName }}       ❌ ERREUR - Propriété inexistante
```

### 2. Event (Événement)

**Propriétés disponibles :**
- `id` (int) - Identifiant unique
- `title` (string) - Titre de l'événement
- `description` (text) - Description de l'événement
- `eventDate` (DateTime) - Date et heure de l'événement
- `location` (string) - Lieu de l'événement
- `placeAvailable` (int) - Nombre de places disponibles
- `isActive` (bool) - Statut actif/inactif
- `category` (Category) - Catégorie de l'événement
- `registrations` (Collection) - Inscriptions à cet événement

**Utilisation dans les templates :**
```twig
{{ event.title }}           ✅ Correct
{{ event.description }}     ✅ Correct
{{ event.eventDate }}       ✅ Correct
{{ event.location }}        ✅ Correct
{{ event.placeAvailable }}  ✅ Correct
{{ event.isActive }}        ✅ Correct
{{ event.category.name }}   ✅ Correct (relation)
{{ event.registrations }}   ✅ Correct
```

### 3. Category (Catégorie)

**Propriétés disponibles :**
- `id` (int) - Identifiant unique
- `name` (string) - Nom de la catégorie
- `events` (Collection) - Événements de cette catégorie

**Utilisation dans les templates :**
```twig
{{ category.name }}    ✅ Correct
{{ category.events }}  ✅ Correct
```

### 4. Registration (Inscription)

**Propriétés disponibles :**
- `id` (int) - Identifiant unique
- `registeredAt` (DateTimeImmutable) - Date d'inscription
- `isConfirmed` (bool) - Statut de confirmation
- `user` (User) - Utilisateur inscrit
- `event` (Event) - Événement concerné

**Utilisation dans les templates :**
```twig
{{ registration.registeredAt }}  ✅ Correct
{{ registration.isConfirmed }}   ✅ Correct
{{ registration.user.email }}    ✅ Correct (relation)
{{ registration.event.title }}   ✅ Correct (relation)
```

## Relations entre entités

### User ↔ Registration
- Un utilisateur peut avoir plusieurs inscriptions
- Une inscription appartient à un seul utilisateur

### Event ↔ Registration
- Un événement peut avoir plusieurs inscriptions
- Une inscription appartient à un seul événement

### Event ↔ Category
- Un événement appartient à une catégorie
- Une catégorie peut avoir plusieurs événements

## Bonnes pratiques

### 1. Utilisation des getters
```twig
{# ✅ Correct - Utilise les getters automatiquement #}
{{ user.email }}
{{ event.title }}

{# ❌ Incorrect - N'utilise pas les propriétés privées #}
{{ user.privateProperty }}
```

### 2. Vérification des relations
```twig
{# ✅ Correct - Vérification avant utilisation #}
{% if event.category %}
    {{ event.category.name }}
{% endif %}

{# ✅ Correct - Utilisation avec vérification #}
{{ registration.user ? registration.user.email : 'Utilisateur inconnu' }}
```

### 3. Filtres et méthodes
```twig
{# ✅ Correct - Utilisation des méthodes de collection #}
{{ user.registrations|length }}
{{ user.registrations|filter(r => r.isConfirmed)|length }}

{# ✅ Correct - Tri et filtrage #}
{{ user.registrations|filter(r => r.event.eventDate > "now")|sort((a, b) => a.event.eventDate <=> b.event.eventDate)|first }}
```

## Script de vérification

Un script `check_entity_properties.php` est disponible pour détecter automatiquement les propriétés manquantes :

```bash
php check_entity_properties.php
```

Ce script analyse tous les templates et vérifie que les propriétés utilisées existent dans les entités correspondantes.

## Erreurs courantes à éviter

### 1. Propriétés inexistantes
```twig
{# ❌ ERREUR - firstName n'existe pas dans User #}
{{ user.firstName }}

{# ✅ CORRECT - Utiliser email #}
{{ user.email }}
```

### 2. Relations non vérifiées
```twig
{# ❌ ERREUR - Peut causer une erreur si category est null #}
{{ event.category.name }}

{# ✅ CORRECT - Vérification avant utilisation #}
{% if event.category %}
    {{ event.category.name }}
{% endif %}
```

### 3. Méthodes inexistantes
```twig
{# ❌ ERREUR - Méthode inexistante #}
{{ user.getFullName() }}

{# ✅ CORRECT - Utiliser les propriétés disponibles #}
{{ user.email }}
```

## Maintenance

Pour maintenir cette documentation à jour :

1. **Ajout d'une nouvelle propriété** : Mettre à jour ce document
2. **Modification d'une entité** : Vérifier tous les templates utilisant cette entité
3. **Nouveau template** : Utiliser ce document comme référence
4. **Tests réguliers** : Exécuter le script de vérification

## Commandes utiles

```bash
# Vérifier les propriétés des entités
php check_entity_properties.php

# Lister toutes les routes
php bin/console debug:router

# Vider le cache
php bin/console cache:clear

# Vérifier la syntaxe des templates
php bin/console lint:twig templates/
``` 