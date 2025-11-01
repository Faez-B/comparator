# Guide de mise à niveau - Comparator (Symfony 7.2)

## 🎉 Nouveautés

Cette mise à niveau transforme Comparator en une application moderne avec :

- **Symfony 7.2** - Dernière version LTS de Symfony
- **Design moderne** - Interface utilisateur repensée avec Tailwind CSS 3.4
- **JavaScript vanilla** - Remplacement de jQuery par du JavaScript moderne avec Fetch API
- **Dark Mode** - Support du mode sombre avec persistance
- **Fonctionnalité de comparaison** - Comparez jusqu'à 3 voitures côte à côte
- **AssetMapper** - Gestion moderne des assets sans build JavaScript
- **Améliorations UX** - Navigation améliorée, filtres en temps réel, animations fluides

## 📋 Prérequis

- PHP >= 8.2
- Composer 2.x
- Node.js >= 18.x (pour Tailwind CSS)
- MySQL/MariaDB

## 🚀 Installation et mise à niveau

### 1. Mettre à jour les dépendances PHP

```bash
composer install
```

### 2. Installer les dépendances Node.js

```bash
npm install
```

### 3. Compiler les assets CSS

```bash
# Mode développement avec watch
npm run dev

# OU pour la production
npm run build
```

### 4. Mettre à jour la base de données

```bash
# Créer la base de données si elle n'existe pas
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# (Optionnel) Charger les données de test
php bin/console doctrine:fixtures:load
```

### 5. Lancer le serveur

```bash
symfony server:start
```

Ou avec le serveur PHP natif :

```bash
php -S localhost:8000 -t public/
```

## 📁 Structure des fichiers modifiés

### Backend (PHP/Symfony)

- `composer.json` - Dépendances Symfony 7.2
- `src/Entity/User.php` - Suppression des méthodes dépréciées
- `src/Controller/VoitureController.php` - Refactoring avec Request object
- `src/Controller/ComparisonController.php` - **NOUVEAU** - Comparateur de voitures

### Frontend (Assets)

- `assets/styles/app.css` - **NOUVEAU** - Styles Tailwind modernes
- `public/js/main.js` - JavaScript vanilla moderne (sans jQuery)
- `tailwind.config.js` - Configuration Tailwind avec tokens de design personnalisés
- `package.json` - Scripts de build Tailwind

### Templates (Twig)

- `templates/base.html.twig` - Layout moderne avec dark mode
- `templates/navbar.html.twig` - Navigation moderne avec dark mode toggle
- `templates/_flash_messages.html.twig` - **NOUVEAU** - Messages flash animés
- `templates/default/index.html.twig` - Page d'accueil redessinée
- `templates/voiture/index.html.twig` - Liste de voitures avec filtres modernes
- `templates/voiture/_index_body.html.twig` - Tableau de voitures redessiné
- `templates/comparison/voiture.html.twig` - **NOUVEAU** - Interface de comparaison

### Configuration

- `config/packages/asset_mapper.yaml` - **NOUVEAU** - Configuration AssetMapper

## 🎨 Fonctionnalités principales

### 1. Mode sombre

- Toggle dans la navbar
- Persistance dans localStorage
- Respect des préférences système

### 2. Filtres en temps réel

- Recherche par énergie, marque, prix
- Tri alphabétique et par prix
- Filtre état (neuf/occasion)
- Résultats AJAX sans rechargement

### 3. Comparateur de voitures

- Comparer jusqu'à 3 voitures
- Affichage côte à côte des caractéristiques
- Interface intuitive de sélection

### 4. Design responsive

- Mobile-first
- Optimisé pour tablettes et desktop
- Navigation adaptative

## 🔧 Commandes utiles

### Développement

```bash
# Watch Tailwind CSS en mode dev
npm run dev

# Lancer les tests
php bin/console doctrine:fixtures:load
php bin/phpunit

# Vider le cache
php bin/console cache:clear
```

### Production

```bash
# Build CSS pour production
npm run build

# Optimiser l'autoloader
composer dump-autoload --optimize --classmap-authoritative

# Vider et réchauffer le cache
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

## 🎯 Points d'attention

1. **jQuery supprimé** - Tout le code JavaScript a été réécrit en vanilla JS moderne
2. **PHP 8.2+** - Version minimum requise pour Symfony 7.2
3. **Doctrine ORM 3** - Mise à niveau de Doctrine ORM
4. **AssetMapper** - Pas besoin de Webpack Encore
5. **Tailwind Build** - Nécessite de lancer `npm run dev` ou `npm run build`

## 🐛 Résolution de problèmes

### Le CSS ne se charge pas

```bash
npm run build
php bin/console cache:clear
```

### Erreur de migration

```bash
php bin/console doctrine:schema:update --force
```

### Assets non trouvés

```bash
php bin/console asset-map:compile
```

## 📚 Documentation

- [Symfony 7.2](https://symfony.com/doc/7.2/index.html)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [AssetMapper](https://symfony.com/doc/current/frontend/asset_mapper.html)

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à :

- Signaler des bugs
- Proposer de nouvelles fonctionnalités
- Améliorer la documentation
