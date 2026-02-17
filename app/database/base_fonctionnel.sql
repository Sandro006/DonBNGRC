-- =========================
-- BASE DE DONNÉES FONCTIONNELLE COMPLÈTE - BNGRC
-- Système de gestion des dons globaux et des besoins par ville
-- Version: Post-migration (dons globaux uniquement)
-- =========================

DROP DATABASE IF EXISTS bngrc;
CREATE DATABASE IF NOT EXISTS bngrc;
USE bngrc;

-- =========================
-- TABLE REGION
-- =========================
CREATE TABLE bngrc_region (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =========================
-- TABLE VILLE
-- =========================
CREATE TABLE bngrc_ville (
    id INT AUTO_INCREMENT PRIMARY KEY,
    region_id INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    nombre_sinistres INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_ville_region
        FOREIGN KEY (region_id)
        REFERENCES bngrc_region(id)
        ON DELETE CASCADE,
    
    INDEX idx_ville_region (region_id),
    INDEX idx_ville_nom (nom)
);

-- =========================
-- TABLE CATEGORIE
-- (nature, materiaux, argent)
-- =========================
CREATE TABLE bngrc_categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_categorie_libelle (libelle)
);

-- =========================
-- TABLE STATUS
-- =========================
CREATE TABLE bngrc_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =========================
-- TABLE DONATEUR
-- =========================
CREATE TABLE bngrc_donateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    telephone VARCHAR(20),
    email VARCHAR(100),
    adresse TEXT,
    type_donateur ENUM('particulier', 'entreprise', 'association', 'ong') DEFAULT 'particulier',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_donateur_nom (nom),
    INDEX idx_donateur_email (email),
    INDEX idx_donateur_type (type_donateur)
);

-- =========================
-- TABLE BESOIN
-- =========================
CREATE TABLE bngrc_besoin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville_id INT NOT NULL,
    categorie_id INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    status_id INT DEFAULT 1,
    date_besoin DATETIME DEFAULT CURRENT_TIMESTAMP,
    priorite ENUM('basse', 'normale', 'haute', 'urgente') DEFAULT 'normale',
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_besoin_ville
        FOREIGN KEY (ville_id)
        REFERENCES bngrc_ville(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_besoin_categorie
        FOREIGN KEY (categorie_id)
        REFERENCES bngrc_categorie(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_besoin_status
        FOREIGN KEY (status_id)
        REFERENCES bngrc_status(id)
        ON DELETE RESTRICT,
        
    -- Index pour optimiser les requêtes de simulation
    INDEX idx_besoin_date (date_besoin),
    INDEX idx_besoin_status (status_id),
    INDEX idx_besoin_simulation (categorie_id, status_id, date_besoin),
    INDEX idx_besoin_ville (ville_id),
    INDEX idx_besoin_priorite (priorite)
);

-- =========================
-- TABLE DON GLOBAL
-- (Dons qui ne sont pas liés à une ville spécifique - système principal)
-- =========================
CREATE TABLE bngrc_don_global (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categorie_id INT NOT NULL,
    donateur_id INT NOT NULL,
    date_don DATETIME DEFAULT CURRENT_TIMESTAMP,
    quantite INT NOT NULL,
    status_distribution ENUM('disponible', 'distribue', 'reserve', 'expire') DEFAULT 'disponible',
    valeur_unitaire DECIMAL(10,2),
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_don_global_categorie
        FOREIGN KEY (categorie_id)
        REFERENCES bngrc_categorie(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_don_global_donateur
        FOREIGN KEY (donateur_id)
        REFERENCES bngrc_donateur(id)
        ON DELETE RESTRICT,
        
    -- Index sur le statut de distribution des dons globaux
    INDEX idx_don_global_status (status_distribution),
    INDEX idx_don_global_categorie (categorie_id),
    INDEX idx_don_global_donateur (donateur_id),
    INDEX idx_don_global_date (date_don),
    -- Index composite pour les dons disponibles par catégorie
    INDEX idx_don_global_disponible (categorie_id, status_distribution, date_don)
);

-- =========================
-- TABLE DISTRIBUTION
-- (Pour tracer les distributions des dons globaux vers les besoins)
-- =========================
CREATE TABLE bngrc_distribution (
    id INT AUTO_INCREMENT PRIMARY KEY,
    don_global_id INT NOT NULL,
    besoin_id INT NOT NULL,
    quantite_distribuee INT NOT NULL,
    date_distribution DATETIME DEFAULT CURRENT_TIMESTAMP,
    methode_distribution ENUM('automatique', 'manuelle', 'prioritaire') DEFAULT 'automatique',
    responsable VARCHAR(100),
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_distribution_don_global
        FOREIGN KEY (don_global_id)
        REFERENCES bngrc_don_global(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_distribution_besoin
        FOREIGN KEY (besoin_id)
        REFERENCES bngrc_besoin(id)
        ON DELETE RESTRICT,
        
    -- Index pour les distributions
    INDEX idx_distribution_don (don_global_id),
    INDEX idx_distribution_besoin (besoin_id),
    INDEX idx_distribution_date (date_distribution),
    INDEX idx_distribution_methode (methode_distribution)
);

-- =========================
-- TABLE 55
-- =========================
CREATE TABLE bngrc_achat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville_id INT NOT NULL,
    besoin_id INT NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    frais_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
    montant_total DECIMAL(10,2) NOT NULL,
    date_achat DATETIME DEFAULT CURRENT_TIMESTAMP,
    fournisseur VARCHAR(150),
    bon_commande VARCHAR(50),
    statut_achat ENUM('en_attente', 'commande', 'livre', 'annule') DEFAULT 'en_attente',
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_achat_ville 
        FOREIGN KEY (ville_id) 
        REFERENCES bngrc_ville(id)
        ON DELETE RESTRICT,
        
    CONSTRAINT fk_achat_besoin 
        FOREIGN KEY (besoin_id) 
        REFERENCES bngrc_besoin(id)
        ON DELETE RESTRICT,
        
    INDEX idx_achat_ville (ville_id),
    INDEX idx_achat_besoin (besoin_id),
    INDEX idx_achat_date (date_achat),
    INDEX idx_achat_statut (statut_achat)
);

-- =========================
-- DONNÉES DE BASE POUR LE FONCTIONNEMENT
-- =========================

-- ===== RÉGIONS =====
INSERT INTO bngrc_region (nom) VALUES
('Analamanga'),
('Diana'),
('Sava'),
('Itasy'),
('Bongolava'),
('Haute Matsiatra'),
('Amoron''i Mania'),
('Vatovavy-Fitovinany'),
('Ihorombe'),
('Atsimo-Atsinanana'),
('Atsinanana'),
('Analanjirofo'),
('Alaotra-Mangoro'),
('Boeny'),
('Sofia'),
('Betsiboka'),
('Melaky'),
('Atsimo-Andrefana'),
('Androy'),
('Anosy'),
('Menabe'),
('Vakinankaratra');

-- ===== VILLES =====
INSERT INTO bngrc_ville (region_id, nom, nombre_sinistres) VALUES
(1, 'Antananarivo', 3),
(1, 'Ambohidratrimo', 1),
(2, 'Antsiranana', 5),
(3, 'Sambava', 4),
(4, 'Miarinarivo', 2),
(5, 'Tsiroanomandidy', 3),
(6, 'Fianarantsoa', 4),
(7, 'Ambositra', 2),
(8, 'Manakara', 6),
(9, 'Ihosy', 3),
(10, 'Farafangana', 5),
(11, 'Toamasina', 7),
(12, 'Maroantsetra', 4),
(13, 'Ambatondrazaka', 2),
(14, 'Mahajanga', 6),
(15, 'Antsohihy', 3),
(16, 'Kandreho', 1),
(17, 'Besalampy', 2),
(18, 'Toliara', 8),
(19, 'Ambovombe', 5),
(20, 'Tôlanaro', 4),
(21, 'Morondava', 5),
(22, 'Antsirabe', 3);

-- ===== CATÉGORIES =====
INSERT INTO bngrc_categorie (libelle, description) VALUES
('Alimentation', 'Denrées alimentaires de première nécessité'),
('Matériaux', 'Matériaux de construction et équipements'),
('Argent', 'Dons financiers pour achats urgents'),
('Vêtements', 'Habits et textiles'),
('Médicaments', 'Produits pharmaceutiques et matériel médical'),
('Eau et Assainissement', 'Équipements pour accès à l''eau potable'),
('Éducation', 'Matériel scolaire et éducatif'),
('Électronique', 'Matériel électronique et de communication');

-- ===== STATUS =====
INSERT INTO bngrc_status (libelle, description) VALUES
('En attente', 'Besoin enregistré mais pas encore traité'),
('En cours', 'Besoin en cours de traitement'),
('Satisfait', 'Besoin complètement couvert'),
('Partiellement satisfait', 'Besoin partiellement couvert'),
('Annulé', 'Besoin annulé ou plus d''actualité'),
('Urgent', 'Besoin urgent nécessitant une action immédiate');

-- ===== DONATEURS D'EXEMPLE =====
INSERT INTO bngrc_donateur (nom, telephone, email, type_donateur) VALUES
('Jean Dupont', '06 12 34 56 78', 'jean.dupont@email.com', 'particulier'),
('Marie Martin', '06 23 45 67 89', 'marie.martin@email.com', 'particulier'),
('Association Solidarité Madagascar', '01 23 45 67 89', 'contact@solidarite-mg.fr', 'association'),
('Entreprise BTP Malagasy', '01 34 56 78 90', 'rh@btp-malagasy.mg', 'entreprise'),
('ONG Aide Humanitaire International', '01 45 67 89 01', 'contact@ahi.org', 'ong'),
('Sophie Laurent', '06 56 78 90 12', 'sophie.laurent@email.com', 'particulier'),
('Fondation Développement Rural', '01 56 78 90 12', 'contact@fdr.mg', 'association'),
('Coopérative Agricole du Sud', '02 67 89 01 23', 'admin@coop-sud.mg', 'association');

-- ===== BESOINS D'EXEMPLE =====
INSERT INTO bngrc_besoin (ville_id, categorie_id, quantite, prix_unitaire, status_id, priorite, description) VALUES
-- Antananarivo
(1, 1, 500, 2500, 1, 'haute', 'Riz et légumes secs pour 200 familles sinistrées'),
(1, 2, 50, 150000, 1, 'normale', 'Tôles ondulées pour reconstruction'),
(1, 5, 100, 5000, 6, 'urgente', 'Médicaments de première urgence'),

-- Antsiranana  
(3, 1, 1000, 2000, 1, 'haute', 'Alimentation d''urgence post-cyclone'),
(3, 6, 20, 250000, 1, 'normale', 'Pompes à eau et systèmes de purification'),

-- Toamasina
(12, 1, 800, 1800, 1, 'haute', 'Vivres pour familles évacuées'),
(12, 2, 100, 200000, 2, 'normale', 'Matériaux pour abris temporaires'),
(12, 4, 300, 15000, 1, 'normale', 'Vêtements pour enfants et adultes'),

-- Mahajanga
(15, 1, 600, 2200, 1, 'haute', 'Denrées non-périssables'),
(15, 3, 1, 5000000, 1, 'urgente', 'Fonds d''urgence pour achats immédiats'),

-- Toliara
(19, 6, 15, 300000, 1, 'urgente', 'Systèmes d''adduction d''eau d''urgence'),
(19, 1, 1200, 1500, 1, 'haute', 'Alimentation pour zone affectée par sécheresse'),

-- Fianarantsoa
(7, 2, 80, 180000, 1, 'normale', 'Matériaux de reconstruction post-inondations'),
(7, 7, 200, 25000, 2, 'basse', 'Fournitures scolaires de remplacement');

-- ===== DONS GLOBAUX D'EXEMPLE =====
INSERT INTO bngrc_don_global (categorie_id, donateur_id, quantite, status_distribution, valeur_unitaire, notes) VALUES
-- Alimentation
(1, 1, 200, 'disponible', 2500, 'Riz de qualité supérieure'),
(1, 3, 500, 'disponible', 2000, 'Mélange riz, haricots, huile'),
(1, 5, 300, 'disponible', 1800, 'Conserves et denrées non-périssables'),
(1, 7, 800, 'disponible', 2200, 'Kit alimentaire famille 1 mois'),

-- Matériaux
(2, 4, 100, 'disponible', 150000, 'Tôles galvanisées neuves'),
(2, 4, 50, 'disponible', 200000, 'Kit construction abri d''urgence'),
(2, 8, 75, 'disponible', 180000, 'Matériaux divers construction'),

-- Argent
(3, 2, 1, 'disponible', 2000000, 'Don financier libre usage'),
(3, 6, 1, 'disponible', 5000000, 'Fonds d''urgence catastrophe naturelle'),
(3, 5, 1, 'disponible', 3000000, 'Aide financière reconstruction'),

-- Vêtements
(4, 1, 150, 'disponible', 15000, 'Vêtements chauds adultes'),
(4, 3, 200, 'disponible', 12000, 'Habits enfants 5-15 ans'),

-- Médicaments
(5, 5, 50, 'disponible', 8000, 'Kit médical de base'),
(5, 5, 20, 'reserve', 25000, 'Médicaments spécialisés'),

-- Eau et assainissement
(6, 4, 10, 'disponible', 250000, 'Pompe manuelle résistante'),
(6, 8, 25, 'disponible', 50000, 'Kit purification eau familial'),

-- Dons déjà distribués (historique)
(1, 2, 300, 'distribue', 2300, 'Déjà distribué à Antananarivo'),
(2, 4, 30, 'distribue', 160000, 'Matériaux envoyés à Toamasina');

-- =========================
-- VUES UTILES POUR LES RAPPORTS
-- =========================

CREATE VIEW vue_dons_disponibles AS
SELECT 
    dg.id,
    dg.quantite,
    dg.date_don,
    dg.valeur_unitaire,
    dg.notes,
    c.libelle as categorie,
    d.nom as donateur,
    d.type_donateur
FROM bngrc_don_global dg
INNER JOIN bngrc_categorie c ON dg.categorie_id = c.id
INNER JOIN bngrc_donateur d ON dg.donateur_id = d.id
WHERE dg.status_distribution = 'disponible'
ORDER BY dg.date_don ASC, c.libelle;

CREATE VIEW vue_besoins_urgents AS
SELECT 
    b.id,
    b.quantite,
    b.prix_unitaire,
    b.date_besoin,
    b.priorite,
    b.description,
    v.nom as ville,
    r.nom as region,
    c.libelle as categorie,
    s.libelle as status
FROM bngrc_besoin b
INNER JOIN bngrc_ville v ON b.ville_id = v.id
INNER JOIN bngrc_region r ON v.region_id = r.id
INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
INNER JOIN bngrc_status s ON b.status_id = s.id
WHERE b.priorite IN ('haute', 'urgente') 
  AND b.status_id NOT IN (3, 5) -- Pas satisfait ou annulé
ORDER BY 
    FIELD(b.priorite, 'urgente', 'haute'),
    b.date_besoin ASC;

CREATE VIEW vue_statistics_globales AS
SELECT 
    'Dons globaux disponibles' as metric,
    COUNT(*) as count,
    SUM(quantite) as total_quantite
FROM bngrc_don_global 
WHERE status_distribution = 'disponible'
UNION ALL
SELECT 
    'Besoins en attente' as metric,
    COUNT(*) as count,
    SUM(quantite) as total_quantite
FROM bngrc_besoin 
WHERE status_id IN (1, 2, 6) -- En attente, en cours, urgent
UNION ALL
SELECT 
    'Distributions effectuées' as metric,
    COUNT(*) as count,
    SUM(quantite_distribuee) as total_quantite
FROM bngrc_distribution;

-- =========================
-- TRIGGERS POUR AUTOMATISATION
-- =========================

DELIMITER //

-- Trigger pour mettre à jour le statut du don global après distribution
CREATE TRIGGER after_distribution_insert 
    AFTER INSERT ON bngrc_distribution
    FOR EACH ROW
BEGIN
    DECLARE total_distribue INT;
    DECLARE quantite_originale INT;
    
    -- Calculer le total distribué pour le don
    SELECT SUM(quantite_distribuee) INTO total_distribue
    FROM bngrc_distribution 
    WHERE don_global_id = NEW.don_global_id;
    
    -- Récupérer la quantité originale du don
    SELECT quantite INTO quantite_originale
    FROM bngrc_don_global 
    WHERE id = NEW.don_global_id;
    
    -- Mettre à jour le statut si complètement distribué
    IF total_distribue >= quantite_originale THEN
        UPDATE bngrc_don_global 
        SET status_distribution = 'distribue',
            updated_at = CURRENT_TIMESTAMP
        WHERE id = NEW.don_global_id;
    END IF;
END//

-- Trigger pour mettre à jour automatiquement le montant total des achats
CREATE TRIGGER before_achat_insert_update
    BEFORE INSERT ON bngrc_achat
    FOR EACH ROW
BEGIN
    SET NEW.montant_total = NEW.montant * (1 + NEW.frais_percent / 100);
END//

CREATE TRIGGER before_achat_update
    BEFORE UPDATE ON bngrc_achat
    FOR EACH ROW
BEGIN
    SET NEW.montant_total = NEW.montant * (1 + NEW.frais_percent / 100);
END//

DELIMITER ;

-- =========================
-- INFORMATIONS FINALES
-- =========================

SELECT 
    '✅ Base de données BNGRC créée avec succès!' as status,
    'Le système utilise uniquement les dons globaux.' as info
UNION ALL
SELECT 
    '📊 Tables principales créées:',
    'bngrc_don_global, bngrc_distribution, bngrc_besoin, etc.'
UNION ALL
SELECT 
    '🎯 Données d''exemple insérées:',
    'Régions, villes, catégories, donateurs, besoins et dons'
UNION ALL
SELECT 
    '🔧 Vues et triggers configurés:',
    'Rapports automatisés et mises à jour en temps réel';
