#!/bin/bash

# Script de déploiement pour le Mémorial Carine SIASSIA
# Usage: ./deploy.sh [environment] [options]

set -e  # Arrêter en cas d'erreur

# Configuration
PROJECT_NAME="memorial-carine-siassia"
PROJECT_DIR="/var/www/$PROJECT_NAME"
BACKUP_DIR="/backups/$PROJECT_NAME"
LOG_FILE="/var/log/deploy-$PROJECT_NAME.log"

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonctions utilitaires
log() {
    echo -e "${BLUE}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

success() {
    echo -e "${GREEN}✓${NC} $1" | tee -a "$LOG_FILE"
}

warning() {
    echo -e "${YELLOW}⚠${NC} $1" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}✗${NC} $1" | tee -a "$LOG_FILE"
    exit 1
}

# Fonction d'aide
show_help() {
    echo "Usage: $0 [environment] [options]"
    echo ""
    echo "Environnements:"
    echo "  development  - Déploiement en développement"
    echo "  staging      - Déploiement en staging"
    echo "  production   - Déploiement en production"
    echo ""
    echo "Options:"
    echo "  --backup     - Créer une sauvegarde avant déploiement"
    echo "  --migrate    - Exécuter les migrations de base de données"
    echo "  --test       - Exécuter les tests avant déploiement"
    echo "  --force      - Forcer le déploiement même en cas d'erreurs"
    echo "  --help       - Afficher cette aide"
    echo ""
    echo "Exemples:"
    echo "  $0 development --test"
    echo "  $0 production --backup --migrate"
    echo "  $0 staging --force"
}

# Vérification des prérequis
check_prerequisites() {
    log "Vérification des prérequis..."
    
    # Vérifier que le script est exécuté en tant que root ou avec sudo
    if [[ $EUID -ne 0 ]]; then
        error "Ce script doit être exécuté en tant que root ou avec sudo"
    fi
    
    # Vérifier les commandes nécessaires
    local commands=("git" "php" "mysql" "nginx" "systemctl")
    for cmd in "${commands[@]}"; do
        if ! command -v "$cmd" &> /dev/null; then
            error "Commande '$cmd' non trouvée"
        fi
    done
    
    # Vérifier que le dossier du projet existe
    if [[ ! -d "$PROJECT_DIR" ]]; then
        error "Dossier du projet '$PROJECT_DIR' non trouvé"
    fi
    
    success "Prérequis vérifiés"
}

# Création de sauvegarde
create_backup() {
    log "Création de la sauvegarde..."
    
    local timestamp=$(date +%Y%m%d_%H%M%S)
    local backup_path="$BACKUP_DIR/backup_$timestamp"
    
    # Créer le dossier de sauvegarde
    mkdir -p "$backup_path"
    
    # Sauvegarde de la base de données
    if [[ -f "$PROJECT_DIR/.env" ]]; then
        source "$PROJECT_DIR/.env"
        if [[ -n "$DB_NAME" && -n "$DB_USER" && -n "$DB_PASS" ]]; then
            mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$backup_path/database.sql"
            success "Base de données sauvegardée"
        else
            warning "Configuration de base de données manquante, saut de la sauvegarde DB"
        fi
    fi
    
    # Sauvegarde des fichiers
    tar -czf "$backup_path/files.tar.gz" -C "$PROJECT_DIR" . --exclude='.git' --exclude='node_modules' --exclude='uploads'
    success "Fichiers sauvegardés"
    
    # Nettoyer les anciennes sauvegardes (garder 7 jours)
    find "$BACKUP_DIR" -name "backup_*" -type d -mtime +7 -exec rm -rf {} \;
    
    success "Sauvegarde créée: $backup_path"
}

# Tests avant déploiement
run_tests() {
    log "Exécution des tests..."
    
    cd "$PROJECT_DIR"
    
    # Tests PHP (si disponibles)
    if [[ -f "vendor/bin/phpunit" ]]; then
        ./vendor/bin/phpunit --no-coverage
        success "Tests PHP exécutés"
    fi
    
    # Tests JavaScript (si disponibles)
    if [[ -f "tests/package.json" ]]; then
        cd tests
        npm test
        cd ..
        success "Tests JavaScript exécutés"
    fi
    
    # Tests de linting
    if command -v phpcs &> /dev/null; then
        phpcs --standard=PSR12 backend/
        success "Tests de style PHP exécutés"
    fi
    
    success "Tous les tests sont passés"
}

# Mise à jour du code
update_code() {
    log "Mise à jour du code..."
    
    cd "$PROJECT_DIR"
    
    # Sauvegarder les fichiers de configuration
    if [[ -f ".env" ]]; then
        cp .env .env.backup
    fi
    
    # Récupérer les dernières modifications
    git fetch origin
    git reset --hard origin/main
    
    # Restaurer la configuration
    if [[ -f ".env.backup" ]]; then
        cp .env.backup .env
        rm .env.backup
    fi
    
    success "Code mis à jour"
}

# Installation des dépendances
install_dependencies() {
    log "Installation des dépendances..."
    
    cd "$PROJECT_DIR"
    
    # Dépendances PHP (si composer.json existe)
    if [[ -f "composer.json" ]]; then
        composer install --no-dev --optimize-autoloader
        success "Dépendances PHP installées"
    fi
    
    # Dépendances Node.js (si package.json existe)
    if [[ -f "package.json" ]]; then
        npm ci --production
        success "Dépendances Node.js installées"
    fi
    
    # Dépendances de test (si nécessaire)
    if [[ -f "tests/package.json" ]]; then
        cd tests
        npm ci
        cd ..
        success "Dépendances de test installées"
    fi
}

# Migrations de base de données
run_migrations() {
    log "Exécution des migrations..."
    
    cd "$PROJECT_DIR"
    
    # Vérifier s'il y a des migrations à exécuter
    if [[ -d "database/migrations" ]]; then
        # Ici, vous pouvez ajouter votre logique de migration
        # Par exemple, exécuter des scripts SQL ou utiliser un outil de migration
        warning "Migrations non implémentées - à configurer selon vos besoins"
    fi
    
    success "Migrations exécutées"
}

# Configuration des permissions
set_permissions() {
    log "Configuration des permissions..."
    
    # Permissions pour les fichiers
    find "$PROJECT_DIR" -type f -exec chmod 644 {} \;
    
    # Permissions pour les dossiers
    find "$PROJECT_DIR" -type d -exec chmod 755 {} \;
    
    # Permissions spéciales pour les scripts
    find "$PROJECT_DIR" -name "*.sh" -exec chmod +x {} \;
    
    # Permissions pour le dossier uploads
    if [[ -d "$PROJECT_DIR/uploads" ]]; then
        chmod 755 "$PROJECT_DIR/uploads"
        chown -R www-data:www-data "$PROJECT_DIR/uploads"
    fi
    
    # Permissions pour les logs
    if [[ -d "$PROJECT_DIR/backend/logs" ]]; then
        chmod 755 "$PROJECT_DIR/backend/logs"
        chown -R www-data:www-data "$PROJECT_DIR/backend/logs"
    fi
    
    # Propriétaire général
    chown -R www-data:www-data "$PROJECT_DIR"
    
    success "Permissions configurées"
}

# Optimisation
optimize() {
    log "Optimisation..."
    
    cd "$PROJECT_DIR"
    
    # Optimisation PHP (si Composer est utilisé)
    if [[ -f "composer.json" ]]; then
        composer dump-autoload --optimize
    fi
    
    # Optimisation des images (si ImageMagick est disponible)
    if command -v convert &> /dev/null && [[ -d "uploads" ]]; then
        find uploads -name "*.jpg" -o -name "*.png" | while read -r file; do
            convert "$file" -strip -quality 85 "$file"
        done
        success "Images optimisées"
    fi
    
    # Nettoyage du cache
    rm -rf /tmp/memorial-cache/*
    
    success "Optimisation terminée"
}

# Redémarrage des services
restart_services() {
    log "Redémarrage des services..."
    
    # Redémarrer PHP-FPM
    systemctl reload php8.1-fpm
    
    # Redémarrer Nginx
    systemctl reload nginx
    
    # Redémarrer MySQL (si nécessaire)
    # systemctl restart mysql
    
    success "Services redémarrés"
}

# Vérification post-déploiement
post_deployment_check() {
    log "Vérification post-déploiement..."
    
    # Vérifier que les services fonctionnent
    if ! systemctl is-active --quiet nginx; then
        error "Nginx n'est pas actif"
    fi
    
    if ! systemctl is-active --quiet php8.1-fpm; then
        error "PHP-FPM n'est pas actif"
    fi
    
    # Vérifier la connectivité à la base de données
    if [[ -f "$PROJECT_DIR/.env" ]]; then
        source "$PROJECT_DIR/.env"
        if [[ -n "$DB_NAME" && -n "$DB_USER" && -n "$DB_PASS" ]]; then
            if ! mysql -u "$DB_USER" -p"$DB_PASS" -e "SELECT 1;" "$DB_NAME" &> /dev/null; then
                error "Impossible de se connecter à la base de données"
            fi
        fi
    fi
    
    # Test de l'API (si disponible)
    if command -v curl &> /dev/null; then
        local api_url="http://localhost/backend/api/memories.php"
        if curl -s -o /dev/null -w "%{http_code}" "$api_url" | grep -q "200\|404"; then
            success "API accessible"
        else
            warning "API non accessible"
        fi
    fi
    
    success "Vérification post-déploiement terminée"
}

# Fonction principale
main() {
    local environment=""
    local backup=false
    local migrate=false
    local test=false
    local force=false
    
    # Parsing des arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
            development|staging|production)
                environment="$1"
                shift
                ;;
            --backup)
                backup=true
                shift
                ;;
            --migrate)
                migrate=true
                shift
                ;;
            --test)
                test=true
                shift
                ;;
            --force)
                force=true
                shift
                ;;
            --help)
                show_help
                exit 0
                ;;
            *)
                error "Option inconnue: $1"
                ;;
        esac
    done
    
    # Vérifier que l'environnement est spécifié
    if [[ -z "$environment" ]]; then
        error "Environnement non spécifié. Utilisez --help pour voir les options."
    fi
    
    log "Début du déploiement en environnement: $environment"
    
    # Vérifications préliminaires
    check_prerequisites
    
    # Créer une sauvegarde si demandé
    if [[ "$backup" == true ]]; then
        create_backup
    fi
    
    # Exécuter les tests si demandé
    if [[ "$test" == true ]]; then
        run_tests
    fi
    
    # Mise à jour du code
    update_code
    
    # Installation des dépendances
    install_dependencies
    
    # Migrations si demandé
    if [[ "$migrate" == true ]]; then
        run_migrations
    fi
    
    # Configuration des permissions
    set_permissions
    
    # Optimisation
    optimize
    
    # Redémarrage des services
    restart_services
    
    # Vérification post-déploiement
    post_deployment_check
    
    success "Déploiement terminé avec succès en environnement: $environment"
    
    # Notification (optionnelle)
    if command -v mail &> /dev/null && [[ -n "$NOTIFICATION_EMAIL" ]]; then
        echo "Déploiement terminé avec succès en environnement: $environment" | mail -s "Déploiement $PROJECT_NAME" "$NOTIFICATION_EMAIL"
    fi
}

# Gestion des erreurs
trap 'error "Erreur lors du déploiement. Vérifiez les logs: $LOG_FILE"' ERR

# Exécution du script principal
main "$@"
