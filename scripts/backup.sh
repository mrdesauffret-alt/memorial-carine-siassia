#!/bin/bash

# Script de sauvegarde pour le Mémorial Carine SIASSIA
# Usage: ./backup.sh [options]

set -e

# Configuration
PROJECT_NAME="memorial-carine-siassia"
PROJECT_DIR="/var/www/$PROJECT_NAME"
BACKUP_DIR="/backups/$PROJECT_NAME"
LOG_FILE="/var/log/backup-$PROJECT_NAME.log"

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

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
    echo "Usage: $0 [options]"
    echo ""
    echo "Options:"
    echo "  --full        - Sauvegarde complète (base de données + fichiers)"
    echo "  --db-only     - Sauvegarde de la base de données uniquement"
    echo "  --files-only  - Sauvegarde des fichiers uniquement"
    echo "  --compress    - Compresser les sauvegardes"
    echo "  --encrypt     - Chiffrer les sauvegardes"
    echo "  --cleanup     - Nettoyer les anciennes sauvegardes"
    echo "  --upload      - Uploader vers un stockage distant"
    echo "  --help        - Afficher cette aide"
    echo ""
    echo "Exemples:"
    echo "  $0 --full --compress"
    echo "  $0 --db-only --encrypt"
    echo "  $0 --files-only --cleanup"
}

# Vérification des prérequis
check_prerequisites() {
    log "Vérification des prérequis..."
    
    # Vérifier que le script est exécuté en tant que root ou avec sudo
    if [[ $EUID -ne 0 ]]; then
        error "Ce script doit être exécuté en tant que root ou avec sudo"
    fi
    
    # Vérifier les commandes nécessaires
    local commands=("mysql" "mysqldump" "tar")
    for cmd in "${commands[@]}"; do
        if ! command -v "$cmd" &> /dev/null; then
            error "Commande '$cmd' non trouvée"
        fi
    done
    
    # Vérifier que le dossier du projet existe
    if [[ ! -d "$PROJECT_DIR" ]]; then
        error "Dossier du projet '$PROJECT_DIR' non trouvé"
    fi
    
    # Créer le dossier de sauvegarde s'il n'existe pas
    mkdir -p "$BACKUP_DIR"
    
    success "Prérequis vérifiés"
}

# Chargement de la configuration
load_config() {
    if [[ -f "$PROJECT_DIR/.env" ]]; then
        source "$PROJECT_DIR/.env"
        
        # Vérifier que les variables de base de données sont définies
        if [[ -z "$DB_NAME" || -z "$DB_USER" || -z "$DB_PASS" ]]; then
            error "Configuration de base de données manquante dans .env"
        fi
        
        success "Configuration chargée"
    else
        error "Fichier .env non trouvé dans $PROJECT_DIR"
    fi
}

# Sauvegarde de la base de données
backup_database() {
    log "Sauvegarde de la base de données..."
    
    local timestamp=$(date +%Y%m%d_%H%M%S)
    local db_backup_file="$BACKUP_DIR/database_$timestamp.sql"
    
    # Créer la sauvegarde
    mysqldump -u "$DB_USER" -p"$DB_PASS" \
        --single-transaction \
        --routines \
        --triggers \
        --events \
        --hex-blob \
        --complete-insert \
        "$DB_NAME" > "$db_backup_file"
    
    # Vérifier que la sauvegarde a été créée
    if [[ ! -f "$db_backup_file" || ! -s "$db_backup_file" ]]; then
        error "Échec de la sauvegarde de la base de données"
    fi
    
    # Compresser si demandé
    if [[ "$compress" == true ]]; then
        gzip "$db_backup_file"
        db_backup_file="$db_backup_file.gz"
    fi
    
    # Chiffrer si demandé
    if [[ "$encrypt" == true ]]; then
        if command -v gpg &> /dev/null; then
            gpg --symmetric --cipher-algo AES256 --output "$db_backup_file.gpg" "$db_backup_file"
            rm "$db_backup_file"
            db_backup_file="$db_backup_file.gpg"
        else
            warning "GPG non disponible, chiffrement ignoré"
        fi
    fi
    
    success "Base de données sauvegardée: $db_backup_file"
    echo "$db_backup_file"
}

# Sauvegarde des fichiers
backup_files() {
    log "Sauvegarde des fichiers..."
    
    local timestamp=$(date +%Y%m%d_%H%M%S)
    local files_backup_file="$BACKUP_DIR/files_$timestamp.tar"
    
    # Créer la sauvegarde (exclure certains dossiers)
    tar -cf "$files_backup_file" \
        -C "$PROJECT_DIR" \
        --exclude='.git' \
        --exclude='node_modules' \
        --exclude='vendor' \
        --exclude='uploads' \
        --exclude='backend/logs' \
        --exclude='*.log' \
        --exclude='.env' \
        .
    
    # Vérifier que la sauvegarde a été créée
    if [[ ! -f "$files_backup_file" || ! -s "$files_backup_file" ]]; then
        error "Échec de la sauvegarde des fichiers"
    fi
    
    # Compresser si demandé
    if [[ "$compress" == true ]]; then
        gzip "$files_backup_file"
        files_backup_file="$files_backup_file.gz"
    fi
    
    # Chiffrer si demandé
    if [[ "$encrypt" == true ]]; then
        if command -v gpg &> /dev/null; then
            gpg --symmetric --cipher-algo AES256 --output "$files_backup_file.gpg" "$files_backup_file"
            rm "$files_backup_file"
            files_backup_file="$files_backup_file.gpg"
        else
            warning "GPG non disponible, chiffrement ignoré"
        fi
    fi
    
    success "Fichiers sauvegardés: $files_backup_file"
    echo "$files_backup_file"
}

# Sauvegarde des uploads
backup_uploads() {
    log "Sauvegarde des uploads..."
    
    local timestamp=$(date +%Y%m%d_%H%M%S)
    local uploads_backup_file="$BACKUP_DIR/uploads_$timestamp.tar"
    
    if [[ -d "$PROJECT_DIR/uploads" ]]; then
        tar -cf "$uploads_backup_file" -C "$PROJECT_DIR" uploads/
        
        # Vérifier que la sauvegarde a été créée
        if [[ ! -f "$uploads_backup_file" || ! -s "$uploads_backup_file" ]]; then
            warning "Aucun fichier uploadé à sauvegarder"
            rm -f "$uploads_backup_file"
            return
        fi
        
        # Compresser si demandé
        if [[ "$compress" == true ]]; then
            gzip "$uploads_backup_file"
            uploads_backup_file="$uploads_backup_file.gz"
        fi
        
        # Chiffrer si demandé
        if [[ "$encrypt" == true ]]; then
            if command -v gpg &> /dev/null; then
                gpg --symmetric --cipher-algo AES256 --output "$uploads_backup_file.gpg" "$uploads_backup_file"
                rm "$uploads_backup_file"
                uploads_backup_file="$uploads_backup_file.gpg"
            else
                warning "GPG non disponible, chiffrement ignoré"
            fi
        fi
        
        success "Uploads sauvegardés: $uploads_backup_file"
        echo "$uploads_backup_file"
    else
        warning "Dossier uploads non trouvé"
    fi
}

# Nettoyage des anciennes sauvegardes
cleanup_old_backups() {
    log "Nettoyage des anciennes sauvegardes..."
    
    local days_to_keep=30
    local files_deleted=0
    
    # Nettoyer les sauvegardes de base de données
    find "$BACKUP_DIR" -name "database_*.sql*" -mtime +$days_to_keep -delete
    files_deleted=$((files_deleted + $(find "$BACKUP_DIR" -name "database_*.sql*" -mtime +$days_to_keep | wc -l)))
    
    # Nettoyer les sauvegardes de fichiers
    find "$BACKUP_DIR" -name "files_*.tar*" -mtime +$days_to_keep -delete
    files_deleted=$((files_deleted + $(find "$BACKUP_DIR" -name "files_*.tar*" -mtime +$days_to_keep | wc -l)))
    
    # Nettoyer les sauvegardes d'uploads
    find "$BACKUP_DIR" -name "uploads_*.tar*" -mtime +$days_to_keep -delete
    files_deleted=$((files_deleted + $(find "$BACKUP_DIR" -name "uploads_*.tar*" -mtime +$days_to_keep | wc -l)))
    
    success "$files_deleted anciennes sauvegardes supprimées"
}

# Upload vers un stockage distant
upload_to_remote() {
    log "Upload vers le stockage distant..."
    
    # Configuration pour AWS S3 (exemple)
    if [[ -n "$AWS_ACCESS_KEY_ID" && -n "$AWS_SECRET_ACCESS_KEY" && -n "$S3_BUCKET" ]]; then
        if command -v aws &> /dev/null; then
            local latest_backup=$(ls -t "$BACKUP_DIR"/*.tar* 2>/dev/null | head -1)
            if [[ -n "$latest_backup" ]]; then
                aws s3 cp "$latest_backup" "s3://$S3_BUCKET/backups/"
                success "Sauvegarde uploadée vers S3"
            else
                warning "Aucune sauvegarde récente trouvée pour l'upload"
            fi
        else
            warning "AWS CLI non installé, upload ignoré"
        fi
    else
        warning "Configuration AWS manquante, upload ignoré"
    fi
}

# Vérification de l'intégrité des sauvegardes
verify_backups() {
    log "Vérification de l'intégrité des sauvegardes..."
    
    local backup_files=("$@")
    local all_valid=true
    
    for backup_file in "${backup_files[@]}"; do
        if [[ -n "$backup_file" && -f "$backup_file" ]]; then
            # Vérifier la taille du fichier
            local file_size=$(stat -c%s "$backup_file")
            if [[ $file_size -eq 0 ]]; then
                error "Sauvegarde vide: $backup_file"
                all_valid=false
            fi
            
            # Vérifier l'intégrité selon le type
            if [[ "$backup_file" == *.tar.gz ]]; then
                if ! gzip -t "$backup_file" 2>/dev/null; then
                    error "Sauvegarde corrompue: $backup_file"
                    all_valid=false
                fi
            elif [[ "$backup_file" == *.sql ]]; then
                if ! head -1 "$backup_file" | grep -q "MySQL dump"; then
                    error "Sauvegarde SQL invalide: $backup_file"
                    all_valid=false
                fi
            fi
            
            success "Sauvegarde vérifiée: $backup_file ($file_size bytes)"
        fi
    done
    
    if [[ "$all_valid" == true ]]; then
        success "Toutes les sauvegardes sont valides"
    else
        error "Certaines sauvegardes sont corrompues"
    fi
}

# Génération du rapport
generate_report() {
    log "Génération du rapport de sauvegarde..."
    
    local timestamp=$(date +%Y%m%d_%H%M%S)
    local report_file="$BACKUP_DIR/backup_report_$timestamp.txt"
    
    {
        echo "=== RAPPORT DE SAUVEGARDE ==="
        echo "Date: $(date)"
        echo "Projet: $PROJECT_NAME"
        echo "Environnement: $(hostname)"
        echo ""
        echo "=== SAUVEGARDES CRÉÉES ==="
        ls -la "$BACKUP_DIR"/*_$timestamp.* 2>/dev/null || echo "Aucune sauvegarde créée"
        echo ""
        echo "=== ESPACE DISQUE ==="
        df -h "$BACKUP_DIR"
        echo ""
        echo "=== SAUVEGARDES RÉCENTES ==="
        ls -la "$BACKUP_DIR" | tail -10
    } > "$report_file"
    
    success "Rapport généré: $report_file"
}

# Fonction principale
main() {
    local full_backup=false
    local db_only=false
    local files_only=false
    local compress=false
    local encrypt=false
    local cleanup=false
    local upload=false
    
    # Parsing des arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
            --full)
                full_backup=true
                shift
                ;;
            --db-only)
                db_only=true
                shift
                ;;
            --files-only)
                files_only=true
                shift
                ;;
            --compress)
                compress=true
                shift
                ;;
            --encrypt)
                encrypt=true
                shift
                ;;
            --cleanup)
                cleanup=true
                shift
                ;;
            --upload)
                upload=true
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
    
    # Si aucune option n'est spécifiée, faire une sauvegarde complète
    if [[ "$full_backup" == false && "$db_only" == false && "$files_only" == false ]]; then
        full_backup=true
    fi
    
    log "Début de la sauvegarde"
    
    # Vérifications préliminaires
    check_prerequisites
    load_config
    
    local backup_files=()
    
    # Sauvegarde de la base de données
    if [[ "$full_backup" == true || "$db_only" == true ]]; then
        backup_files+=($(backup_database))
    fi
    
    # Sauvegarde des fichiers
    if [[ "$full_backup" == true || "$files_only" == true ]]; then
        backup_files+=($(backup_files))
        backup_files+=($(backup_uploads))
    fi
    
    # Vérification de l'intégrité
    verify_backups "${backup_files[@]}"
    
    # Nettoyage si demandé
    if [[ "$cleanup" == true ]]; then
        cleanup_old_backups
    fi
    
    # Upload si demandé
    if [[ "$upload" == true ]]; then
        upload_to_remote
    fi
    
    # Génération du rapport
    generate_report
    
    success "Sauvegarde terminée avec succès"
    
    # Notification (optionnelle)
    if command -v mail &> /dev/null && [[ -n "$NOTIFICATION_EMAIL" ]]; then
        echo "Sauvegarde terminée avec succès le $(date)" | mail -s "Sauvegarde $PROJECT_NAME" "$NOTIFICATION_EMAIL"
    fi
}

# Gestion des erreurs
trap 'error "Erreur lors de la sauvegarde. Vérifiez les logs: $LOG_FILE"' ERR

# Exécution du script principal
main "$@"
