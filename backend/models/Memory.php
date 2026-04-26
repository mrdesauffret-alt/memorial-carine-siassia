<?php
/**
 * Modèle pour les souvenirs et témoignages
 */

class Memory {
    private $conn;
    private $table_name = "memories";

    public $id;
    public $type; // 'story', 'photo', 'message'
    public $content;
    public $author_name;
    public $author_email;
    public $image_path;
    public $is_approved;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Créer un nouveau souvenir
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (type, content, author_name, author_email, image_path, is_approved, created_at) 
                  VALUES (:type, :content, :author_name, :author_email, :image_path, :is_approved, NOW())";

        $stmt = $this->conn->prepare($query);

        // Nettoyer les données
        $this->type = htmlspecialchars(strip_tags($this->type));
        $this->content = htmlspecialchars(strip_tags($this->content));
        $this->author_name = htmlspecialchars(strip_tags($this->author_name));
        $this->author_email = filter_var($this->author_email, FILTER_SANITIZE_EMAIL);
        $this->image_path = htmlspecialchars(strip_tags($this->image_path));
        $this->is_approved = $this->is_approved ? 1 : 0;

        // Lier les paramètres
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':author_name', $this->author_name);
        $stmt->bindParam(':author_email', $this->author_email);
        $stmt->bindParam(':image_path', $this->image_path);
        $stmt->bindParam(':is_approved', $this->is_approved);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Récupérer tous les souvenirs approuvés
    public function getApprovedMemories($limit = 50, $offset = 0) {
        $query = "SELECT id, type, content, author_name, image_path, created_at 
                  FROM " . $this->table_name . " 
                  WHERE is_approved = 1 
                  ORDER BY created_at DESC 
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Récupérer tous les souvenirs (admin)
    public function getAllMemories($limit = 100, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  ORDER BY created_at DESC 
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Approuver un souvenir
    public function approve($id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET is_approved = 1, updated_at = NOW() 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // Supprimer un souvenir
    public function delete($id) {
        // Récupérer le chemin de l'image si elle existe
        $memory = $this->getById($id);
        if($memory && $memory['image_path']) {
            $image_path = '../uploads/' . $memory['image_path'];
            if(file_exists($image_path)) {
                unlink($image_path);
            }
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // Récupérer un souvenir par ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Compter les souvenirs
    public function count($approved_only = true) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        if($approved_only) {
            $query .= " WHERE is_approved = 1";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $row = $stmt->fetch();
        return $row['total'];
    }
}
?>
