# EasyColoc 🏠

> Application web de gestion de colocation — Laravel MVC + MySQL + Tailwind CSS

---

## 📋 Table des matières

- [Présentation](#présentation)
- [Fonctionnalités](#fonctionnalités)
- [Stack technique](#stack-technique)
- [Structure du projet](#structure-du-projet)
- [Installation](#installation)
- [Commandes Artisan utilisées](#commandes-artisan-utilisées)
- [Comptes de test](#comptes-de-test)
- [Rôles & Permissions](#rôles--permissions)
- [Logique métier](#logique-métier)

---

## Présentation

EasyColoc est une application web qui permet de gérer une colocation de façon simplifiée :
- Suivre les dépenses communes
- Calculer automatiquement les soldes de chaque membre
- Savoir **qui doit quoi à qui** sans calcul manuel
- Gérer les invitations, les départs et la réputation des membres

---

## Fonctionnalités

### Authentification
- [x] Inscription et connexion manuelles 
- [x] Premier utilisateur inscrit promu **Admin global** automatiquement
- [x] Gestion du profil (nom, email, mot de passe)
- [x] Blocage automatique des utilisateurs bannis

### Colocations
- [x] Création d'une colocation (créateur = Owner automatique)
- [x] Une seule colocation active par utilisateur
- [x] Invitation de membres par email + token (lien valable 7 jours)
- [x] Acceptation ou refus d'une invitation
- [x] Départ volontaire d'un membre (`left_at`)
- [x] Retrait d'un membre par l'Owner
- [x] Annulation de la colocation (status = `cancelled`)

### Dépenses
- [x] Ajout d'une dépense (titre, montant, date, payeur, catégorie)
- [x] Suppression d'une dépense (par le payeur ou l'Owner)
- [x] Filtre des dépenses par mois
- [x] Catégories personnalisables (gérées par l'Owner)

### Balances & Remboursements
- [x] Calcul automatique du solde de chaque membre
- [x] Vue synthétique **"qui doit quoi à qui"** (algorithme de réduction des dettes)
- [x] Enregistrement d'un paiement ("Marquer payé")

### Réputation
- [x] `+1` si départ/annulation sans dette
- [x] `-1` si départ/annulation avec dette
- [x] Si l'Owner retire un membre avec dette : `-1` pour l'Owner

### Administration globale
- [x] Dashboard avec statistiques (utilisateurs, colocations, dépenses, montant total)
- [x] Bannissement / débannissement des utilisateurs

---

## Stack technique

| Élément | Choix |
|---|---|
| Framework | Laravel 11 (MVC monolithique) |
| Base de données | MySQL via migrations Eloquent |
| ORM | Eloquent (`hasMany`, `belongsTo`, `belongsToMany`) |
| Authentification | Manuelle via session (pas de Breeze) |
| Validation | FormRequest (`app/Http/Requests/`) |
| CSS | Tailwind CSS (CDN, sans build step) |
| Template | Blade |

---

## Structure du projet

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php          # Inscription, connexion, profil
│   │   ├── DashboardController.php     # Dashboard principal
│   │   ├── ColocationController.php    # CRUD colocation + membres
│   │   ├── ExpenseController.php       # Ajout/suppression dépenses
│   │   ├── PaymentController.php       # Soldes & paiements
│   │   ├── CategoryController.php      # Gestion catégories (Owner)
│   │   ├── InvitationController.php    # Invitations par token
│   │   └── AdminController.php         # Dashboard admin global
│   ├── Middleware/
│   │   ├── AuthMiddleware.php          # Vérification session + bannissement
│   │   └── AdminMiddleware.php         # Restriction admin global
│   └── Requests/
│       ├── RegisterRequest.php
│       ├── LoginRequest.php
│       ├── UpdateProfileRequest.php
│       ├── StoreColocationRequest.php
│       ├── UpdateColocationRequest.php
│       ├── StoreExpenseRequest.php
│       ├── StorePaymentRequest.php
│       ├── StoreCategoryRequest.php
│       └── StoreInvitationRequest.php
├── Models/
│   ├── User.php
│   ├── Colocation.php
│   ├── Membership.php
│   ├── Category.php
│   ├── Expense.php
│   ├── Payment.php
│   └── Invitation.php
└── Services/
    ├── BalanceService.php              # Calcul des soldes & settlements
    └── ReputationService.php           # Gestion de la réputation

database/
├── migrations/
│   ├── ..._create_users_table.php
│   ├── ..._create_colocations_table.php
│   ├── ..._create_memberships_table.php
│   ├── ..._create_categories_table.php
│   ├── ..._create_expenses_table.php
│   ├── ..._create_payments_table.php
│   └── ..._create_invitations_table.php
└── seeders/
    └── DatabaseSeeder.php

resources/views/
├── layouts/
│   ├── app.blade.php                   # Layout principal avec navbar
│   └── auth.blade.php                  # Layout login/register
├── auth/
│   ├── login.blade.php
│   ├── register.blade.php
│   └── profile.blade.php
├── colocations/
│   ├── show.blade.php                  # Page principale de la coloc
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── no-colocation.blade.php
├── payments/
│   └── index.blade.php                 # Soldes & remboursements
├── categories/
│   └── index.blade.php
├── invitations/
│   ├── show.blade.php
│   └── invalid.blade.php
├── admin/
│   └── dashboard.blade.php
└── dashboard.blade.php

routes/
└── web.php

bootstrap/
└── app.php                             # Enregistrement des middlewares
```

---

## Installation

### Prérequis
- PHP 8.2+
- Composer
- MySQL 8+

### Étapes

```bash
# 1. Installer les dépendances PHP
composer install

# 2. Copier le fichier d'environnement
cp .env.example .env

# 3. Générer la clé d'application
php artisan key:generate

# 4. Configurer la base de données dans .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=easycoloc
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe

# 5. Lancer les migrations
php artisan migrate

# 6. (Optionnel) Insérer les données de test
php artisan db:seed

# 7. Lancer le serveur de développement
php artisan serve
```

---

## Commandes Artisan utilisées

```bash
# Migrations
php artisan make:migration create_users_table
php artisan make:migration create_colocations_table
php artisan make:migration create_memberships_table
php artisan make:migration create_categories_table
php artisan make:migration create_expenses_table
php artisan make:migration create_payments_table
php artisan make:migration create_invitations_table

# Models
php artisan make:model User
php artisan make:model Colocation
php artisan make:model Membership
php artisan make:model Category
php artisan make:model Expense
php artisan make:model Payment
php artisan make:model Invitation

# Controllers
php artisan make:controller AuthController
php artisan make:controller DashboardController
php artisan make:controller ColocationController
php artisan make:controller ExpenseController
php artisan make:controller PaymentController
php artisan make:controller CategoryController
php artisan make:controller InvitationController
php artisan make:controller AdminController

# FormRequests
php artisan make:request RegisterRequest
php artisan make:request LoginRequest
php artisan make:request UpdateProfileRequest
php artisan make:request StoreColocationRequest
php artisan make:request UpdateColocationRequest
php artisan make:request StoreExpenseRequest
php artisan make:request StorePaymentRequest
php artisan make:request StoreCategoryRequest
php artisan make:request StoreInvitationRequest

# Middlewares
php artisan make:middleware AuthMiddleware
php artisan make:middleware AdminMiddleware

# Seeder
php artisan make:seeder DatabaseSeeder

# Exécution
php artisan migrate
php artisan migrate:fresh --seed   # Reset complet + seed
php artisan db:seed
php artisan serve
```

---

## Comptes de test

> Disponibles après `php artisan db:seed`

| Nom | Email | Mot de passe | Rôle |
|---|---|---|---|
| Admin EasyColoc | admin@easycoloc.fr | password | Admin global |
| Alice Martin | alice@example.com | password | Owner (Appart Belleville) |
| Bob Dupont | bob@example.com | password | Member |
| Claire Petit | claire@example.com | password | Member |

---

## Rôles & Permissions

| Action | Member | Owner | Admin global |
|---|:---:|:---:|:---:|
| S'inscrire / Se connecter | ✅ | ✅ | ✅ |
| Voir sa colocation | ✅ | ✅ | ✅ |
| Ajouter une dépense | ✅ | ✅ | ✅ |
| Voir les soldes | ✅ | ✅ | ✅ |
| Marquer un paiement | ✅ | ✅ | ✅ |
| Quitter la colocation | ✅ | ❌ | — |
| Inviter un membre | ❌ | ✅ | — |
| Retirer un membre | ❌ | ✅ | — |
| Gérer les catégories | ❌ | ✅ | — |
| Annuler la colocation | ❌ | ✅ | — |
| Voir les stats globales | ❌ | ❌ | ✅ |
| Bannir / débannir | ❌ | ❌ | ✅ |

---

## Logique métier

### Calcul des soldes (`BalanceService`)

```
Solde d'un membre =
    total des dépenses payées par ce membre
  - sa part individuelle (total colocation ÷ nombre de membres actifs)
  + paiements reçus
  - paiements effectués
```

Les **settlements** (remboursements simplifiés) utilisent un algorithme greedy :
les débiteurs remboursent les créanciers en minimisant le nombre de transactions.

### Réputation (`ReputationService`)

| Situation | Membre | Owner |
|---|---|---|
| Départ / annulation sans dette | +1 | +1 |
| Départ / annulation avec dette | -1 | -1 |
| Owner retire un membre avec dette | -1 | **-1 aussi** |

### Invitation

1. L'Owner envoie une invitation (token unique, expire dans 7 jours)
2. L'invité reçoit un lien → il doit être connecté avec **le même email**
3. S'il a déjà une colocation active → blocage
4. Sinon → ajout comme `member` avec `joined_at = now()`