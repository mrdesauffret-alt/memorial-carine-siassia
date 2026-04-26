# 🌹 Mémorial Carine SIASSIA

> Une Étoile Qui Continue de Briller

Un site web mémorial moderne et interactif dédié à Carine SIASSIA, offrant un espace de recueillement, de partage de souvenirs et de célébration de sa mémoire.

## ✨ Fonctionnalités

### 🎨 Frontend
- **Design moderne et émotionnel** avec animations fluides
- **Galerie de photos** interactive avec lightbox
- **Bougie virtuelle** avec système de comptage en temps réel
- **Partage de souvenirs** (anecdotes, photos, messages)
- **Méditation guidée** pour le recueillement
- **Timeline des événements** détaillée
- **PWA** (Progressive Web App) installable
- **Mode hors ligne** avec Service Worker
- **Design responsive** pour tous les appareils

### 🔧 Backend
- **API REST** complète pour la gestion des données
- **Base de données MySQL** avec schéma optimisé
- **Système de modération** des contributions
- **Upload d'images** avec optimisation automatique
- **Analytics** et monitoring des performances
- **Tableau de bord administrateur**

### 🧪 Tests
- **Tests unitaires** JavaScript (Jest)
- **Tests d'intégration** API (Postman)
- **Tests E2E** (Cypress)
- **Tests de performance** (Lighthouse)
- **Tests d'accessibilité** (axe-core)

## 🚀 Installation

### Prérequis
- PHP 7.4+ avec extensions PDO, GD, JSON
- MySQL 5.7+ ou MariaDB 10.3+
- Serveur web (Apache/Nginx)
- Node.js 16+ (pour les tests)

### 1. Cloner le projet
```bash
git clone https://github.com/votre-repo/memorial-carine-siassia.git
cd memorial-carine-siassia
```

### 2. Configuration de la base de données
```bash
# Créer la base de données
mysql -u root -p < database/schema.sql

# Ou via phpMyAdmin, importer le fichier database/schema.sql
```

### 3. Configuration de l'environnement
```bash
# Copier le fichier de configuration
cp .env.example .env

# Éditer les variables d'environnement
nano .env
```

Variables d'environnement requises :
```env
DB_HOST=localhost
DB_NAME=memorial_carine
DB_USER=root
DB_PASS=votre_mot_de_passe

# Analytics (optionnel)
GA_MEASUREMENT_ID=G-XXXXXXXXXX
HOTJAR_ID=1234567
SENTRY_DSN=https://votre-dsn@sentry.io/projet
```

### 4. Permissions des dossiers
```bash
# Créer et configurer les permissions
mkdir -p uploads
chmod 755 uploads
chmod 644 backend/config/database.php
```

### 5. Installation des dépendances de test
```bash
cd tests
npm install
```

## 🧪 Tests

### Tests unitaires
```bash
cd tests
npm test
```

### Tests avec couverture
```bash
npm run test:coverage
```

### Tests E2E
```bash
# Interface graphique
npm run test:e2e:open

# En ligne de commande
npm run test:e2e
```

### Tests API
```bash
npm run test:api
```

### Tests de performance
```bash
npm run test:lighthouse
```

## 📁 Structure du projet

```
memorial-carine-siassia/
├── assets/
│   ├── css/
│   │   ├── style.css              # Styles principaux
│   │   └── enhanced-styles.css    # Styles des fonctionnalités avancées
│   ├── js/
│   │   ├── animations.js          # Animations de base
│   │   ├── api.js                 # Gestionnaire API
│   │   ├── enhanced-features.js   # Fonctionnalités avancées
│   │   └── analytics.js           # Analytics et monitoring
│   └── images/                    # Images du mémorial
├── backend/
│   ├── api/                       # Endpoints API
│   │   ├── memories.php
│   │   ├── candles.php
│   │   └── analytics.php
│   ├── admin/                     # Interface d'administration
│   │   └── dashboard.php
│   ├── config/
│   │   └── database.php           # Configuration BDD
│   ├── models/                    # Modèles de données
│   │   ├── Memory.php
│   │   └── Candle.php
│   └── upload.php                 # Gestionnaire d'upload
├── database/
│   └── schema.sql                 # Schéma de base de données
├── tests/                         # Suite de tests
│   ├── unit/                      # Tests unitaires
│   ├── e2e/                       # Tests end-to-end
│   ├── api/                       # Tests API
│   └── package.json
├── docs/                          # Documentation
├── uploads/                       # Images uploadées
├── index.php                      # Page principale
├── offline.html                   # Page hors ligne
├── sw.js                          # Service Worker
└── README.md
```

## 🔧 Configuration

### Base de données
Le schéma de base de données inclut les tables suivantes :
- `memories` - Souvenirs et témoignages
- `candles` - Bougies virtuelles
- `visit_stats` - Statistiques de visite
- `contacts` - Messages de contact
- `events` - Événements du programme
- `gallery_photos` - Photos de la galerie
- `testimonials` - Témoignages prédéfinis
- `site_settings` - Paramètres du site
- `analytics_events` - Événements d'analytics

### API Endpoints

#### Memories
- `GET /backend/api/memories.php` - Récupérer les souvenirs approuvés
- `POST /backend/api/memories.php` - Créer un nouveau souvenir
- `GET /backend/api/memories.php/{id}` - Récupérer un souvenir spécifique
- `PUT /backend/api/memories.php/{id}` - Mettre à jour un souvenir (admin)
- `DELETE /backend/api/memories.php/{id}` - Supprimer un souvenir (admin)

#### Candles
- `GET /backend/api/candles.php` - Statistiques des bougies
- `POST /backend/api/candles.php` - Allumer une bougie
- `GET /backend/api/candles.php/recent` - Bougies récentes

#### Upload
- `POST /backend/upload.php` - Upload d'images

### Analytics
Le système d'analytics collecte automatiquement :
- Vues de page et temps passé
- Interactions utilisateur (clics, scroll, formulaires)
- Core Web Vitals (LCP, FID, CLS)
- Erreurs JavaScript et réseau
- Métriques de performance

## 🔒 Sécurité

### Mesures implémentées
- **Validation et sanitisation** de toutes les entrées
- **Protection CSRF** sur les formulaires
- **Limitation de taux** pour les API
- **Upload sécurisé** avec validation des types
- **Headers de sécurité** (CORS, CSP)
- **Gestion des erreurs** sans exposition d'informations sensibles

### Recommandations de production
- Configurer HTTPS avec certificat SSL
- Mettre en place un firewall applicatif
- Activer la compression Gzip
- Configurer la mise en cache
- Surveiller les logs d'erreur
- Effectuer des sauvegardes régulières

## 📊 Monitoring

### Métriques surveillées
- **Performance** : Temps de chargement, Core Web Vitals
- **Utilisation** : Visiteurs, pages vues, interactions
- **Erreurs** : JavaScript, API, serveur
- **Sécurité** : Tentatives d'intrusion, requêtes suspectes

### Outils intégrés
- Google Analytics 4
- Hotjar (analyse comportementale)
- Sentry (monitoring d'erreurs)
- Lighthouse (audit de performance)

## 🚀 Déploiement

### Préparation
1. Tester toutes les fonctionnalités en local
2. Optimiser les images et assets
3. Configurer les variables d'environnement
4. Préparer la base de données de production

### Serveur de production
1. Configurer le serveur web (Apache/Nginx)
2. Installer PHP et MySQL
3. Configurer SSL/TLS
4. Déployer le code
5. Configurer les sauvegardes

### Scripts de déploiement
```bash
# Déploiement automatique
./scripts/deploy.sh production

# Sauvegarde
./scripts/backup.sh

# Restauration
./scripts/restore.sh backup_file.sql
```

## 🤝 Contribution

### Développement
1. Fork le projet
2. Créer une branche feature (`git checkout -b feature/nouvelle-fonctionnalite`)
3. Commit les changements (`git commit -am 'Ajouter nouvelle fonctionnalité'`)
4. Push vers la branche (`git push origin feature/nouvelle-fonctionnalite`)
5. Créer une Pull Request

### Standards de code
- PHP : PSR-12
- JavaScript : ESLint + Prettier
- CSS : BEM methodology
- Tests : Couverture minimale de 80%

## 📝 Changelog

### Version 1.0.0
- ✅ Site mémorial complet
- ✅ Système de bougies virtuelles
- ✅ Partage de souvenirs
- ✅ API REST complète
- ✅ Tableau de bord admin
- ✅ Analytics et monitoring
- ✅ Tests automatisés
- ✅ PWA et mode hors ligne

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENCE](LICENCE) pour plus de détails.

## 📞 Support

Pour toute question ou problème :
- 📧 Email : support@memorial-carine-siassia.com
- 🐛 Issues : [GitHub Issues](https://github.com/votre-repo/memorial-carine-siassia/issues)
- 📖 Documentation : [Wiki du projet](https://github.com/votre-repo/memorial-carine-siassia/wiki)

## 🙏 Remerciements

- Famille et amis de Carine SIASSIA
- Communauté open source
- Contributeurs du projet

---

*Créé avec ❤️ en mémoire de Carine SIASSIA*
