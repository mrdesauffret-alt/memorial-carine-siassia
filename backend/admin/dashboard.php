<?php
/**
 * Tableau de bord administrateur
 */

session_start();

// Vérification de l'authentification (simplifiée)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
} catch (Exception $e) {
    die('Erreur de connexion à la base de données');
}

// Récupérer les statistiques
$stats = [];

// Statistiques des souvenirs
$query = "SELECT 
            COUNT(*) as total_memories,
            COUNT(CASE WHEN is_approved = 1 THEN 1 END) as approved_memories,
            COUNT(CASE WHEN is_approved = 0 THEN 1 END) as pending_memories,
            COUNT(CASE WHEN type = 'story' THEN 1 END) as stories,
            COUNT(CASE WHEN type = 'photo' THEN 1 END) as photos,
            COUNT(CASE WHEN type = 'message' THEN 1 END) as messages
          FROM memories";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['memories'] = $stmt->fetch();

// Statistiques des bougies
$query = "SELECT 
            COUNT(*) as total_candles,
            COUNT(DISTINCT visitor_ip) as unique_visitors,
            COUNT(CASE WHEN DATE(lit_at) = CURDATE() THEN 1 END) as today_candles,
            COUNT(CASE WHEN DATE(lit_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as week_candles
          FROM candles";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['candles'] = $stmt->fetch();

// Statistiques des visites
$query = "SELECT 
            COUNT(*) as total_visits,
            COUNT(DISTINCT visitor_ip) as unique_visitors,
            COUNT(CASE WHEN DATE(visit_date) = CURDATE() THEN 1 END) as today_visits
          FROM visit_stats";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['visits'] = $stmt->fetch();

// Récupérer les souvenirs en attente
$query = "SELECT * FROM memories WHERE is_approved = 0 ORDER BY created_at DESC LIMIT 10";
$stmt = $db->prepare($query);
$stmt->execute();
$pending_memories = $stmt->fetchAll();

// Récupérer les bougies récentes
$query = "SELECT * FROM candles ORDER BY lit_at DESC LIMIT 10";
$stmt = $db->prepare($query);
$stmt->execute();
$recent_candles = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Mémorial Carine SIASSIA</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #10b981;
            --warning-color: #f59e0b;
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
            background: var(--light-color);
            color: var(--dark-color);
            line-height: 1.6;
        }

        .header {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: var(--primary-color);
            font-size: 1.5rem;
        }

        .logout-btn {
            background: var(--danger-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-left: 4px solid var(--primary-color);
        }

        .stat-card.success {
            border-left-color: var(--success-color);
        }

        .stat-card.warning {
            border-left-color: var(--warning-color);
        }

        .stat-card.danger {
            border-left-color: var(--danger-color);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-title {
            font-size: 0.9rem;
            color: #6b7280;
            font-weight: 500;
        }

        .stat-icon {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
        }

        .stat-change {
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }

        .stat-change.positive {
            color: var(--success-color);
        }

        .stat-change.negative {
            color: var(--danger-color);
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .content-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .content-card h2 {
            margin-bottom: 1rem;
            color: var(--dark-color);
            font-size: 1.2rem;
        }

        .memory-item, .candle-item {
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .memory-item:last-child, .candle-item:last-child {
            margin-bottom: 0;
        }

        .memory-header, .candle-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .memory-type, .candle-time {
            font-size: 0.8rem;
            color: #6b7280;
        }

        .memory-content, .candle-message {
            margin-bottom: 0.5rem;
        }

        .memory-author, .candle-visitor {
            font-size: 0.9rem;
            color: var(--primary-color);
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .btn {
            padding: 0.25rem 0.75rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .btn-success {
            background: var(--success-color);
            color: white;
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .empty-state {
            text-align: center;
            color: #6b7280;
            padding: 2rem;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .container {
                padding: 0 1rem;
            }
            
            .header {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-tachometer-alt"></i> Tableau de bord</h1>
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            Déconnexion
        </a>
    </div>

    <div class="container">
        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Total Souvenirs</span>
                    <i class="fas fa-heart stat-icon"></i>
                </div>
                <div class="stat-value"><?= $stats['memories']['total_memories'] ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> +<?= $stats['memories']['approved_memories'] ?> approuvés
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-header">
                    <span class="stat-title">En Attente</span>
                    <i class="fas fa-clock stat-icon"></i>
                </div>
                <div class="stat-value"><?= $stats['memories']['pending_memories'] ?></div>
                <div class="stat-change">
                    Souvenirs à modérer
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-header">
                    <span class="stat-title">Bougies Allumées</span>
                    <i class="fas fa-fire stat-icon"></i>
                </div>
                <div class="stat-value"><?= $stats['candles']['total_candles'] ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> +<?= $stats['candles']['today_candles'] ?> aujourd'hui
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Visiteurs Uniques</span>
                    <i class="fas fa-users stat-icon"></i>
                </div>
                <div class="stat-value"><?= $stats['candles']['unique_visitors'] ?></div>
                <div class="stat-change">
                    <?= $stats['visits']['today_visits'] ?> visites aujourd'hui
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="content-grid">
            <!-- Souvenirs en attente -->
            <div class="content-card">
                <h2><i class="fas fa-clock"></i> Souvenirs en attente</h2>
                <?php if (empty($pending_memories)): ?>
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <p>Aucun souvenir en attente</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($pending_memories as $memory): ?>
                        <div class="memory-item">
                            <div class="memory-header">
                                <span class="memory-type">
                                    <i class="fas fa-<?= $memory['type'] === 'photo' ? 'camera' : ($memory['type'] === 'story' ? 'book' : 'envelope') ?>"></i>
                                    <?= ucfirst($memory['type']) ?>
                                </span>
                                <span class="memory-date"><?= date('d/m/Y H:i', strtotime($memory['created_at'])) ?></span>
                            </div>
                            <div class="memory-content">
                                <?= htmlspecialchars(substr($memory['content'], 0, 100)) ?>
                                <?php if (strlen($memory['content']) > 100): ?>...<?php endif; ?>
                            </div>
                            <div class="memory-author">
                                Par <?= htmlspecialchars($memory['author_name'] ?: 'Anonyme') ?>
                            </div>
                            <div class="action-buttons">
                                <a href="approve_memory.php?id=<?= $memory['id'] ?>" class="btn btn-success">
                                    <i class="fas fa-check"></i> Approuver
                                </a>
                                <a href="delete_memory.php?id=<?= $memory['id'] ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr ?')">
                                    <i class="fas fa-trash"></i> Supprimer
                                </a>
                                <a href="view_memory.php?id=<?= $memory['id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Bougies récentes -->
            <div class="content-card">
                <h2><i class="fas fa-fire"></i> Bougies récentes</h2>
                <?php if (empty($recent_candles)): ?>
                    <div class="empty-state">
                        <i class="fas fa-fire"></i>
                        <p>Aucune bougie allumée</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($recent_candles as $candle): ?>
                        <div class="candle-item">
                            <div class="candle-header">
                                <span class="candle-visitor">
                                    <?= htmlspecialchars($candle['visitor_name'] ?: 'Anonyme') ?>
                                </span>
                                <span class="candle-time">
                                    <?= date('d/m/Y H:i', strtotime($candle['lit_at'])) ?>
                                </span>
                            </div>
                            <?php if ($candle['message']): ?>
                                <div class="candle-message">
                                    "<?= htmlspecialchars($candle['message']) ?>"
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh toutes les 30 secondes
        setInterval(() => {
            location.reload();
        }, 30000);

        // Confirmation pour les actions destructives
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-danger')) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                    e.preventDefault();
                }
            }
        });
    </script>
</body>
</html>
