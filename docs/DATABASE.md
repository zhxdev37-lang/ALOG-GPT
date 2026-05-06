# Documentation Base de Données - ALOG Academy

## Schéma Relationnel

```
roles (1) ←── (N) users
school_levels (1) ←── (N) filieres
school_levels (1) ←── (N) users (school_level_id)
school_levels (1) ←── (N) lessons
filieres (1) ←── (N) users (filiere_id)
subjects (1) ←── (N) lessons
subjects (N) ←── (N) school_levels (via subject_level)
lessons (1) ←── (1) quizzes (quiz_id)
lessons (1) ←── (N) lesson_progress
quizzes (1) ←── (N) questions
quizzes (1) ←── (N) quiz_attempts
users (1) ←── (N) lesson_progress
users (1) ←── (N) quiz_attempts
users (1) ←── (N) xp_transactions
users (1) ←── (N) user_achievements
users (1) ←── (N) subscriptions
users (1) ←── (N) event_registrations
users (1) ←── (N) admin_logs
users (1) ←── (N) blog_posts (author_id)
plans (1) ←── (N) subscriptions
plans (1) ←── (N) lessons (plan_id)
achievements (1) ←── (N) user_achievements
blog_categories (1) ←── (N) blog_posts
events (1) ←── (N) event_registrations
```

## Contraintes Clés Étrangères

| Table | Colonne | Référence | ON DELETE |
|-------|---------|-----------|-----------|
| users | role_id | roles.id | RESTRICT |
| users | school_level_id | school_levels.id | SET NULL |
| users | filiere_id | filieres.id | SET NULL |
| filieres | school_level_id | school_levels.id | CASCADE |
| subject_level | subject_id | subjects.id | CASCADE |
| subject_level | school_level_id | school_levels.id | CASCADE |
| subject_level | filiere_id | filieres.id | CASCADE |
| lessons | subject_id | subjects.id | CASCADE |
| lessons | school_level_id | school_levels.id | CASCADE |
| lessons | filiere_id | filieres.id | CASCADE |
| quizzes | lesson_id | lessons.id | CASCADE |
| questions | quiz_id | quizzes.id | CASCADE |
| quiz_attempts | user_id | users.id | CASCADE |
| quiz_attempts | quiz_id | quizzes.id | CASCADE |
| subscriptions | user_id | users.id | CASCADE |
| subscriptions | plan_id | plans.id | RESTRICT |
| blog_posts | author_id | users.id | RESTRICT |
| blog_posts | category_id | blog_categories.id | SET NULL |
| event_registrations | event_id | events.id | CASCADE |
| event_registrations | user_id | users.id | CASCADE |
| admin_logs | user_id | users.id | CASCADE |
| promo_codes | applies_to_plan_id | plans.id | SET NULL |

## Index Stratégiques

### Indexes de Recherche
- `lessons` : FULLTEXT sur `(title, description)`
- `blog_posts` : FULLTEXT sur `(title, content, excerpt)`

### Indexes de Performance
- `users` : INDEX sur `email`, `status`, `xp_total`, `region`
- `lesson_progress` : UNIQUE sur `(user_id, lesson_id)`
- `weekly_rankings` : UNIQUE sur `(user_id, week_start)`
- `subscriptions` : INDEX sur `status`, `expires_at`

## Types de Données JSON

### roles.permissions
```json
["lessons.*", "users.read", "blog.*"]
```

### plans.features
```json
["Leçons illimitées", "Support email", "Classement national"]
```

### questions.options
```json
["Option A", "Option B", "Option C", "Option D"]
```

### quiz_attempts.answers
```json
{"1":{"user_answer":"A","points":1},"2":{"user_answer":"B","points":0}}
```

## Stratégie de Sauvegarde

### Export Quotidien Recommandé
```bash
mysqldump -u user -p database_name > backup_$(date +%Y%m%d).sql
```

### Tables Critiques à Sauvegarder en Priorité
1. `users` (données personnelles)
2. `lesson_progress` (progression étudiante)
3. `xp_transactions` (économie XP)
4. `subscriptions` (revenus)
5. `quiz_attempts` (résultats)

## Maintenance

### Nettoyage Recommandé (mensuel)
```sql
-- Supprimer les tokens expirés
DELETE FROM email_verifications WHERE expires_at < NOW() - INTERVAL 7 DAY;
DELETE FROM password_resets WHERE expires_at < NOW() - INTERVAL 7 DAY;

-- Archiver les vieux logs admin
DELETE FROM admin_logs WHERE created_at < NOW() - INTERVAL 90 DAY;

-- Vider les tentatives de connexion anciennes
DELETE FROM login_attempts WHERE created_at < NOW() - INTERVAL 7 DAY;
```

### Optimisation (trimestrielle)
```sql
OPTIMIZE TABLE users;
OPTIMIZE TABLE lesson_progress;
OPTIMIZE TABLE xp_transactions;
```
