<?php
/**
 * Gestionnaire d'upload d'images pour les souvenirs
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

// Configuration
$upload_dir = '../uploads/';
$max_file_size = 5 * 1024 * 1024; // 5MB
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Créer le dossier d'upload s'il n'existe pas
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

try {
    // Vérifier si un fichier a été uploadé
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Aucun fichier uploadé ou erreur d\'upload');
    }

    $file = $_FILES['image'];
    
    // Vérifier la taille du fichier
    if ($file['size'] > $max_file_size) {
        throw new Exception('Fichier trop volumineux (max 5MB)');
    }

    // Vérifier le type MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        throw new Exception('Type de fichier non autorisé');
    }

    // Vérifier l'extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowed_extensions)) {
        throw new Exception('Extension de fichier non autorisée');
    }

    // Générer un nom de fichier unique
    $filename = uniqid('memory_', true) . '.' . $extension;
    $filepath = $upload_dir . $filename;

    // Déplacer le fichier
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Erreur lors du déplacement du fichier');
    }

    // Optimiser l'image si nécessaire
    optimizeImage($filepath, $mime_type);

    echo json_encode([
        'success' => true,
        'filename' => $filename,
        'url' => 'uploads/' . $filename,
        'message' => 'Image uploadée avec succès'
    ]);

} catch (Exception $e) {
    error_log("Erreur upload: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}

/**
 * Optimiser l'image pour le web
 */
function optimizeImage($filepath, $mime_type) {
    $max_width = 1200;
    $max_height = 1200;
    $quality = 85;

    switch ($mime_type) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($filepath);
            break;
        case 'image/png':
            $image = imagecreatefrompng($filepath);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($filepath);
            break;
        case 'image/webp':
            $image = imagecreatefromwebp($filepath);
            break;
        default:
            return;
    }

    if (!$image) return;

    $original_width = imagesx($image);
    $original_height = imagesy($image);

    // Calculer les nouvelles dimensions
    $ratio = min($max_width / $original_width, $max_height / $original_height);
    $new_width = (int)($original_width * $ratio);
    $new_height = (int)($original_height * $ratio);

    // Redimensionner seulement si nécessaire
    if ($new_width < $original_width || $new_height < $original_height) {
        $resized = imagecreatetruecolor($new_width, $new_height);
        
        // Préserver la transparence pour PNG et GIF
        if ($mime_type === 'image/png' || $mime_type === 'image/gif') {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
            imagefilledrectangle($resized, 0, 0, $new_width, $new_height, $transparent);
        }

        imagecopyresampled($resized, $image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
        
        // Sauvegarder l'image optimisée
        switch ($mime_type) {
            case 'image/jpeg':
                imagejpeg($resized, $filepath, $quality);
                break;
            case 'image/png':
                imagepng($resized, $filepath, 9);
                break;
            case 'image/gif':
                imagegif($resized, $filepath);
                break;
            case 'image/webp':
                imagewebp($resized, $filepath, $quality);
                break;
        }
        
        imagedestroy($resized);
    }

    imagedestroy($image);
}
?>
