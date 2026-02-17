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
(11, 'Toamasina', 7),
(10, 'Mananjary', 6),
(10, 'Farafangana', 5),
(2, 'Nosy Be', 4),
(21, 'Morondava', 5);

-- ===== CATÉGORIES =====
INSERT INTO bngrc_categorie (libelle, description) VALUES
('nature', 'Produits alimentaires et ressources naturelles'),
('materiel', 'Matériaux et équipements'),
('argent', 'Dons financiers pour achats urgents');

-- ===== STATUS =====
INSERT INTO bngrc_status (libelle, description) VALUES
('En attente', 'Besoin enregistré mais pas encore traité'),
('En cours', 'Besoin en cours de traitement'),
('Satisfait', 'Besoin complètement couvert');

-- ===== DONATEURS D'EXEMPLE =====
INSERT INTO bngrc_donateur (nom, telephone, email, type_donateur) VALUES
('BNGRC', '00 00 00 00 00', 'contact@bngrc.mg', 'association');

-- ===== BESOINS D'EXEMPLE =====
INSERT INTO bngrc_besoin (ville_id, categorie_id, quantite, prix_unitaire, status_id, priorite, date_besoin, description) VALUES
-- Toamasina
(1, 1, 800, 3000, 1, 'haute', '2026-02-16', 'Riz (kg)'),
(1, 1, 1500, 1000, 1, 'haute', '2026-02-15', 'Eau (L)'),
(1, 2, 120, 25000, 1, 'normale', '2026-02-16', 'Tôle'),
(1, 2, 200, 15000, 1, 'normale', '2026-02-15', 'Bâche'),
(1, 3, 12000000, 1, 1, 'urgente', '2026-02-16', 'Argent'),
(1, 2, 3, 6750000, 1, 'normale', '2026-02-15', 'groupe'),

-- Mananjary
(2, 1, 500, 3000, 1, 'haute', '2026-02-15', 'Riz (kg)'),
(2, 1, 120, 6000, 1, 'normale', '2026-02-16', 'Huile (L)'),
(2, 2, 80, 25000, 1, 'normale', '2026-02-15', 'Tôle'),
(2, 2, 60, 8000, 1, 'normale', '2026-02-16', 'Clous (kg)'),
(2, 3, 6000000, 1, 1, 'urgente', '2026-02-15', 'Argent'),

-- Farafangana
(3, 1, 600, 3000, 1, 'haute', '2026-02-16', 'Riz (kg)'),
(3, 1, 1000, 1000, 1, 'haute', '2026-02-15', 'Eau (L)'),
(3, 2, 150, 15000, 1, 'normale', '2026-02-16', 'Bâche'),
(3, 2, 100, 10000, 1, 'normale', '2026-02-15', 'Bois'),
(3, 3, 8000000, 1, 1, 'urgente', '2026-02-16', 'Argent'),

-- Nosy Be
(4, 1, 300, 3000, 1, 'haute', '2026-02-15', 'Riz (kg)'),
(4, 1, 200, 4000, 1, 'normale', '2026-02-16', 'Haricots'),
(4, 2, 40, 25000, 1, 'normale', '2026-02-15', 'Tôle'),
(4, 2, 30, 8000, 1, 'normale', '2026-02-16', 'Clous (kg)'),
(4, 3, 4000000, 1, 1, 'urgente', '2026-02-15', 'Argent'),

-- Morondava
(5, 1, 700, 3000, 1, 'haute', '2026-02-16', 'Riz (kg)'),
(5, 1, 1200, 1000, 1, 'haute', '2026-02-15', 'Eau (L)'),
(5, 2, 180, 15000, 1, 'normale', '2026-02-16', 'Bâche'),
(5, 2, 150, 10000, 1, 'normale', '2026-02-15', 'Bois'),
(5, 3, 10000000, 1, 1, 'urgente', '2026-02-16', 'Argent');

-- ===== DONS GLOBAUX D'EXEMPLE =====
-- Aucun don global préconfiguré (à ajouter via l'interface)

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
