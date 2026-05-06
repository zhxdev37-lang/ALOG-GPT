# Guide de Déploiement - ALOG Academy

## Déploiement sur Hébergement Mutualisé Gratuit

### AeonFree / InfinityFree / 000webhost

1. **Créer le compte**
   - Inscrivez-vous sur la plateforme gratuite
   - Créez un sous-domaine (ex: `alogacademy.aeonfree.com`)

2. **Uploader via FTP**
   - Host: `ftpupload.net` (AeonFree) ou l'hôte fourni
   - User: votre identifiant
   - Password: votre mot de passe
   - Uploadez **tout** sauf le dossier `docs/` dans `htdocs/` ou `public_html/`

3. **Créer la base de données**
   - MySQL Databases > Create Database
   - Nom: `epiz_xxx_alog` (selon la contrainte de la plateforme)
   - Créez l'utilisateur et associez-le

4. **Importer le schéma**
   - phpMyAdmin > Import > Choisir `sql/01_schema.sql`

5. **Configuration .env**
   - Créez le fichier `.env` à la racine (pas dans `public/`)
   - Remplissez les identifiants MySQL

6. **Vérification finale**
   - Accédez à `votresite.com`
   - Créez un compte
   - Passez-le en admin via phpMyAdmin : `UPDATE users SET role_id = 1 WHERE id = 1`

## Déploiement sur VPS (DigitalOcean, AWS, Vultr)

### Prérequis Serveur

```bash
# Ubuntu 22.04
sudo apt update && sudo apt upgrade -y
sudo apt install apache2 mysql-server php8.1 php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl -y
```

### Configuration Apache

```bash
sudo nano /etc/apache2/sites-available/alogacademy.conf
```

```apache
<VirtualHost *:80>
    ServerName alogacademy.ma
    DocumentRoot /var/www/alog-academy/public
    
    <Directory /var/www/alog-academy/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/alog-error.log
    CustomLog ${APACHE_LOG_DIR}/alog-access.log combined
</VirtualHost>
```

```bash
sudo a2ensite alogacademy.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### SSL avec Let's Encrypt

```bash
sudo apt install certbot python3-certbot-apache -y
sudo certbot --apache -d alogacademy.ma
```

### Base de Données

```bash
sudo mysql
CREATE DATABASE alog_academy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'alog_user'@'localhost' IDENTIFIED BY 'votre_mot_de_passe_fort';
GRANT ALL PRIVILEGES ON alog_academy.* TO 'alog_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Importer le schéma : `mysql -u alog_user -p alog_academy < sql/01_schema.sql`

### Permissions

```bash
sudo chown -R www-data:www-data /var/www/alog-academy
sudo chmod -R 755 /var/www/alog-academy
sudo chmod -R 775 /var/www/alog-academy/storage/cache
sudo chmod -R 775 /var/www/alog-academy/storage/logs
```

## Docker (Optionnel - Non requis)

```dockerfile
FROM php:8.1-apache
RUN docker-php-ext-install pdo pdo_mysql
COPY . /var/www/html/
RUN a2enmod rewrite
RUN chown -R www-data:www-data /var/www/html/storage
```

Note : Docker n'est pas recommandé pour l'hébergement gratuit.

## Vérification Post-Déploiement

- [ ] Page d'accueil accessible
- [ ] Inscription fonctionnelle
- [ ] Email de vérification envoyé (vérifier spam)
- [ ] Connexion admin fonctionnelle
- [ ] Création d'une leçon test
- [ ] Passage d'un quiz test
- [ ] Sitemap.xml accessible
- [ ] PWA installable (vérifier dans Chrome DevTools > Application)
- [ ] Mode sombre fonctionnel
- [ ] Responsive mobile testé
