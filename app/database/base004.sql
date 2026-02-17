-- =========================
-- MODIFICATIONS BASE002
-- Ajout date_besoin et table don_global
-- =========================

USE bngrc;

-- ================================
-- ALTER TABLE: Ajouter date_besoin
-- ================================
ALTER TABLE bngrc_besoin 
ADD COLUMN date_besoin DATETIME DEFAULT CURRENT_TIMESTAMP;

-- Mise à jour des besoins existants avec la date actuelle si nécessaire
-- UPDATE bngrc_besoin SET date_besoin = CURRENT_TIMESTAMP WHERE date_besoin IS NULL;

-- =========================
-- TABLE DON GLOBAL
-- (Dons qui ne sont pas liés à une ville spécifique)
-- =========================
CREATE TABLE bngrc_don_global (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categorie_id INT NOT NULL,
    donateur_id INT NOT NULL,
    date_don DATETIME DEFAULT CURRENT_TIMESTAMP,
    quantite INT NOT NULL,
    status_distribution ENUM('disponible', 'distribue', 'reserve') DEFAULT 'disponible',

    CONSTRAINT fk_don_global_categorie
        FOREIGN KEY (categorie_id)
        REFERENCES bngrc_categorie(id),

    CONSTRAINT fk_don_global_donateur
        FOREIGN KEY (donateur_id)
        REFERENCES bngrc_donateur(id)
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

    CONSTRAINT fk_distribution_don_global
        FOREIGN KEY (don_global_id)
        REFERENCES bngrc_don_global(id),

    CONSTRAINT fk_distribution_besoin
        FOREIGN KEY (besoin_id)
        REFERENCES bngrc_besoin(id)
);

-- =========================
-- INDEX pour optimiser les requêtes de simulation
-- =========================

-- Index sur date_besoin pour la prioritisation
CREATE INDEX idx_besoin_date ON bngrc_besoin(date_besoin);

-- Index sur status_id pour filtrer les besoins non satisfaits
CREATE INDEX idx_besoin_status ON bngrc_besoin(status_id);

-- Index composite pour la simulation (categorie + status + date)
CREATE INDEX idx_besoin_simulation ON bngrc_besoin(categorie_id, status_id, date_besoin);

-- Index sur le statut de distribution des dons globaux
CREATE INDEX idx_don_global_status ON bngrc_don_global(status_distribution);

-- Index composite pour les dons disponibles par catégorie
CREATE INDEX idx_don_global_disponible ON bngrc_don_global(categorie_id, status_distribution);