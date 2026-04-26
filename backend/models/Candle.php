<?php
/**
 * Modèle pour les bougies virtuelles
 */

class Candle {
    private $conn;
    private $table_name = "candles";

    public $id;
    public $visitor_ip;
    public $visitor_name;
    public $message;
    public $lit_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Allumer une bougie
    public function lightCandle($visitor_ip, $visitor_name = '', $message = '') {
        // Vérifier si cette IP a déjà allumé une bougie aujourd'hui
        $today = date('Y-m-d');
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE visitor_ip = :ip AND DATE(lit_at) = :today";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ip', $visitor_ip);
        $stmt->bindParam(':today', $today);
        $stmt->execute();
        
        $result = $stmt->fetch();
        if($result['count'] > 0) {
            return ['success' => false, 'message' => 'Vous avez déjà allumé une bougie aujourd\'hui'];
        }

        // Allumer la bougie
        $query = "INSERT INTO " . $this->table_name . " 
                  (visitor_ip, visitor_name, message, lit_at) 
                  VALUES (:ip, :name, :message, NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ip', $visitor_ip);
        $stmt->bindParam(':name', $visitor_name);
        $stmt->bindParam(':message', $message);

        if($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->lastInsertId()];
        }
        
        return ['success' => false, 'message' => 'Erreur lors de l\'allumage de la bougie'];
    }

    // Récupérer le nombre total de bougies
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Récupérer les bougies récentes
    public function getRecentCandles($limit = 20) {
        $query = "SELECT visitor_name, message, lit_at 
                  FROM " . $this->table_name . " 
                  WHERE visitor_name IS NOT NULL AND visitor_name != '' 
                  ORDER BY lit_at DESC 
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Statistiques des bougies
    public function getStats() {
        $query = "SELECT 
                    COUNT(*) as total_candles,
                    COUNT(DISTINCT visitor_ip) as unique_visitors,
                    COUNT(CASE WHEN DATE(lit_at) = CURDATE() THEN 1 END) as today_candles,
                    COUNT(CASE WHEN DATE(lit_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as week_candles
                  FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch();
    }
}
?>
