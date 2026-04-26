#!/bin/bash

# Script de monitoring pour le Mémorial Carine SIASSIA
# Usage: ./monitor.sh [options]

set -e

# Configuration
PROJECT_NAME="memorial-carine-siassia"
PROJECT_DIR="/var/www/$PROJECT_NAME"
LOG_FILE="/var/log/monitor-$PROJECT_NAME.log"
ALERT_EMAIL="admin@memorial-carine-siassia.com"

# Seuils d'alerte
CPU_THRESHOLD=80
MEMORY_THRESHOLD=80
DISK_THRESHOLD=85
LOAD_THRESHOLD=5.0

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
}

# Fonction d'aide
show_help() {
    echo "Usage: $0 [options]"
    echo ""
    echo "Options:"
    echo "  --check-all     - Vérifier tous les services et métriques"
    echo "  --check-services - Vérifier uniquement les services"
    echo "  --check-system   - Vérifier uniquement les métriques système"
    echo "  --check-database - Vérifier uniquement la base de données"
    echo "  --check-website  - Vérifier uniquement le site web"
    echo "  --alert          - Envoyer des alertes par email"
    echo "  --daemon         - Mode démon (surveillance continue)"
    echo "  --help           - Afficher cette aide"
    echo ""
    echo "Exemples:"
    echo "  $0 --check-all"
    echo "  $0 --check-services --alert"
    echo "  $0 --daemon"
}

# Envoi d'alerte par email
send_alert() {
    local subject="$1"
    local message="$2"
    
    if command -v mail &> /dev/null && [[ -n "$ALERT_EMAIL" ]]; then
        echo "$message" | mail -s "$subject" "$ALERT_EMAIL"
        log "Alerte envoyée: $subject"
    else
        warning "Impossible d'envoyer l'alerte par email"
    fi
}

# Vérification des services
check_services() {
    log "Vérification des services..."
    
    local services=("nginx" "php8.1-fpm" "mysql")
    local failed_services=()
    
    for service in "${services[@]}"; do
        if systemctl is-active --quiet "$service"; then
            success "Service $service: ACTIF"
        else
            error "Service $service: INACTIF"
            failed_services+=("$service")
        fi
    done
    
    if [[ ${#failed_services[@]} -gt 0 ]]; then
        local message="Services inactifs détectés: ${failed_services[*]}"
        send_alert "ALERTE: Services inactifs" "$message"
        return 1
    fi
    
    return 0
}

# Vérification des métriques système
check_system_metrics() {
    log "Vérification des métriques système..."
    
    local alerts=()
    
    # CPU
    local cpu_usage=$(top -bn1 | grep "Cpu(s)" | awk '{print $2}' | awk -F'%' '{print $1}')
    if (( $(echo "$cpu_usage > $CPU_THRESHOLD" | bc -l) )); then
        alerts+=("CPU: ${cpu_usage}% (seuil: ${CPU_THRESHOLD}%)")
    else
        success "CPU: ${cpu_usage}%"
    fi
    
    # Mémoire
    local memory_usage=$(free | grep Mem | awk '{printf("%.1f", $3/$2 * 100.0)}')
    if (( $(echo "$memory_usage > $MEMORY_THRESHOLD" | bc -l) )); then
        alerts+=("Mémoire: ${memory_usage}% (seuil: ${MEMORY_THRESHOLD}%)")
    else
        success "Mémoire: ${memory_usage}%"
    fi
    
    # Espace disque
    local disk_usage=$(df -h "$PROJECT_DIR" | awk 'NR==2 {print $5}' | sed 's/%//')
    if [[ $disk_usage -gt $DISK_THRESHOLD ]]; then
        alerts+=("Disque: ${disk_usage}% (seuil: ${DISK_THRESHOLD}%)")
    else
        success "Disque: ${disk_usage}%"
    fi
    
    # Charge système
    local load_avg=$(uptime | awk -F'load average:' '{print $2}' | awk '{print $1}' | sed 's/,//')
    if (( $(echo "$load_avg > $LOAD_THRESHOLD" | bc -l) )); then
        alerts+=("Charge: ${load_avg} (seuil: ${LOAD_THRESHOLD})")
    else
        success "Charge: ${load_avg}"
    fi
    
    # Envoyer des alertes si nécessaire
    if [[ ${#alerts[@]} -gt 0 ]]; then
        local message="Métriques système critiques:\n${alerts[*]}"
        send_alert "ALERTE: Métriques système" "$message"
        return 1
    fi
    
    return 0
}

# Vérification de la base de données
check_database() {
    log "Vérification de la base de données..."
    
    # Charger la configuration
    if [[ -f "$PROJECT_DIR/.env" ]]; then
        source "$PROJECT_DIR/.env"
    else
        error "Fichier .env non trouvé"
        return 1
    fi
    
    # Test de connexion
    if mysql -u "$DB_USER" -p"$DB_PASS" -e "SELECT 1;" "$DB_NAME" &> /dev/null; then
        success "Connexion à la base de données: OK"
    else
        error "Connexion à la base de données: ÉCHEC"
        send_alert "ALERTE: Base de données" "Impossible de se connecter à la base de données"
        return 1
    fi
    
    # Vérification de l'espace disque de la base de données
    local db_size=$(mysql -u "$DB_USER" -p"$DB_PASS" -e "
        SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'DB Size in MB'
        FROM information_schema.tables
        WHERE table_schema='$DB_NAME';" 2>/dev/null | tail -1)
    
    if [[ -n "$db_size" ]]; then
        success "Taille de la base de données: ${db_size} MB"
    fi
    
    # Vérification des processus MySQL
    local mysql_processes=$(mysql -u "$DB_USER" -p"$DB_PASS" -e "SHOW PROCESSLIST;" 2>/dev/null | wc -l)
    if [[ $mysql_processes -gt 100 ]]; then
        warning "Nombre élevé de processus MySQL: $mysql_processes"
    else
        success "Processus MySQL: $mysql_processes"
    fi
    
    return 0
}

# Vérification du site web
check_website() {
    log "Vérification du site web..."
    
    local website_url="http://localhost"
    local api_url="http://localhost/backend/api/memories.php"
    
    # Test de disponibilité du site
    if curl -s -o /dev/null -w "%{http_code}" "$website_url" | grep -q "200"; then
        success "Site web: ACCESSIBLE"
    else
        error "Site web: INACCESSIBLE"
        send_alert "ALERTE: Site web" "Le site web n'est pas accessible"
        return 1
    fi
    
    # Test de l'API
    if curl -s -o /dev/null -w "%{http_code}" "$api_url" | grep -q "200\|404"; then
        success "API: ACCESSIBLE"
    else
        error "API: INACCESSIBLE"
        send_alert "ALERTE: API" "L'API n'est pas accessible"
        return 1
    fi
    
    # Test de temps de réponse
    local response_time=$(curl -s -o /dev/null -w "%{time_total}" "$website_url")
    if (( $(echo "$response_time > 5.0" | bc -l) )); then
        warning "Temps de réponse élevé: ${response_time}s"
    else
        success "Temps de réponse: ${response_time}s"
    fi
    
    return 0
}

# Vérification des logs d'erreur
check_error_logs() {
    log "Vérification des logs d'erreur..."
    
    local error_count=0
    local log_files=(
        "/var/log/nginx/error.log"
        "/var/log/php8.1-fpm.log"
        "$PROJECT_DIR/backend/logs/security.log"
    )
    
    for log_file in "${log_files[@]}"; do
        if [[ -f "$log_file" ]]; then
            # Compter les erreurs des dernières 24h
            local recent_errors=$(find "$log_file" -mtime -1 -exec grep -c "ERROR\|CRITICAL\|FATAL" {} \; 2>/dev/null || echo "0")
            error_count=$((error_count + recent_errors))
            
            if [[ $recent_errors -gt 0 ]]; then
                warning "Erreurs dans $log_file: $recent_errors"
            else
                success "Log $log_file: OK"
            fi
        fi
    done
    
    if [[ $error_count -gt 10 ]]; then
        send_alert "ALERTE: Logs d'erreur" "Nombre élevé d'erreurs détectées: $error_count"
        return 1
    fi
    
    return 0
}

# Vérification de la sécurité
check_security() {
    log "Vérification de la sécurité..."
    
    local security_issues=()
    
    # Vérifier les tentatives de connexion échouées
    if [[ -f "/var/log/auth.log" ]]; then
        local failed_logins=$(grep "Failed password" /var/log/auth.log | grep "$(date +%b\ %d)" | wc -l)
        if [[ $failed_logins -gt 10 ]]; then
            security_issues+=("Tentatives de connexion échouées: $failed_logins")
        fi
    fi
    
    # Vérifier les fichiers sensibles
    local sensitive_files=(
        "$PROJECT_DIR/.env"
        "$PROJECT_DIR/backend/config/database.php"
    )
    
    for file in "${sensitive_files[@]}"; do
        if [[ -f "$file" ]]; then
            local permissions=$(stat -c "%a" "$file")
            if [[ "$permissions" != "644" && "$permissions" != "600" ]]; then
                security_issues+=("Permissions incorrectes pour $file: $permissions")
            fi
        fi
    done
    
    # Vérifier les certificats SSL
    if command -v openssl &> /dev/null; then
        local cert_expiry=$(echo | openssl s_client -servername localhost -connect localhost:443 2>/dev/null | openssl x509 -noout -dates | grep notAfter | cut -d= -f2)
        if [[ -n "$cert_expiry" ]]; then
            local days_until_expiry=$(( ($(date -d "$cert_expiry" +%s) - $(date +%s)) / 86400 ))
            if [[ $days_until_expiry -lt 30 ]]; then
                security_issues+=("Certificat SSL expire dans $days_until_expiry jours")
            fi
        fi
    fi
    
    if [[ ${#security_issues[@]} -gt 0 ]]; then
        local message="Problèmes de sécurité détectés:\n${security_issues[*]}"
        send_alert "ALERTE: Sécurité" "$message"
        return 1
    else
        success "Vérifications de sécurité: OK"
    fi
    
    return 0
}

# Génération du rapport
generate_report() {
    log "Génération du rapport de monitoring..."
    
    local timestamp=$(date +%Y%m%d_%H%M%S)
    local report_file="$PROJECT_DIR/backend/logs/monitoring_report_$timestamp.txt"
    
    {
        echo "=== RAPPORT DE MONITORING ==="
        echo "Date: $(date)"
        echo "Serveur: $(hostname)"
        echo "Projet: $PROJECT_NAME"
        echo ""
        echo "=== SERVICES ==="
        systemctl status nginx --no-pager -l
        echo ""
        systemctl status php8.1-fpm --no-pager -l
        echo ""
        systemctl status mysql --no-pager -l
        echo ""
        echo "=== MÉTRIQUES SYSTÈME ==="
        echo "CPU: $(top -bn1 | grep "Cpu(s)" | awk '{print $2}')"
        echo "Mémoire: $(free -h | grep Mem | awk '{print $3 "/" $2}')"
        echo "Disque: $(df -h $PROJECT_DIR | awk 'NR==2 {print $5}')"
        echo "Charge: $(uptime | awk -F'load average:' '{print $2}')"
        echo ""
        echo "=== CONNECTIVITÉ ==="
        echo "Site web: $(curl -s -o /dev/null -w "%{http_code}" http://localhost)"
        echo "API: $(curl -s -o /dev/null -w "%{http_code}" http://localhost/backend/api/memories.php)"
        echo ""
        echo "=== DERNIÈRES ERREURS ==="
        tail -20 /var/log/nginx/error.log 2>/dev/null || echo "Aucune erreur récente"
    } > "$report_file"
    
    success "Rapport généré: $report_file"
}

# Mode démon
daemon_mode() {
    log "Démarrage du mode démon..."
    
    while true; do
        log "=== Vérification automatique ==="
        
        check_services
        check_system_metrics
        check_database
        check_website
        check_error_logs
        check_security
        
        log "Prochaine vérification dans 5 minutes..."
        sleep 300  # 5 minutes
    done
}

# Fonction principale
main() {
    local check_all=false
    local check_services=false
    local check_system=false
    local check_database=false
    local check_website=false
    local alert=false
    local daemon=false
    
    # Parsing des arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
            --check-all)
                check_all=true
                shift
                ;;
            --check-services)
                check_services=true
                shift
                ;;
            --check-system)
                check_system=true
                shift
                ;;
            --check-database)
                check_database=true
                shift
                ;;
            --check-website)
                check_website=true
                shift
                ;;
            --alert)
                alert=true
                shift
                ;;
            --daemon)
                daemon=true
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
    
    # Si aucune option n'est spécifiée, faire toutes les vérifications
    if [[ "$check_all" == false && "$check_services" == false && "$check_system" == false && "$check_database" == false && "$check_website" == false && "$daemon" == false ]]; then
        check_all=true
    fi
    
    log "Début du monitoring"
    
    # Mode démon
    if [[ "$daemon" == true ]]; then
        daemon_mode
        return
    fi
    
    local exit_code=0
    
    # Vérifications selon les options
    if [[ "$check_all" == true || "$check_services" == true ]]; then
        if ! check_services; then
            exit_code=1
        fi
    fi
    
    if [[ "$check_all" == true || "$check_system" == true ]]; then
        if ! check_system_metrics; then
            exit_code=1
        fi
    fi
    
    if [[ "$check_all" == true || "$check_database" == true ]]; then
        if ! check_database; then
            exit_code=1
        fi
    fi
    
    if [[ "$check_all" == true || "$check_website" == true ]]; then
        if ! check_website; then
            exit_code=1
        fi
    fi
    
    if [[ "$check_all" == true ]]; then
        if ! check_error_logs; then
            exit_code=1
        fi
        
        if ! check_security; then
            exit_code=1
        fi
    fi
    
    # Génération du rapport
    generate_report
    
    if [[ $exit_code -eq 0 ]]; then
        success "Monitoring terminé - Tous les systèmes sont opérationnels"
    else
        error "Monitoring terminé - Des problèmes ont été détectés"
    fi
    
    exit $exit_code
}

# Gestion des erreurs
trap 'error "Erreur lors du monitoring. Vérifiez les logs: $LOG_FILE"' ERR

# Exécution du script principal
main "$@"
