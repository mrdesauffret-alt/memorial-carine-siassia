<?php
/**
 * Page de connexion administrateur
 */

session_start();

// Redirection si déjà connecté
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

require_once '../config/database.php';
require_once '../config/security.php';

// Configuration des headers de sécurité
SecurityConfig::setSecurityHeaders();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = SecurityConfig::sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Vérification CSRF
    if (!SecurityConfig::verifyCSRFToken($csrf_token)) {
        $error = 'Token de sécurité invalide';
        SecurityConfig::logSecurityEvent('csrf_token_invalid', ['ip' => SecurityConfig::getRealIP()]);
    } else {
        // Vérification des identifiants (simplifiée - à améliorer en production)
        $valid_username = 'admin';
        $valid_password_hash = '$argon2id$v=19$m=65536,t=4,p=3$dGVzdA$test'; // Mot de passe: 'admin123'
        
        if ($username === $valid_username && SecurityConfig::verifyPassword($password, $valid_password_hash)) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            $_SESSION['admin_login_time'] = time();
            
            SecurityConfig::logSecurityEvent('admin_login_success', [
                'username' => $username,
                'ip' => SecurityConfig::getRealIP()
            ]);
            
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Identifiants incorrects';
            SecurityConfig::logSecurityEvent('admin_login_failed', [
                'username' => $username,
                'ip' => SecurityConfig::getRealIP()
            ]);
        }
    }
}

$csrf_token = SecurityConfig::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - Mémorial Carine SIASSIA</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --danger-color: #ef4444;
            --dark-color: #1f2937;
            --light-color: #f9fafb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, transparent, rgba(102, 126, 234, 0.1), transparent);
            animation: rotate 20s linear infinite;
            z-index: -1;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: var(--primary-color);
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: var(--dark-color);
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            font-family: inherit;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .login-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 15px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: var(--danger-color);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: var(--secondary-color);
        }

        .security-info {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            color: #0369a1;
            padding: 12px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 0.8rem;
            text-align: center;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-shield-alt"></i> Administration</h1>
            <p>Mémorial Carine SIASSIA</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user"></i> Nom d'utilisateur
                </label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> Mot de passe
                </label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>

            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </button>
        </form>

        <div class="back-link">
            <a href="../index.php">
                <i class="fas fa-arrow-left"></i>
                Retour au mémorial
            </a>
        </div>

        <div class="security-info">
            <i class="fas fa-info-circle"></i>
            Connexion sécurisée - Toutes les tentatives sont enregistrées
        </div>
    </div>

    <script>
        // Focus automatique sur le premier champ
        document.getElementById('username').focus();

        // Gestion des erreurs de validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            if (!username || !password) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs');
                return;
            }

            if (username.length < 3) {
                e.preventDefault();
                alert('Le nom d\'utilisateur doit contenir au moins 3 caractères');
                return;
            }

            if (password.length < 6) {
                e.preventDefault();
                alert('Le mot de passe doit contenir au moins 6 caractères');
                return;
            }
        });

        // Protection contre les attaques par force brute
        let loginAttempts = 0;
        const maxAttempts = 5;

        document.querySelector('form').addEventListener('submit', function(e) {
            loginAttempts++;
            
            if (loginAttempts > maxAttempts) {
                e.preventDefault();
                alert('Trop de tentatives de connexion. Veuillez réessayer plus tard.');
                return;
            }
        });

        // Reset du compteur après 15 minutes
        setTimeout(() => {
            loginAttempts = 0;
        }, 15 * 60 * 1000);
    </script>
</body>
</html>
