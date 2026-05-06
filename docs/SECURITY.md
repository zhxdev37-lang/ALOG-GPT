# Documentation Sécurité - ALOG Academy

## Couches de Sécurité

### 1. Transport (HTTPS)
- Forcer HTTPS via `.htaccess` (décommenter les lignes RewriteCond/RewriteRule)
- Cookies `Secure` flag activé quand HTTPS détecté

### 2. Authentification
- **Hash** : Argon2id avec memory_cost=65536, time_cost=4, threads=3
- **Sessions** : 
  - Régénération ID toutes les 30 min
  - Validation IP + User-Agent
  - Expiration après 2h d'inactivité
  - HTTPOnly + Secure + SameSite=Strict
- **Rate Limiting** :
  - 5 tentatives max par IP pour login
  - Lockout 30 min après échecs
  - Fichier-based (pas besoin de Redis)

### 3. Autorisation (RBAC)
- Rôles hiérarchisés (level 1-100)
- Permissions granulaires (`lessons.create`, `users.read`)
- Wildcard support (`lessons.*`)
- Super Admin wildcard `*`

### 4. Input Validation
- **CSRF** : Token 32 bytes, validé sur tout POST
- **XSS** : `htmlspecialchars()` sur toute sortie
- **SQL Injection** : Prepared statements 100% du temps
- **Email** : `FILTER_SANITIZE_EMAIL` + `FILTER_VALIDATE_EMAIL`
- **Phone** : Regex marocaine `/^(\+212|0)[5-7][0-9]{8}$/`
- **Upload** : Seuls les avatars prédéfinis sont acceptés (pas d'upload arbitraire)

### 5. Audit & Logs
- `admin_logs` : IP, User-Agent, anciennes/nouvelles valeurs JSON
- `login_attempts` : Traçage brute force
- Fichier logs quotidiens dans `storage/logs/`

### 6. Headers de Sécurité (via .htaccess)
```apache
Header always set X-Content-Type-Options "nosniff"
Header always set X-Frame-Options "SAMEORIGIN"
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy "strict-origin-when-cross-origin"
```

## Checklist de Sécurité Pré-Déploiement

- [ ] Changer les clés par défaut dans `.env`
- [ ] Activer HTTPS et forcer la redirection
- [ ] Supprimer `display_errors` en production
- [ ] Vérifier les permissions dossiers (pas 777)
- [ ] Configurer les headers de sécurité dans .htaccess
- [ ] Tester le rate limiting sur login
- [ ] Vérifier que les tokens CSRF sont présents sur tous les formulaires
- [ ] Confirmer que les uploads sont désactivés (sauf avatars prédéfinis)
- [ ] Tester l'injection SQL sur les champs de recherche
- [ ] Vérifier la validation des emails et téléphones

## PWA & Offline Security
- Service Worker ne met en cache que les requêtes GET publiques
- Admin routes et API exclues du cache
- Pas de stockage local sensible (pas de JWT dans localStorage)

## Gestion des Incidents

### Compte Compromis
1. Suspendre via `UPDATE users SET status = 'suspended'`
2. Forcer déconnexion (supprimer session côté serveur)
3. Reset password via email
4. Logger l'incident dans `admin_logs`

### Fuite de Données
1. Identifier la table/source via `admin_logs`
2. Contacter les utilisateurs affectés par email
3. Appliquer un patch si vulnérabilité technique
4. Documenter dans les logs

## Conformité RGPD (Maroc)
- Données stockées : nom, email, téléphone, date naissance, région
- Pas de tracking tiers sans consentement
- Suppression compte possible (à implémenter via admin)
- Logs conservés 90 jours max
