# Comparator - Outil de Comparaison Multi-Produits

Une application web moderne construite avec Symfony 7.3 et Tailwind CSS 3.4 pour comparer différents types de produits (voitures, téléphones, électroménager).

![Symfony](https://img.shields.io/badge/Symfony-7.3-000000?style=flat&logo=symfony)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.4-38B2AC?style=flat&logo=tailwind-css)

## 📋 Table des matières

- [Aperçu](#-aperçu)
- [Fonctionnalités](#-fonctionnalités)
- [Stack technique](#-stack-technique)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Configuration](#️-configuration)
- [Utilisation](#-utilisation)
- [Commandes utiles](#-commandes-utiles)
- [Structure du projet](#-structure-du-projet)
- [Résolution de problèmes](#-résolution-de-problèmes)
- [Contribution](#-contribution)

## 🎯 Aperçu

Comparator est un outil puissant permettant aux utilisateurs de comparer différents types de produits côte à côte pour prendre des décisions éclairées. L'application utilise les dernières technologies web pour offrir une expérience utilisateur moderne et fluide.

### Produits supportés

- **Voitures** ✅ (disponible)
  - Comparaison détaillée de caractéristiques
  - Filtres avancés (marque, prix, énergie, état)
  - Vue côte à côte jusqu'à 3 voitures
- **Téléphones** 🔜 (à venir)
- **Électroménager** 🔜 (à venir)

## ✨ Fonctionnalités

### Interface utilisateur

- 🎨 **Design moderne** - Interface épurée avec Tailwind CSS
- 🌓 **Mode sombre** - Basculement clair/sombre avec persistance
- 📱 **Responsive** - Mobile-first, optimisé pour tous les écrans
- ⚡ **Animations fluides** - Transitions CSS modernes
- 🔍 **Recherche en temps réel** - Filtrage AJAX sans rechargement

### Fonctionnalités techniques

- 🔐 **Authentification** - Système de connexion sécurisé
- 🔄 **Comparaison multi-critères** - Jusqu'à 3 produits côte à côte
- 📊 **Filtres avancés** - Prix, marque, énergie, état
- 💾 **Gestion des données** - CRUD complet avec Doctrine ORM
- 🚀 **Performance** - AssetMapper pour une gestion optimale des assets

## 🛠 Stack technique

### Backend

- **Symfony 7.3** - Framework PHP moderne
- **PHP 8.2+** - Langage de programmation
- **Doctrine ORM 3** - Gestion de base de données
- **Twig 3** - Moteur de templates

### Frontend

- **Tailwind CSS 3.4** - Framework CSS utility-first
- **JavaScript Vanilla** - Sans dépendances jQuery
- **Fetch API** - Requêtes AJAX modernes
- **Font Awesome** - Icônes

### Base de données

- **MySQL 8.0+** / **MariaDB** - Base de données relationnelle

### Outils de développement

- **Docker** (optionnel) - Conteneurisation
- **PHPUnit** - Tests unitaires
- **Rector** - Modernisation du code PHP

## 📦 Prérequis

- **PHP** >= 8.2.0
- **Composer** 2.x
- **Node.js** >= 18.x
- **MySQL** 8.0+ ou **MariaDB**
- **Docker Desktop** (optionnel, recommandé)
- **Git**

### Vérification des prérequis

```bash
php -v          # Doit afficher >= 8.2
composer -V     # Doit afficher 2.x
node -v         # Doit afficher >= 18.x
docker -v       # Si vous utilisez Docker
```

## 🚀 Installation

### 1. Cloner le projet

```bash
git clone https://github.com/Faez-B/comparator.git
cd comparator
```

### 2. Installer les dépendances

```bash
# Dépendances PHP
composer install

# Dépendances Node.js (pour Tailwind CSS)
npm install
```

### 3. Configuration de la base de données

#### Option A : Avec Docker (recommandé) 🐳

**Avantages :**

- MySQL et phpMyAdmin préinstallés
- Isolation complète
- Pas de configuration système requise

```bash
# Démarrer les services Docker
docker compose up -d

# Vérifier que les conteneurs fonctionnent
docker compose ps

# Créer la base de données
docker compose exec app php bin/console doctrine:database:create

# Exécuter les migrations
docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction

# (Optionnel) Charger les données de test
docker compose exec app php bin/console doctrine:fixtures:load --no-interaction
```

**Accès aux services :**

- Application : <http://localhost:8000>
- phpMyAdmin : <http://localhost:8080>
  - Utilisateur : `root`
  - Mot de passe : `symfony`

**Configuration `.env.local` pour Docker :**

```env
DATABASE_URL="mysql://symfony:symfony@database:3306/symfony?serverVersion=8.4&charset=utf8mb4"
```

#### Option B : Sans Docker (MySQL local) 💻

**Prérequis :**

- MySQL 8.0+ installé et en cours d'exécution

```bash
# Se connecter à MySQL
mysql -u root -p
```

```sql
CREATE DATABASE comparator CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'comparator'@'localhost' IDENTIFIED BY 'comparator';
GRANT ALL PRIVILEGES ON comparator.* TO 'comparator'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

**Configuration `.env.local` pour MySQL local :**

```env
DATABASE_URL="mysql://comparator:comparator@127.0.0.1:3306/comparator?serverVersion=8.0&charset=utf8mb4"
```

```bash
# Créer la base et exécuter les migrations
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction

# (Optionnel) Charger les données de test
php bin/console doctrine:fixtures:load --no-interaction
```

### 4. Compiler les assets CSS

```bash
# Mode développement avec watch (recompile automatiquement)
npm run dev

# OU mode production (compilation unique)
npm run build
```

**Important :** En mode développement, laissez `npm run dev` tourner dans un terminal séparé. Il recompilera automatiquement le CSS à chaque modification.

### 5. Lancer l'application

```bash
docker compose up -d
```

```bash
symfony server:start
```

### 6. Vérification de l'installation

Accédez à <http://localhost:8000> et vérifiez que :

- ✅ La page s'affiche correctement avec les styles CSS
- ✅ Le mode sombre fonctionne
- ✅ Vous pouvez vous inscrire et vous connecter
- ✅ La liste des voitures s'affiche (/voiture)
- ✅ Le comparateur fonctionne (/comparison/voiture)

## ⚙️ Configuration

### Variables d'environnement

Créez un fichier `.env.local` à la racine du projet :

```env
# Configuration de la base de données
# Pour Docker :
DATABASE_URL="mysql://symfony:symfony@database:3306/symfony?serverVersion=8.4&charset=utf8mb4"

# Pour MySQL local :
# DATABASE_URL="mysql://comparator:comparator@127.0.0.1:3306/comparator?serverVersion=8.0&charset=utf8mb4"

# Configuration de l'application
APP_ENV=dev
APP_SECRET=votre_secret_ici

# Configuration du mailer (optionnel)
MAILER_DSN=null://null
```

### Ports personnalisés (si nécessaire)

Si les ports 3306 ou 8080 sont déjà utilisés, modifiez `compose.yaml` :

```yaml
database:
  ports:
    - "3307:3306"  # Changez le port externe

phpmyadmin:
  ports:
    - "8081:80"    # Changez le port externe
```

Et mettez à jour `.env.local` en conséquence :

```env
DATABASE_URL="mysql://symfony:symfony@127.0.0.1:3307/symfony?serverVersion=8.4&charset=utf8mb4"
```

## 📖 Utilisation

### Fonctionnalités principales

#### 1. Inscription et connexion

```
Accueil → S'inscrire
Remplir le formulaire → Confirmer l'email (si configuré)
Se connecter avec les identifiants
```

#### 2. Liste des voitures

```
Menu → Voitures
Utiliser les filtres :
  - Énergie (essence, diesel, électrique, hybride)
  - Marque
  - Prix (min/max)
  - État (neuf/occasion)
  - Tri (alphabétique, prix)
```

#### 3. Comparateur de voitures

```
Menu → Comparer les voitures
Sélectionner jusqu'à 3 voitures
Cliquer sur "Comparer les voitures"
Voir la comparaison détaillée côte à côte
```

#### 4. Mode sombre

```
Cliquer sur l'icône 🌙/☀️ dans la navbar
Le choix est sauvegardé automatiquement
```

### Routes principales

| Route | Description |
|-------|-------------|
| `/` | Page d'accueil |
| `/register` | Inscription |
| `/login` | Connexion |
| `/voiture` | Liste des voitures |
| `/voiture/new` | Ajouter une voiture |
| `/voiture/{id}` | Détails d'une voiture |
| `/voiture/{id}/edit` | Modifier une voiture |
| `/comparison/voiture` | Comparateur de voitures |

## 🛠 Commandes utiles

### Docker

```bash
# Démarrer les services
docker compose up -d

# Arrêter les services
docker compose down

# Voir les logs
docker compose logs -f

# Accéder au conteneur app
docker compose exec app bash

# Reconstruire les images
docker compose build --no-cache

# Supprimer tout (⚠️ supprime les données !)
docker compose down -v
```

### Base de données

```bash
# Avec Docker
docker compose exec app php bin/console doctrine:database:create
docker compose exec app php bin/console doctrine:migrations:migrate
docker compose exec app php bin/console doctrine:fixtures:load

# Sans Docker
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
php bin/console doctrine:schema:update --force
```

### Cache Symfony

```bash
# Avec Docker
docker compose exec app php bin/console cache:clear

# Sans Docker
php bin/console cache:clear
php bin/console cache:clear --env=prod
php bin/console cache:warmup
```

### Assets (CSS)

```bash
# Watch mode (développement)
npm run dev

# Build production
npm run build
```

### Tests

```bash
# Lancer tous les tests
php bin/phpunit

# Lancer un test spécifique
php bin/phpunit tests/Controller/VoitureControllerTest.php

# Coverage
php bin/phpunit --coverage-html coverage/
```

## 📁 Structure du projet

```
comparator/
├── assets/
│   └── styles/
│       └── app.css              # Styles Tailwind
├── config/
│   ├── packages/                # Configuration des bundles
│   └── routes/                  # Définition des routes
├── migrations/                  # Migrations de base de données
├── public/
│   ├── css/
│   │   └── output.css          # CSS compilé
│   ├── js/
│   │   └── main.js             # JavaScript vanilla
│   └── index.php               # Point d'entrée
├── src/
│   ├── Controller/
│   │   ├── ComparisonController.php    # Comparateur
│   │   ├── VoitureController.php       # CRUD voitures
│   │   └── ...
│   ├── Entity/
│   │   ├── User.php                    # Entité utilisateur
│   │   ├── Voiture.php                 # Entité voiture
│   │   └── ...
│   ├── Form/                    # Formulaires Symfony
│   └── Repository/              # Repositories Doctrine
├── templates/
│   ├── base.html.twig          # Layout principal
│   ├── navbar.html.twig        # Navigation
│   ├── comparison/
│   │   └── voiture.html.twig   # Interface comparateur
│   ├── voiture/
│   │   ├── index.html.twig     # Liste des voitures
│   │   └── ...
│   └── ...
├── tests/                       # Tests PHPUnit
├── compose.yaml                 # Configuration Docker
├── composer.json                # Dépendances PHP
├── package.json                 # Dépendances Node.js
├── tailwind.config.js           # Configuration Tailwind
└── .env                         # Variables d'environnement
```

## 🐛 Résolution de problèmes

### Le CSS ne se charge pas

**Symptôme :** La page s'affiche sans styles

**Solution :**

```bash
# 1. Recompiler le CSS
npm run build

# 2. Vider le cache Symfony
php bin/console cache:clear

# 3. Vérifier que le fichier existe
ls -la public/css/output.css
```

### Erreur "Connection refused" à la base de données

**Avec Docker :**

```bash
# Vérifier que les conteneurs fonctionnent
docker compose ps

# Si non, les démarrer
docker compose up -d

# Vérifier les logs
docker compose logs database
```

### Erreur "Table doesn't exist"

```bash
# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Si ça ne fonctionne pas, forcer la mise à jour
php bin/console doctrine:schema:update --force
```

### Port déjà utilisé (3306 ou 8080)

Modifiez les ports dans `compose.yaml` (voir section [Configuration](#️-configuration))

### Erreurs Symfony

```bash
# Vérifier la configuration
php bin/console debug:config
php bin/console debug:router

# Recréer le cache
php bin/console cache:clear --no-warmup
php bin/console cache:warmup
```

### Assets non trouvés

```bash
php bin/console asset-map:compile
php bin/console assets:install
```

## 🎨 Personnalisation

### Couleurs et thème

Les couleurs sont définies dans `tailwind.config.js` :

```javascript
colors: {
  primary: {
    50: '#f0f9ff',
    // ...
    900: '#0c4a6e',
  },
  accent: {
    // ...
  }
}
```

### Ajouter une nouvelle catégorie de produits

1. **Créer l'entité**

```bash
php bin/console make:entity Telephone
```

2. **Créer le controller**

```bash
php bin/console make:controller TelephoneController
```

3. **Créer les templates**

```twig
{# templates/telephone/index.html.twig #}
{% extends 'base.html.twig' %}
```

4. **Ajouter le comparateur**

```php
// src/Controller/ComparisonController.php
#[Route('/comparison/telephone', name: 'app_comparison_telephone')]
public function compareTelephones(Request $request): Response
{
    // Logique de comparaison
}
```

## 🚀 Déploiement en production

### 1. Préparer l'application

```bash
# Mettre APP_ENV en production dans .env.local
APP_ENV=prod
APP_DEBUG=0

# Optimiser Composer
composer install --no-dev --optimize-autoloader

# Compiler les assets
npm run build

# Vider et réchauffer le cache
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# Exécuter les migrations
php bin/console doctrine:migrations:migrate --no-interaction
```

### 2. Configurer le serveur web

**Apache (.htaccess)**

```apache
<VirtualHost *:80>
    ServerName comparator.example.com
    DocumentRoot /var/www/comparator/public

    <Directory /var/www/comparator/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Nginx**

```nginx
server {
    listen 80;
    server_name comparator.example.com;
    root /var/www/comparator/public;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }
}
```

### 3. Checklist de sécurité

- [ ] `APP_ENV=prod` dans `.env.local`
- [ ] `APP_DEBUG=0` dans `.env.local`
- [ ] Mot de passe base de données fort
- [ ] `APP_SECRET` unique et sécurisé
- [ ] HTTPS configuré (Let's Encrypt)
- [ ] Permissions fichiers correctes (755/644)
- [ ] Supprimer `.git` si nécessaire
- [ ] Configurer les sauvegardes de base de données

## 🤝 Contribution

Les contributions sont les bienvenues ! Voici comment contribuer :

1. **Fork** le projet
2. **Créez** une branche pour votre fonctionnalité (`git checkout -b feature/AmazingFeature`)
3. **Committez** vos changements (`git commit -m 'Add some AmazingFeature'`)
4. **Push** vers la branche (`git push origin feature/AmazingFeature`)
5. **Ouvrez** une Pull Request

### Guidelines

- Suivre les standards de code PHP (PSR-12)
- Ajouter des tests pour les nouvelles fonctionnalités
- Documenter les changements importants
- Utiliser des commits clairs et descriptifs

## 📝 Changelog

### Version actuelle (v2.0.0)

#### ✨ Nouveautés

- Upgrade vers Symfony 7.3 et PHP 8.2+
- Design moderne avec Tailwind CSS 3.4
- Mode sombre avec persistance
- Comparateur de voitures (jusqu'à 3 véhicules)
- JavaScript vanilla (suppression de jQuery)
- AssetMapper pour la gestion des assets

#### 🔄 Améliorations

- Interface utilisateur repensée
- Filtres en temps réel
- Responsive design mobile-first
- Animations fluides
- Performance optimisée

#### 🐛 Corrections

- Suppression de `getUsername()` déprécié dans User
- Refactoring de VoitureController sans `$_POST`
- Migration complète vers les attributs PHP 8

#### 📚 Documentation

- README complet et détaillé
- Guide d'installation pas à pas
- Guide de résolution de problèmes
- Documentation de l'architecture

## 📄 Licence

Ce projet est sous licence propriétaire. Tous droits réservés.

## 📞 Support

Pour toute question ou problème :

- 📧 Ouvrir une [issue](https://github.com/Faez-B/comparator/issues)
- 💬 Consulter la [documentation Symfony](https://symfony.com/doc/7.3/)
