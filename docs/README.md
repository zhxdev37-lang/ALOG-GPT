# ALOG ACADEMY - Documentation

## Table des Matières

1. [Installation](#installation)
2. [Déploiement](#déploiement)
3. [Architecture](#architecture)
4. [Base de Données](#base-de-données)
5. [Sécurité](#sécurité)
6. [Optimisation](#optimisation)
7. [API](#api)
8. [Guide Admin](#guide-admin)

---

## Installation

### Prérequis

- PHP 8.0 ou supérieur
- MySQL 5.7+ ou MariaDB 10.3+
- Extensions PHP : PDO, PDO_MySQL, mbstring, openssl, json, session
- Espace disque : 50 Mo minimum
- Mémoire PHP : 64M minimum (128M recommandé)

### Étapes d'Installation

1. **Uploader les fichiers**
   - Extraire l'archive sur votre ordinateur
   - Uploader tout le contenu via FTP dans le dossier `public_html/` de votre hébergement
   - Assurez-vous que le dossier `public/` correspond à la racine web

2. **Créer la base de données**
   - Connectez-vous à cPanel > MySQL Databases
   - Créez une base de données, un utilisateur, et donnez tous les privilèges
   - Notez le nom de la base, l'utilisateur et le mot de passe

3. **Importer le schéma**
   - Allez dans phpMyAdmin
   - Sélectionnez votre base de données
   - Importez le fichier `sql/01_schema.sql`

4. **Configurer l'environnement**
   - Renommez `.env.example` en `.env`
   - Remplissez vos informations de connexion à la base de données
   ```
   DB_HOST=localhost
   DB_NAME=votre_base
   DB_USER=votre_user
   DB_PASS=votre_mot_de_passe
   ```

5. **Permissions des dossiers**
   - Assurez-vous que ces dossiers sont inscriptibles (chmod 755 ou 775) :
     - `storage/cache/`
     - `storage/logs/`
     - `public/assets/avatars/` (si vous uploadez des avatars personnalisés)

6. **Vérification**
   - Accédez à votre domaine
   - La page d'accueil doit s'afficher
   - Le compte Super Admin par défaut n'existe pas - vous devez créer un utilisateur puis modifier son role_id à 1 dans la base de données

---

## Déploiement

### Hébergement Mutualisé Gratuit (AeonFree, InfinityFree)

1. L'architecture est **nativement compatible** avec ces plateformes
2. Aucune configuration serveur supplémentaire n'est requise
3. Le fichier `.htaccess` est préconfiguré pour la réécriture d'URLs

### Migrer vers un VPS/Cloud

Pour migrer facilement :
1. Exportez la base de données via phpMyAdmin
2. Copiez tous les fichiers via FTP/SCP
3. Importez la base sur le nouveau serveur
4. Mettez à jour le fichier `.env`
5. Assurez-vous que le dossier `public/` est la racine web (document root)

---

## Architecture

### Structure MVC Légère

```
/app
  /controllers    → Logique métier (Public, Auth, Admin, Student...)
  /models         → Accès données (User, Lesson, Quiz...)
  /views          → Templates (layouts, partials, pages)
  /services       → Services métier (Auth, Mailer, Logger)
  /helpers        → Fonctions globales
  /middleware     → Filtres requêtes (Auth, Admin, CSRF)
  /core           → Framework interne (Router, DB, View, Cache)

/public           → Racine web (index.php, assets, uploads)
/storage          → Cache, logs
/config           → Configuration
/routes           → Définition des routes
/docs             → Documentation
/sql              → Schéma et migrations
```

### Flux de Requête

1. Requête → `public/index.php`
2. Chargement config + autoloader
3. `routes/web.php` → matching route
4. Middleware exécuté (Auth, CSRF...)
5. Controller appelé
6. Model interroge la base
7. View rend le template avec layout
8. Réponse HTML/JSON envoyée

### Sécurité

- **CSRF** : Token généré par session, validé sur POST
- **XSS** : Toutes les sorties passent par `htmlspecialchars()`
- **SQL Injection** : Requêtes préparées PDO uniquement
- **Passwords** : Hash Argon2id avec options sécurisées
- **Sessions** : HTTPOnly, Secure, SameSite=Strict
- **Rate Limiting** : Par IP et action (fichier cache)
- **RBAC** : Rôles et permissions granulaires en base

---

## Base de Données

### Tables Principales

| Table | Description |
|-------|-------------|
| `users` | Comptes étudiants et admins |
| `roles` | Rôles RBAC (super_admin, content_manager...) |
| `school_levels` | Niveaux scolaires (TC, 1ère Bac, 2ème Bac...) |
| `filieres` | Filières par niveau |
| `subjects` | Matières (Maths, Physique, SVT...) |
| `subject_level` | Liaison matières-niveaux-filières |
| `lessons` | Leçons avec médias externes |
| `lesson_progress` | Progression vidéo/quiz par utilisateur |
| `quizzes` | Quiz associés aux leçons |
| `questions` | Questions QCM/Vrai-Faux |
| `quiz_attempts` | Tentatives de quiz avec réponses |
| `plans` | Plans d'abonnement (Free, Pro, Ultra) |
| `subscriptions` | Abonnements actifs/expirés |
| `xp_transactions` | Historique des gains/dépenses XP |
| `achievements` | Définitions des badges |
| `user_achievements` | Badges gagnés |
| `weekly_rankings` | Classements hebdomadaires |
| `blog_posts` | Articles SEO |
| `blog_categories` | Catégories blog |
| `events` | Événements et webinaires |
| `event_registrations` | Inscriptions aux événements |
| `contacts` | Messages du formulaire |
| `faqs` | Questions fréquentes |
| `admin_logs` | Journal d'audit admin |
| `settings` | Configuration clé-valeur |

### Optimisations

- Index sur `users.email`, `users.status`, `users.xp_total`
- Index sur `lessons.subject_id`, `lessons.school_level_id`
- Fulltext sur `lessons.title+description`, `blog_posts.title+content`
- Clés étrangères avec ON DELETE CASCADE/RESTRICT appropriés
- Normalisation 3NF

---

## Optimisation Performance

### Cache
- **Query Cache** : Résultats de requêtes fréquentes en fichier (1h TTL)
- **Page Cache** : Homepage et blog list mis en cache
- **Cache directory** : `storage/cache/`

### Frontend
- Bootstrap 5 + Icons via CDN (compression gzip côté serveur)
- Google Fonts avec `display=swap`
- Images lazy loading via IntersectionObserver
- JavaScript `defer`

### Base de Données
- Requêtes paginées partout
- `SELECT` spécifiques (pas de `SELECT *` dans les listings)
- Jointures optimisées avec index

---

## API Interne

### Endpoints JSON (AJAX)

| Endpoint | Méthode | Description |
|----------|---------|-------------|
| `/lecon/progres-video` | POST | Met à jour la progression vidéo |
| `/lecon/debloquer-xp` | POST | Débloque une leçon avec XP |
| `/quiz/soumettre` | POST | Soumet un quiz, retourne score |
| `/paiement/process` | POST | Initialise un paiement |
| `/webhook/cmi` | POST | Webhook CMI |
| `/webhook/paypal` | POST | Webhook PayPal |

### En-têtes Requises

Pour les requêtes AJAX : `X-Requested-With: XMLHttpRequest`
Token CSRF : passé en header `X-CSRF-Token` ou dans le body `_token`

---

## Guide Admin

### Accès Initial

1. Créez un compte via `/inscription`
2. Connectez-vous à phpMyAdmin
3. Exécutez : `UPDATE users SET role_id = 1 WHERE id = VOTRE_ID`
4. Accédez à `/admin`

### Gestion des Contenus

- **Niveaux/Filières** : Créez d'abord le niveau, puis les filières associées
- **Matières** : Créez la matière, puis associez-la aux niveaux/filières
- **Leçons** : Liens YouTube et PDF hébergés sur GitHub (raw links)
- **Quiz** : Créez le quiz, puis ajoutez les questions dans l'éditeur

### SEO Blog

- Remplissez toujours `meta_title`, `meta_description`, `slug`
- Utilisez des tags pertinents séparés par des virgules
- L'image OpenGraph doit faire 1200x630px minimum

### Paiements

- Configurez `cmi_merchant_id` et `cmi_api_key` dans Paramètres pour activer CMI
- Configurez `paypal_client_id` pour PayPal
- Le numéro WhatsApp est utilisé pour le paiement manuel

---

## Support & Maintenance

### Logs
- Erreurs PHP : `storage/logs/error.log`
- Activité : `storage/logs/YYYY-MM-DD.log`
- Logs admin : table `admin_logs`

### Backup
- Base de données : export via phpMyAdmin régulièrement
- Fichiers : sauvegardez tout le dossier sauf `storage/cache/`

### Mises à jour
1. Sauvegardez la base et les fichiers
2. Uploadez les nouveaux fichiers (écraser)
3. Exécutez les nouvelles migrations SQL si fournies
4. Videz `storage/cache/`
