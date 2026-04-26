<?php
/**
 * Configuration de sécurité pour le mémorial
 */

class SecurityConfig {
    // Headers de sécurité
    public static function setSecurityHeaders() {
        // Protection XSS
        header('X-XSS-Protection: 1; mode=block');
        
        // Protection contre le clickjacking
        header('X-Frame-Options: SAMEORIGIN');
        
        // Protection contre le MIME sniffing
        header('X-Content-Type-Options: nosniff');
        
        // Politique de référent
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Content Security Policy
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://www.googletagmanager.com https://www.google-analytics.com https://static.hotjar.com; " .
               "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; " .
               "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; " .
               "img-src 'self' data: https:; " .
               "connect-src 'self' https://www.google-analytics.com https://api.hotjar.com https://browser.sentry-cdn.com; " .
               "frame-src 'none'; " .
               "object-src 'none'; " .
               "base-uri 'self'; " .
               "form-action 'self';";
        
        header("Content-Security-Policy: $csp");
        
        // Permissions Policy
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    }
    
    // Validation des entrées
    public static function sanitizeInput($input, $type = 'string') {
        switch ($type) {
            case 'email':
                return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
            case 'int':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            case 'url':
                return filter_var($input, FILTER_SANITIZE_URL);
            case 'string':
            default:
                return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
    }
    
    // Validation des emails
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    // Validation des URLs
    public static function validateUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    // Génération de token CSRF
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    // Vérification du token CSRF
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    // Limitation de taux
    public static function checkRateLimit($key, $limit, $window = 3600) {
        $file = sys_get_temp_dir() . '/rate_limit_' . md5($key);
        $now = time();
        
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            
            // Nettoyer les entrées anciennes
            $data = array_filter($data, function($timestamp) use ($now, $window) {
                return ($now - $timestamp) < $window;
            });
            
            if (count($data) >= $limit) {
                return false;
            }
        } else {
            $data = [];
        }
        
        // Ajouter la nouvelle entrée
        $data[] = $now;
        file_put_contents($file, json_encode($data));
        
        return true;
    }
    
    // Chiffrement de données sensibles
    public static function encrypt($data, $key) {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }
    
    // Déchiffrement de données sensibles
    public static function decrypt($data, $key) {
        $data = base64_decode($data);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
    }
    
    // Hachage sécurisé des mots de passe
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
    }
    
    // Vérification du mot de passe
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    // Génération de mot de passe sécurisé
    public static function generateSecurePassword($length = 16) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        return substr(str_shuffle($chars), 0, $length);
    }
    
    // Validation des fichiers uploadés
    public static function validateUploadedFile($file, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'], $maxSize = 5242880) {
        $errors = [];
        
        // Vérifier les erreurs d'upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Erreur lors de l\'upload du fichier';
            return $errors;
        }
        
        // Vérifier la taille
        if ($file['size'] > $maxSize) {
            $errors[] = 'Fichier trop volumineux (max ' . ($maxSize / 1024 / 1024) . 'MB)';
        }
        
        // Vérifier le type MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowedMimes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp'
        ];
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowedTypes) || !isset($allowedMimes[$extension]) || $mimeType !== $allowedMimes[$extension]) {
            $errors[] = 'Type de fichier non autorisé';
        }
        
        // Vérifier le contenu du fichier
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            $errors[] = 'Fichier image invalide';
        }
        
        return $errors;
    }
    
    // Nettoyage des noms de fichiers
    public static function sanitizeFilename($filename) {
        // Supprimer les caractères dangereux
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        
        // Limiter la longueur
        $filename = substr($filename, 0, 100);
        
        // Ajouter un préfixe unique
        return uniqid('memorial_', true) . '_' . $filename;
    }
    
    // Logging des événements de sécurité
    public static function logSecurityEvent($event, $details = []) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'event' => $event,
            'details' => $details
        ];
        
        $logFile = __DIR__ . '/../logs/security.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
    }
    
    // Détection d'attaques
    public static function detectAttack($input) {
        $patterns = [
            '/<script[^>]*>.*?<\/script>/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/union\s+select/i',
            '/drop\s+table/i',
            '/delete\s+from/i',
            '/insert\s+into/i',
            '/update\s+set/i',
            '/exec\s*\(/i',
            '/eval\s*\(/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                self::logSecurityEvent('attack_detected', [
                    'pattern' => $pattern,
                    'input' => substr($input, 0, 200)
                ]);
                return true;
            }
        }
        
        return false;
    }
    
    // Validation de l'IP
    public static function validateIP($ip) {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }
    
    // Obtention de l'IP réelle du client
    public static function getRealIP() {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (self::validateIP($ip)) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
    
    // Génération de clé API
    public static function generateAPIKey() {
        return 'memorial_' . bin2hex(random_bytes(16));
    }
    
    // Validation de clé API
    public static function validateAPIKey($key) {
        return preg_match('/^memorial_[a-f0-9]{32}$/', $key);
    }
    
    // Nettoyage des logs anciens
    public static function cleanOldLogs($days = 30) {
        $logDir = __DIR__ . '/../logs/';
        $cutoff = time() - ($days * 24 * 60 * 60);
        
        if (is_dir($logDir)) {
            $files = glob($logDir . '*.log');
            foreach ($files as $file) {
                if (filemtime($file) < $cutoff) {
                    unlink($file);
                }
            }
        }
    }
}
?>
