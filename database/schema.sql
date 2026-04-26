-- Base de données pour le Mémorial Carine SIASSIA
-- Création des tables principales

CREATE DATABASE IF NOT EXISTS memorial_carine CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE memorial_carine;

-- Table des souvenirs et témoignages
CREATE TABLE memories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('story', 'photo', 'message') NOT NULL,
    content TEXT NOT NULL,
    author_name VARCHAR(255) DEFAULT '',
    author_email VARCHAR(255) DEFAULT '',
    image_path VARCHAR(500) DEFAULT '',
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (type),
    INDEX idx_approved (is_approved),
    INDEX idx_created (created_at)
);

-- Table des bougies virtuelles
CREATE TABLE candles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_ip VARCHAR(45) NOT NULL,
    visitor_name VARCHAR(255) DEFAULT '',
    message TEXT DEFAULT '',
    lit_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip (visitor_ip),
    INDEX idx_date (lit_at)
);

-- Table des statistiques de visite
CREATE TABLE visit_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_ip VARCHAR(45) NOT NULL,
    user_agent TEXT,
    page_visited VARCHAR(255),
    visit_date DATE NOT NULL,
    visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip (visitor_ip),
    INDEX idx_date (visit_date)
);

-- Table des contacts et messages
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT '',
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_read (is_read),
    INDEX idx_created (created_at)
);

-- Table des événements (pour le programme)
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    event_time TIME,
    location VARCHAR(255),
    address TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_date (event_date),
    INDEX idx_active (is_active)
);

-- Table des photos de la galerie
CREATE TABLE gallery_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    caption TEXT DEFAULT '',
    alt_text VARCHAR(255) DEFAULT '',
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order (display_order),
    INDEX idx_active (is_active)
);

-- Table des témoignages prédéfinis
CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    author_name VARCHAR(255) NOT NULL,
    author_relation VARCHAR(100) DEFAULT '',
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order (display_order),
    INDEX idx_active (is_active)
);

-- Table des paramètres du site
CREATE TABLE site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insérer les données initiales
INSERT INTO events (title, description, event_date, event_time, location, address) VALUES
('Veillée des Souvenirs', 'Venez avec vos photos, vos anecdotes, vos playlists... Faisons de cette soirée un vrai hommage à la femme extraordinaire qu\'elle était !', '2025-08-30', '21:00:00', '12, Rue Jules Guesdes', '91130 Ris-Orangis'),
('Journée d\'Adieu & de Gratitude', 'Un parcours en plusieurs étapes pour dire merci à Carine. Une journée de recueillement, de partage et de célébration de sa belle vie.', '2025-09-01', '09:30:00', 'Parcours Fontainebleau → Melun', 'Maison Funéraire de l\'Hôpital de Fontainebleau');

INSERT INTO testimonials (content, author_name, author_relation, display_order) VALUES
('Carine avait cette capacité unique de transformer chaque moment ordinaire en souvenir extraordinaire. Son rire était contagieux et son cœur, immense.', 'Jane Rose', 'Ami(e)', 1),
('Elle nous a appris que la beauté de la vie réside dans les petites attentions, les gestes tendres et les sourires partagés.', 'Sa famille', 'Famille', 2),
('Carine était une lumière dans nos vies. Même dans les moments difficiles, elle trouvait toujours le moyen de nous faire sourire.', 'Ses amis', 'Ami(e)s', 3);

INSERT INTO site_settings (setting_key, setting_value, description) VALUES
('site_title', 'Mémorial Carine SIASSIA', 'Titre du site'),
('site_description', 'Une Étoile Qui Continue de Briller', 'Description du site'),
('candle_count', '247', 'Nombre initial de bougies allumées'),
('maintenance_mode', '0', 'Mode maintenance (0=actif, 1=maintenance)'),
('max_upload_size', '5242880', 'Taille maximale d\'upload en bytes (5MB)'),
('auto_approve_memories', '0', 'Approbation automatique des souvenirs (0=non, 1=oui)');

-- Créer un utilisateur pour l'API (optionnel, pour la sécurité)
-- CREATE USER 'memorial_api'@'localhost' IDENTIFIED BY 'votre_mot_de_passe_securise';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON memorial_carine.* TO 'memorial_api'@'localhost';
-- FLUSH PRIVILEGES;
