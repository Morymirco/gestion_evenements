# Guide de Présentation - Gestion Événements

## 🎯 Objectif du projet
Application Symfony pour la gestion d'événements avec système d'authentification et rôles (visiteur, utilisateur, administrateur).

## 🚀 Démarrage rapide

### 1. Installation et configuration
```bash
# Installer les dépendances
composer install

# Créer la base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Créer un utilisateur administrateur
php bin/console app:create-admin admin@example.com password123

# Démarrer le serveur
symfony server:start
```

### 2. Accès à l'application
- **URL** : http://127.0.0.1:8000
- **Admin** : admin@example.com / password123

## 📋 Scénarios de démonstration

### Scénario 1 : Page d'accueil
1. **Accueil de l'application**
   - Aller sur http://127.0.0.1:8000
   - ✅ Page d'accueil moderne avec statistiques
   - ✅ Section hero avec appels à l'action
   - ✅ Événements à venir en vedette
   - ✅ Fonctionnalités mises en avant

2. **Statistiques en temps réel**
   - ✅ Nombre total d'événements
   - ✅ Événements actifs
   - ✅ Utilisateurs inscrits
   - ✅ Inscriptions totales

### Scénario 2 : Visiteur non connecté
1. **Accès public aux événements**
   - Aller sur http://127.0.0.1:8000/events/public
   - ✅ Consultation libre des événements
   - ✅ Détails des événements accessibles
   - ✅ Boutons de connexion/inscription visibles

2. **Processus d'inscription**
   - Cliquer sur "S'inscrire" (redirige vers login)
   - Créer un compte utilisateur
   - ✅ Retour automatique vers l'événement

### Scénario 3 : Utilisateur connecté
1. **Inscription aux événements**
   - Se connecter avec un compte utilisateur
   - Aller sur http://127.0.0.1:8000/events/public
   - Cliquer sur "S'inscrire" sur un événement
   - ✅ Inscription effectuée avec message de confirmation

2. **Gestion du profil**
   - Aller sur "Mon profil" dans la navbar
   - ✅ Voir les statistiques d'inscriptions
   - ✅ Modifier les informations personnelles
   - ✅ Voir la liste des inscriptions

3. **Désinscription**
   - Dans "Mes inscriptions", cliquer sur "Se désinscrire"
   - ✅ Désinscription confirmée

### Scénario 4 : Administrateur
1. **Tableau de bord**
   - Se connecter avec admin@example.com
   - Aller sur "Administration" → "Tableau de bord"
   - ✅ Vue d'ensemble des événements, utilisateurs, inscriptions

2. **Gestion des événements**
   - "Administration" → "Gestion événements"
   - ✅ Liste de tous les événements
   - ✅ Créer un nouvel événement
   - ✅ Modifier un événement existant
   - ✅ Supprimer un événement

3. **Gestion des utilisateurs**
   - "Administration" → "Gestion utilisateurs"
   - ✅ Liste de tous les utilisateurs
   - ✅ Voir les rôles de chaque utilisateur

4. **Gestion des inscriptions**
   - "Administration" → "Gestion inscriptions"
   - ✅ Liste de toutes les inscriptions
   - ✅ Confirmer des inscriptions en attente

5. **Gestion des catégories**
   - "Administration" → "Gestion catégories"
   - ✅ Créer/modifier/supprimer des catégories

## 🔧 Fonctionnalités techniques

### Sécurité et rôles
- **Visiteur** : Consultation publique des événements
- **ROLE_USER** : Inscription aux événements, gestion du profil
- **ROLE_ADMIN** : Accès complet à l'administration

### Entités principales
- **User** : email, roles, registrations
- **Event** : title, description, eventDate, location, placeAvailable, isActive, category
- **Category** : name, events
- **Registration** : registeredAt, isConfirmed, user, event

### Routes principales
- `/` → Page d'accueil avec statistiques et événements à venir
- `/events/public` → Liste publique des événements
- `/events/{id}/details` → Détails d'événement
- `/event/{id}/register` → Inscription à un événement
- `/profile` → Profil utilisateur
- `/admin/*` → Interface d'administration

## 🎨 Interface utilisateur

### Design moderne
- **Bootstrap 5** pour le design responsive
- **Font Awesome** pour les icônes
- **Interface intuitive** avec navigation claire
- **Page d'accueil attrayante** avec statistiques et événements en vedette

### Responsive
- ✅ Compatible mobile et desktop
- ✅ Navigation adaptative
- ✅ Formulaires optimisés
- ✅ Animations CSS subtiles

## 📊 Statistiques de l'application

### Code
- **Contrôleurs** : 10 (Home, Event, PublicEvent, Admin, User, etc.)
- **Entités** : 4 (User, Event, Category, Registration)
- **Templates** : 25+ templates Twig
- **Formulaires** : 4 formulaires Symfony

### Fonctionnalités
- ✅ Authentification complète
- ✅ Gestion des rôles
- ✅ CRUD événements
- ✅ Système d'inscriptions
- ✅ Interface d'administration
- ✅ Profil utilisateur
- ✅ Gestion des catégories
- ✅ Page d'accueil moderne avec statistiques

## 🐛 Résolution de problèmes

### Erreurs courantes
1. **"Access Denied"** → Vérifier les rôles utilisateur
2. **"Route not found"** → Vider le cache : `php bin/console cache:clear`
3. **"Property not found"** → Vérifier la documentation des entités

### Commandes utiles
```bash
# Vider le cache
php bin/console cache:clear

# Lister les routes
php bin/console debug:router

# Vérifier la base de données
php bin/console doctrine:schema:validate

# Créer un admin
php bin/console app:create-admin email@example.com password
```

## 🎉 Points forts du projet

1. **Architecture propre** : Séparation claire des responsabilités
2. **Sécurité** : Gestion des rôles et permissions
3. **UX optimisée** : Interface intuitive et responsive
4. **Code maintenable** : Documentation complète et bonnes pratiques
5. **Fonctionnalités complètes** : CRUD, authentification, administration
6. **Page d'accueil moderne** : Statistiques en temps réel et événements en vedette

## 📝 Notes pour la présentation

- **Durée estimée** : 10-15 minutes
- **Points clés** : Sécurité, UX, architecture, page d'accueil moderne
- **Démo live** : 
  1. Page d'accueil avec statistiques
  2. Créer un événement
  3. S'inscrire à un événement
  4. Administrer l'application
- **Questions possibles** : Sécurité, évolutivité, maintenance, design 