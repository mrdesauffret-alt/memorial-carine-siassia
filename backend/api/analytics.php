<?php
/**
 * API pour la collecte des données d'analytics
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Gérer les requêtes OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit();
}

require_once '../config/database.php';

// Initialiser la base de données
try {
    $database = new Database();
    $db = $database->getConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de connexion à la base de données']);
    exit();
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['events'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Données invalides']);
        exit();
    }

    $events = $input['events'];
    $processed = 0;
    $errors = [];

    foreach ($events as $event) {
        try {
            // Valider l'événement
            if (!isset($event['name']) || !isset($event['timestamp'])) {
                $errors[] = 'Événement invalide: ' . json_encode($event);
                continue;
            }

            // Insérer l'événement dans la base de données
            $query = "INSERT INTO analytics_events 
                      (event_name, event_data, session_id, user_agent, url, created_at) 
                      VALUES (:name, :data, :session_id, :user_agent, :url, :created_at)";

            $stmt = $db->prepare($query);
            $stmt->bindParam(':name', $event['name']);
            $stmt->bindParam(':data', json_encode($event['data'] ?? []));
            $stmt->bindParam(':session_id', $event['session_id'] ?? '');
            $stmt->bindParam(':user_agent', $event['user_agent'] ?? '');
            $stmt->bindParam(':url', $event['url'] ?? '');
            $stmt->bindParam(':created_at', $event['timestamp']);

            if ($stmt->execute()) {
                $processed++;
            } else {
                $errors[] = 'Erreur lors de l\'insertion de l\'événement: ' . $event['name'];
            }

        } catch (Exception $e) {
            $errors[] = 'Erreur lors du traitement de l\'événement: ' . $e->getMessage();
        }
    }

    // Réponse
    $response = [
        'success' => true,
        'processed' => $processed,
        'total' => count($events),
        'errors' => $errors
    ];

    if (!empty($errors)) {
        $response['warning'] = 'Certains événements n\'ont pas pu être traités';
    }

    echo json_encode($response);

} catch (Exception $e) {
    error_log("Erreur API analytics: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur interne du serveur']);
}
?>
