# Setup Guide - Comparator

## 📋 Prérequis

- PHP >= 8.2
- Composer 2.x
- Node.js >= 18.x
- **Docker Desktop** (pour MySQL et phpMyAdmin)
- Git

## 🚀 Installation complète

### Étape 1: Cloner et préparer le projet

```bash
cd /Users/faez/Downloads/Projets-git-solo/Symfony/comparator

# Vérifier la version PHP
php -v  # Doit être >= 8.2
```

### Étape 2: Installer les dépendances

```bash
# Dépendances PHP
composer install

# Dépendances Node.js (pour Tailwind CSS)
npm install
```

### Étape 3: Choisir votre mode de base de données

#### Option A: Avec Docker (Recommandé) 🐳

**Avantages:**

- MySQL et phpMyAdmin préinstallés
- Isolation complète
- Pas de configuration système

```bash
# 1. Démarrer les services Docker
docker compose up -d

# 2. Vérifier que les conteneurs sont en cours d'exécution
docker compose ps

# Vous devriez voir:
# - database (MySQL)
# - phpmyadmin
# - app

# 3. Créer la base de données
docker compose exec app php bin/console doctrine:database:create

# 4. Exécuter les migrations
docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction

# 5. (Optionnel) Charger les fixtures
docker compose exec app php bin/console doctrine:fixtures:load --no-interaction

# Accès:
# - Application: http://localhost:8000
# - phpMyAdmin: http://localhost:8080
#   Utilisateur: root
#   Mot de passe: symfony
```

**Configuration pour Docker:**

Votre `.env.local` doit contenir:

```env
DATABASE_URL="mysql://symfony:symfony@database:3306/symfony?serverVersion=8.4&charset=utf8mb4"
```

#### Option B: Sans Docker (MySQL local) 💻

**Prérequis:**

- MySQL 8.0+ installé localement
- Serveur MySQL en cours d'exécution

```bash
# 1. Créer un utilisateur MySQL
mysql -u root -p
```

```sql
CREATE DATABASE comparator CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'comparator'@'localhost' IDENTIFIED BY 'comparator';
GRANT ALL PRIVILEGES ON comparator.* TO 'comparator'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

```bash
# 2. Modifier .env.local
# Commentez la ligne Docker et décommentez la ligne locale:
```

Votre `.env.local` doit contenir:

```env
DATABASE_URL="mysql://comparator:comparator@127.0.0.1:3306/comparator?serverVersion=8.0&charset=utf8mb4"
```

```bash
# 3. Créer la base et exécuter les migrations
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction

# 4. (Optionnel) Charger les fixtures
php bin/console doctrine:fixtures:load --no-interaction
```

### Étape 4: Compiler les assets CSS

```bash
# Mode développement (avec watch)
npm run dev

# OU mode production (compilation unique)
npm run build
```

**Important:** Laissez `npm run dev` tourner dans un terminal séparé pendant le développement. Il recompilera automatiquement le CSS à chaque modification.

### Étape 5: Lancer l'application

#### Avec Docker

```bash
# L'application est accessible via le conteneur
# Vérifiez que le port est bien configuré dans compose.yaml
docker compose up -d

# Accédez à http://localhost:8000
```

#### Sans Docker

```bash
# Option 1: Symfony CLI (recommandé)
symfony server:start

# Option 2: Serveur PHP natif
php -S localhost:8000 -t public/

# Accédez à http://localhost:8000
```

### Étape 6: Créer un utilisateur de test

```bash
# Avec Docker
docker compose exec app php bin/console

# Sans Docker
php bin/console
```

Ou utilisez les fixtures pour créer des données de test.

## 🔧 Commandes utiles

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

# Supprimer tout (attention: supprime les données!)
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
```

### Cache Symfony

```bash
# Avec Docker
docker compose exec app php bin/console cache:clear

# Sans Docker
php bin/console cache:clear
```

### Assets (CSS)

```bash
# Watch mode (développement)
npm run dev

# Build production
npm run build

# Mise à jour de Browserslist
npx update-browserslist-db@latest
```

## 🐛 Résolution de problèmes

### "Connection refused" à la base de données

**Avec Docker:**

```bash
# Vérifier que les conteneurs sont en cours d'exécution
docker compose ps

# Si non, les démarrer
docker compose up -d

# Vérifier les logs du conteneur database
docker compose logs database
```

**Sans Docker:**

```bash
# Vérifier que MySQL est en cours d'exécution
# MacOS:
brew services list

# Démarrer MySQL si nécessaire
brew services start mysql
```

### Le CSS ne se charge pas

```bash
# 1. Recompiler le CSS
npm run build

# 2. Vider le cache Symfony
php bin/console cache:clear

# 3. Vérifier que le fichier existe
ls -la public/css/output.css
```

### Erreur "Table doesn't exist"

```bash
# Avec Docker
docker compose exec app php bin/console doctrine:migrations:migrate

# Sans Docker
php bin/console doctrine:migrations:migrate

# Si ça ne fonctionne pas, forcer la mise à jour du schéma
php bin/console doctrine:schema:update --force
```

### Port 3306 ou 8080 déjà utilisé

Si les ports sont déjà utilisés, modifiez `compose.yaml`:

```yaml
database:
  ports:
    - "3307:3306"  # Changez 3306 en 3307

phpmyadmin:
  ports:
    - "8081:80"  # Changez 8080 en 8081
```

Puis mettez à jour `.env.local`:

```env
DATABASE_URL="mysql://symfony:symfony@127.0.0.1:3307/symfony?serverVersion=8.4&charset=utf8mb4"
```

## 📊 Architecture

### Sans Docker (Simple)

```bash
Navigateur → PHP serveur (localhost:8000) → MySQL local (localhost:3306)
```

### Avec Docker (Recommandé)

```bash
Navigateur → Container app → Container database (MySQL)
         ↓
phpMyAdmin (localhost:8080)
```

## ✅ Checklist de vérification

- [ ] PHP >= 8.2 installé
- [ ] Composer installé
- [ ] Node.js >= 18.x installé
- [ ] Docker Desktop installé (si utilisation Docker)
- [ ] `composer install` exécuté
- [ ] `npm install` exécuté
- [ ] Services Docker démarrés OU MySQL local en cours d'exécution
- [ ] `.env.local` configuré correctement
- [ ] Base de données créée
- [ ] Migrations exécutées
- [ ] CSS compilé (`npm run build`)
- [ ] Serveur démarré

## 🎉 Test final

Une fois tout installé, testez:

1. **Accédez à l'application**: <http://localhost:8000>
2. **Vérifiez le CSS**: La page doit être stylée (pas de texte brut)
3. **Inscrivez-vous**: Créez un compte utilisateur
4. **Connectez-vous**: Testez la connexion
5. **Liste des voitures**: Accédez à /voiture
6. **Comparateur**: Testez /comparison/voiture

Si tout fonctionne → **Bravo! 🚀**

## 📚 Documentation

- [Documentation du projet](./UPGRADE_GUIDE.md)
- [Résumé des changements](./CHANGES_SUMMARY.md)
- [Symfony 7.2](https://symfony.com/doc/7.2/)
- [Docker Compose](https://docs.docker.com/compose/)
