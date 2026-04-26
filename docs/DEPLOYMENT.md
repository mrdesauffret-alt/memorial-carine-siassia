# 🚀 Guide de Déploiement - Mémorial Carine SIASSIA

## Vue d'ensemble

Ce guide couvre le déploiement complet du mémorial en production, incluant la configuration serveur, la sécurité, et la maintenance.

## 📋 Prérequis

### Serveur
- **OS** : Ubuntu 20.04+ / CentOS 8+ / Debian 11+
- **RAM** : Minimum 2GB (recommandé 4GB+)
- **Stockage** : Minimum 20GB SSD
- **CPU** : 2 cœurs minimum

### Logiciels
- **PHP** : 7.4+ (recommandé 8.1+)
- **MySQL** : 5.7+ ou MariaDB 10.3+
- **Nginx** : 1.18+ ou Apache 2.4+
- **SSL** : Certificat Let's Encrypt ou commercial
- **Node.js** : 16+ (pour les tests et build)

## 🔧 Installation du serveur

### 1. Mise à jour du système
```bash
# Ubuntu/Debian
sudo apt update && sudo apt upgrade -y

# CentOS/RHEL
sudo yum update -y
```

### 2. Installation des paquets
```bash
# Ubuntu/Debian
sudo apt install -y nginx mysql-server php8.1-fpm php8.1-mysql php8.1-gd php8.1-curl php8.1-json php8.1-mbstring php8.1-xml php8.1-zip

# CentOS/RHEL
sudo yum install -y nginx mysql-server php-fpm php-mysql php-gd php-curl php-json php-mbstring php-xml php-zip
```

### 3. Configuration PHP
```bash
# Éditer la configuration PHP
sudo nano /etc/php/8.1/fpm/php.ini

# Modifications recommandées
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 30
memory_limit = 256M
date.timezone = Europe/Paris
```

### 4. Configuration MySQL
```bash
# Sécuriser MySQL
sudo mysql_secure_installation

# Créer la base de données
sudo mysql -u root -p
```

```sql
CREATE DATABASE memorial_carine CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'memorial_user'@'localhost' IDENTIFIED BY 'mot_de_passe_securise';
GRANT ALL PRIVILEGES ON memorial_carine.* TO 'memorial_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 📁 Déploiement du code

### 1. Cloner le projet
```bash
cd /var/www
sudo git clone https://github.com/votre-repo/memorial-carine-siassia.git
sudo chown -R www-data:www-data memorial-carine-siassia
```

### 2. Configuration de l'environnement
```bash
cd memorial-carine-siassia
sudo cp .env.example .env
sudo nano .env
```

```env
# Base de données
DB_HOST=localhost
DB_NAME=memorial_carine
DB_USER=memorial_user
DB_PASS=mot_de_passe_securise

# Analytics (optionnel)
GA_MEASUREMENT_ID=G-XXXXXXXXXX
HOTJAR_ID=1234567
SENTRY_DSN=https://votre-dsn@sentry.io/projet

# Sécurité
ADMIN_TOKEN=votre_token_admin_securise
ENCRYPTION_KEY=votre_cle_chiffrement_32_caracteres

# Production
APP_ENV=production
APP_DEBUG=false
```

### 3. Initialisation de la base de données
```bash
mysql -u memorial_user -p memorial_carine < database/schema.sql
```

### 4. Permissions des dossiers
```bash
sudo mkdir -p uploads
sudo chmod 755 uploads
sudo chown -R www-data:www-data uploads
sudo chmod 644 backend/config/database.php
```

## 🌐 Configuration Nginx

### 1. Fichier de configuration
```bash
sudo nano /etc/nginx/sites-available/memorial-carine-siassia
```

```nginx
server {
    listen 80;
    server_name memorial-carine-siassia.page.gd www.memorial-carine-siassia.page.gd;
    root /var/www/memorial-carine-siassia;
    index index.php index.html;

    # Logs
    access_log /var/log/nginx/memorial-carine-siassia.access.log;
    error_log /var/log/nginx/memorial-carine-siassia.error.log;

    # Sécurité
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss;

    # Cache statique
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # PHP
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # API
    location /backend/api/ {
        try_files $uri $uri/ /backend/api/index.php?$query_string;
    }

    # Uploads
    location /uploads/ {
        expires 1y;
        add_header Cache-Control "public";
    }

    # Service Worker
    location /sw.js {
        expires 0;
        add_header Cache-Control "no-cache, no-store, must-revalidate";
        add_header Pragma "no-cache";
    }

    # Sécurité - Bloquer l'accès aux fichiers sensibles
    location ~ /\. {
        deny all;
    }

    location ~ /(config|database|tests)/ {
        deny all;
    }

    # Redirection HTTPS (après configuration SSL)
    # return 301 https://$server_name$request_uri;
}
```

### 2. Activer le site
```bash
sudo ln -s /etc/nginx/sites-available/memorial-carine-siassia /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 🔒 Configuration SSL

### 1. Installation Certbot
```bash
# Ubuntu/Debian
sudo apt install certbot python3-certbot-nginx

# CentOS/RHEL
sudo yum install certbot python3-certbot-nginx
```

### 2. Obtenir le certificat
```bash
sudo certbot --nginx -d memorial-carine-siassia.page.gd -d www.memorial-carine-siassia.page.gd
```

### 3. Configuration HTTPS
```bash
sudo nano /etc/nginx/sites-available/memorial-carine-siassia
```

```nginx
server {
    listen 443 ssl http2;
    server_name memorial-carine-siassia.page.gd www.memorial-carine-siassia.page.gd;
    
    # SSL
    ssl_certificate /etc/letsencrypt/live/memorial-carine-siassia.page.gd/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/memorial-carine-siassia.page.gd/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    
    # HSTS
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    
    # ... reste de la configuration
}

# Redirection HTTP vers HTTPS
server {
    listen 80;
    server_name memorial-carine-siassia.page.gd www.memorial-carine-siassia.page.gd;
    return 301 https://$server_name$request_uri;
}
```

### 4. Renouvellement automatique
```bash
sudo crontab -e
# Ajouter cette ligne
0 12 * * * /usr/bin/certbot renew --quiet
```

## 🔐 Sécurité

### 1. Firewall
```bash
# UFW (Ubuntu)
sudo ufw enable
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'

# Firewalld (CentOS)
sudo firewall-cmd --permanent --add-service=ssh
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --reload
```

### 2. Fail2Ban
```bash
sudo apt install fail2ban
sudo nano /etc/fail2ban/jail.local
```

```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 3

[nginx-http-auth]
enabled = true

[nginx-limit-req]
enabled = true
```

### 3. Limitation de taux
```bash
sudo nano /etc/nginx/conf.d/rate-limiting.conf
```

```nginx
limit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;
limit_req_zone $binary_remote_addr zone=upload:10m rate=1r/s;

server {
    location /backend/api/ {
        limit_req zone=api burst=20 nodelay;
    }
    
    location /backend/upload.php {
        limit_req zone=upload burst=5 nodelay;
    }
}
```

## 📊 Monitoring

### 1. Installation des outils
```bash
# Htop pour le monitoring système
sudo apt install htop iotop nethogs

# Logwatch pour l'analyse des logs
sudo apt install logwatch
```

### 2. Configuration des logs
```bash
sudo nano /etc/logrotate.d/memorial-carine-siassia
```

```
/var/log/nginx/memorial-carine-siassia.*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
    postrotate
        systemctl reload nginx
    endscript
}
```

### 3. Monitoring des performances
```bash
# Script de monitoring
sudo nano /usr/local/bin/monitor-memorial.sh
```

```bash
#!/bin/bash
# Vérification de l'état des services
systemctl is-active --quiet nginx || echo "Nginx down"
systemctl is-active --quiet mysql || echo "MySQL down"
systemctl is-active --quiet php8.1-fpm || echo "PHP-FPM down"

# Vérification de l'espace disque
df -h | awk '$5 > 80 {print "Disk usage high: " $0}'

# Vérification de la mémoire
free -m | awk 'NR==2{if($3/$2*100 > 80) print "Memory usage high: " $3/$2*100 "%"}'
```

```bash
sudo chmod +x /usr/local/bin/monitor-memorial.sh
```

## 🔄 Sauvegardes

### 1. Script de sauvegarde
```bash
sudo nano /usr/local/bin/backup-memorial.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/backups/memorial-carine-siassia"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="memorial_carine"
DB_USER="memorial_user"
DB_PASS="mot_de_passe_securise"

# Créer le dossier de sauvegarde
mkdir -p $BACKUP_DIR

# Sauvegarde de la base de données
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_$DATE.sql

# Sauvegarde des fichiers
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/memorial-carine-siassia

# Supprimer les sauvegardes anciennes (plus de 30 jours)
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete

echo "Sauvegarde terminée: $DATE"
```

### 2. Automatisation
```bash
sudo chmod +x /usr/local/bin/backup-memorial.sh
sudo crontab -e
# Ajouter cette ligne pour une sauvegarde quotidienne à 2h
0 2 * * * /usr/local/bin/backup-memorial.sh
```

## 🚀 Déploiement continu

### 1. Script de déploiement
```bash
sudo nano /usr/local/bin/deploy-memorial.sh
```

```bash
#!/bin/bash
set -e

PROJECT_DIR="/var/www/memorial-carine-siassia"
BACKUP_DIR="/backups/memorial-carine-siassia"

echo "Début du déploiement..."

# Sauvegarde avant déploiement
/usr/local/bin/backup-memorial.sh

# Aller dans le dossier du projet
cd $PROJECT_DIR

# Sauvegarder les fichiers de configuration
cp .env .env.backup

# Récupérer les dernières modifications
git pull origin main

# Restaurer la configuration
cp .env.backup .env

# Mettre à jour la base de données si nécessaire
# mysql -u memorial_user -p memorial_carine < database/migrations/latest.sql

# Vider le cache
rm -rf /tmp/memorial-cache/*

# Redémarrer les services
systemctl reload nginx
systemctl reload php8.1-fpm

echo "Déploiement terminé avec succès!"
```

### 2. Webhook GitHub
```bash
sudo nano /usr/local/bin/github-webhook.sh
```

```bash
#!/bin/bash
# Script appelé par le webhook GitHub
/usr/local/bin/deploy-memorial.sh
```

## 🔧 Maintenance

### 1. Mise à jour des dépendances
```bash
# Mise à jour du système
sudo apt update && sudo apt upgrade -y

# Mise à jour des certificats SSL
sudo certbot renew

# Redémarrage des services
sudo systemctl restart nginx
sudo systemctl restart php8.1-fpm
sudo systemctl restart mysql
```

### 2. Nettoyage
```bash
# Nettoyer les logs anciens
sudo logrotate -f /etc/logrotate.conf

# Nettoyer les caches
sudo rm -rf /tmp/memorial-cache/*
sudo rm -rf /var/cache/nginx/*

# Optimiser la base de données
mysql -u memorial_user -p memorial_carine -e "OPTIMIZE TABLE memories, candles, visit_stats;"
```

## 📈 Optimisation

### 1. Cache Redis (optionnel)
```bash
sudo apt install redis-server
sudo nano /etc/redis/redis.conf
```

### 2. CDN (optionnel)
- Cloudflare
- AWS CloudFront
- KeyCDN

### 3. Monitoring avancé
- New Relic
- DataDog
- Grafana + Prometheus

## 🆘 Dépannage

### Problèmes courants

#### 1. Erreur 500
```bash
# Vérifier les logs
sudo tail -f /var/log/nginx/memorial-carine-siassia.error.log
sudo tail -f /var/log/php8.1-fpm.log

# Vérifier les permissions
sudo chown -R www-data:www-data /var/www/memorial-carine-siassia
sudo chmod -R 755 /var/www/memorial-carine-siassia
```

#### 2. Problème de base de données
```bash
# Vérifier la connexion
mysql -u memorial_user -p memorial_carine -e "SELECT 1;"

# Vérifier les processus MySQL
sudo systemctl status mysql
```

#### 3. Problème SSL
```bash
# Vérifier le certificat
sudo certbot certificates

# Renouveler le certificat
sudo certbot renew --force-renewal
```

## 📞 Support

En cas de problème :
1. Vérifier les logs d'erreur
2. Consulter la documentation
3. Contacter le support technique

---

*Guide de déploiement v1.0.0 - Mémorial Carine SIASSIA*
