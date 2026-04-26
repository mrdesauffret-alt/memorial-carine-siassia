<?php
/**
 * API pour la gestion des bougies virtuelles
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Gérer les requêtes OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';
require_once '../models/Candle.php';

// Initialiser la base de données
try {
    $database = new Database();
    $db = $database->getConnection();
    $candle = new Candle($db);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de connexion à la base de données']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));
$endpoint = $request[0] ?? '';

// Fonction pour obtenir l'IP du visiteur
function getVisitorIP() {
    $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}

try {
    switch ($method) {
        case 'GET':
            if ($endpoint === '') {
                // Récupérer les statistiques des bougies
                $stats = $candle->getStats();
                echo json_encode([
                    'success' => true,
                    'data' => $stats
                ]);
            } elseif ($endpoint === 'recent') {
                // Récupérer les bougies récentes
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
                $recent_candles = $candle->getRecentCandles($limit);
                echo json_encode([
                    'success' => true,
                    'data' => $recent_candles
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Endpoint non trouvé']);
            }
            break;

        case 'POST':
            if ($endpoint === '') {
                // Allumer une bougie
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!$input) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Données JSON invalides']);
                    break;
                }

                $visitor_ip = getVisitorIP();
                $visitor_name = $input['visitor_name'] ?? '';
                $message = $input['message'] ?? '';

                $result = $candle->lightCandle($visitor_ip, $visitor_name, $message);
                
                if ($result['success']) {
                    // Récupérer les nouvelles statistiques
                    $stats = $candle->getStats();
                    $result['stats'] = $stats;
                    
                    echo json_encode($result);
                } else {
                    http_response_code(400);
                    echo json_encode($result);
                }
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Endpoint non trouvé']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
            break;
    }
} catch (Exception $e) {
    error_log("Erreur API candles: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur interne du serveur']);
}
?>
