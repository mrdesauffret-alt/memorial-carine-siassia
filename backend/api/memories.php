<?php
/**
 * API pour la gestion des souvenirs
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Gérer les requêtes OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';
require_once '../models/Memory.php';

// Initialiser la base de données
try {
    $database = new Database();
    $db = $database->getConnection();
    $memory = new Memory($db);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de connexion à la base de données']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));
$endpoint = $request[0] ?? '';

try {
    switch ($method) {
        case 'GET':
            if ($endpoint === '') {
                // Récupérer tous les souvenirs approuvés
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
                $offset = ($page - 1) * $limit;
                
                $memories = $memory->getApprovedMemories($limit, $offset);
                $total = $memory->count(true);
                
                echo json_encode([
                    'success' => true,
                    'data' => $memories,
                    'pagination' => [
                        'page' => $page,
                        'limit' => $limit,
                        'total' => $total,
                        'pages' => ceil($total / $limit)
                    ]
                ]);
            } elseif ($endpoint === 'admin') {
                // Récupérer tous les souvenirs (admin)
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
                $offset = ($page - 1) * $limit;
                
                $memories = $memory->getAllMemories($limit, $offset);
                $total = $memory->count(false);
                
                echo json_encode([
                    'success' => true,
                    'data' => $memories,
                    'pagination' => [
                        'page' => $page,
                        'limit' => $limit,
                        'total' => $total,
                        'pages' => ceil($total / $limit)
                    ]
                ]);
            } elseif (is_numeric($endpoint)) {
                // Récupérer un souvenir spécifique
                $memory_data = $memory->getById($endpoint);
                if ($memory_data) {
                    echo json_encode(['success' => true, 'data' => $memory_data]);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Souvenir non trouvé']);
                }
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Endpoint non trouvé']);
            }
            break;

        case 'POST':
            if ($endpoint === '') {
                // Créer un nouveau souvenir
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!$input) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Données JSON invalides']);
                    break;
                }

                // Validation des données
                $required_fields = ['type', 'content'];
                foreach ($required_fields as $field) {
                    if (!isset($input[$field]) || empty(trim($input[$field]))) {
                        http_response_code(400);
                        echo json_encode(['error' => "Le champ '$field' est requis"]);
                        exit();
                    }
                }

                // Assigner les données
                $memory->type = $input['type'];
                $memory->content = $input['content'];
                $memory->author_name = $input['author_name'] ?? '';
                $memory->author_email = $input['author_email'] ?? '';
                $memory->image_path = $input['image_path'] ?? '';
                $memory->is_approved = false; // Nécessite modération

                $id = $memory->create();
                if ($id) {
                    http_response_code(201);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Souvenir créé avec succès',
                        'id' => $id
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur lors de la création du souvenir']);
                }
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Endpoint non trouvé']);
            }
            break;

        case 'PUT':
            if (is_numeric($endpoint)) {
                // Mettre à jour un souvenir (admin)
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!$input) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Données JSON invalides']);
                    break;
                }

                // Vérifier si le souvenir existe
                $existing_memory = $memory->getById($endpoint);
                if (!$existing_memory) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Souvenir non trouvé']);
                    break;
                }

                // Mettre à jour les champs fournis
                if (isset($input['is_approved'])) {
                    $memory->approve($endpoint);
                }

                echo json_encode(['success' => true, 'message' => 'Souvenir mis à jour']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Endpoint non trouvé']);
            }
            break;

        case 'DELETE':
            if (is_numeric($endpoint)) {
                // Supprimer un souvenir (admin)
                if ($memory->delete($endpoint)) {
                    echo json_encode(['success' => true, 'message' => 'Souvenir supprimé']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur lors de la suppression']);
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
    error_log("Erreur API memories: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur interne du serveur']);
}
?>
