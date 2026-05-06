# Guide d'Architecture - ALOG Academy

## Vue d'Ensemble

ALOG Academy est construit sur une **architecture MVC personnalisée ultra-légère**, conçue spécifiquement pour fonctionner sur des hébergements mutualisés gratuits sans dépendances lourdes.

## Philosophie

- **Zero dependency** : Pas de Composer obligatoire, pas de framework externe
- **Shared-hosting first** : Chaque décision technique privilégie la compatibilité avec cPanel/AeonFree/InfinityFree
- **Security by default** : CSRF, XSS, SQL injection prévenus nativement
- **SEO-first** : Sitemap, OpenGraph, schema.org, JSON-LD intégrés dans le layout

## Diagramme des Composants

```
Requête HTTP
    ↓
public/index.php (bootstrap)
    ↓
config/app.php (config + autoloader)
    ↓
routes/web.php (route matching)
    ↓
Middleware (Auth/Admin/CSRF/Guest)
    ↓
Controller (ex: AdminController)
    ↓
Service Layer (Auth, Mailer, Logger)
    ↓
Model (BaseModel → User/Lesson/Quiz...)
    ↓
Database (PDO wrapper + query cache)
    ↓
View (layout + sections + partials)
    ↓
Response HTML/JSON
```

## Le Core Framework

### Database.php
- Singleton PDO avec persistent connections
- Query cache en mémoire (durée de la requête)
- Méthodes `query()`, `fetch()`, `execute()` avec prepared statements
- Transactions supportées

### Router.php
- Routes définies avec regex simples
- Paramètres nommés `{slug}`, `{id}`
- Support middleware chainé
- Pas de routeur dynamique coûteux

### View.php
- Layout system avec sections
- Partials réutilisables
- Cache de rendu possible
- Output JSON direct pour API

### Session.php
- Protection fixation/hijacking
- Régénération automatique toutes les 30 min
- HTTPOnly + Secure + SameSite

### Security.php
- Token CSRF cryptographiquement sécurisé (32 bytes)
- Sanitization HTML via `htmlspecialchars`
- Rate limiting fichier-based (pas de Redis requis)
- Argon2id pour les mots de passe

### Cache.php
- Fichier-based TTL cache
- `remember()` pattern
- Auto-flush sur écriture

## Gamification Engine

### XP Flow
1. User complète vidéo → `LessonProgress::updateVideoProgress()`
2. User passe quiz → `QuizController::submit()`
3. Si quiz réussi → `LessonProgress::completeQuiz()` puis `checkAndCompleteLesson()`
4. XP ajouté via `User::addXp()`
5. Transaction loggée dans `xp_transactions`
6. Niveau recalculé automatiquement
7. `Achievement::checkAndAward()` vérifie les nouveaux badges
8. `WeeklyRanking::updateCurrentWeek()` met à jour le classement

### Weekly Ranking Reset
- Pas de cron job requis (trop lourd pour free hosting)
- Le classement est recalculé à la volée lors des actions utilisateur
- La table `weekly_rankings` utilise `week_start` comme clé de partition logique

## Subscription & Access Control

### Plan Hierarchy
- Free (1) → Limited access
- Pro (2) → Full access
- Ultra (3) → Full + priority support

### Lesson Lock Logic
```
if (lesson.plan_id > user.plan_id) {
    if (lesson.xp_unlock_cost && user.xp_current >= cost) {
        allow_unlock_with_xp();
    } else {
        block_access();
    }
}
```

## Media Strategy

- **YouTube** : Embed iframes uniquement (zero bandwidth)
- **PDFs** : GitHub raw links affichés dans `<iframe>` ou PDF.js
- **Images** : URLs externes (GitHub, CDN) sauf logo et avatars
- **Avatar** : 5 fichiers PNG locaux dans `public/assets/avatars/`

## Scalability Path

Actuel (Free Hosting) → Futur (VPS/Cloud)
- Remplacer `Cache` fichier par Redis/Memcached
- Ajouter Composer + PHPMailer + Google API Client
- Activer cron jobs pour le ranking hebdo automatique
- Ajouter CDN CloudFlare
- Migrer vers MySQL 8 sur RDS

## Tests Recommandés (Manuels)

1. Inscription complète avec vérification email
2. Connexion + navigation dashboard
3. Passage d'un quiz complet (vidéo + QCM)
4. Vérification XP et badge gagné
5. Changement de niveau (test 3 mois constraint)
6. Paiement test (mode sandbox CMI/PayPal)
7. Création d'article blog avec SEO metadata
8. Vérification sitemap.xml et robots.txt
9. Test responsive mobile
10. Test mode sombre
