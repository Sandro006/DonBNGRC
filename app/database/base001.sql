DROP DATABASE IF EXISTS bngrc;

CREATE DATABASE IF NOT EXISTS bngrc;
USE bngrc;

-- =========================
-- TABLE REGION
-- =========================
CREATE TABLE bngrc_region (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
);

-- =========================
-- TABLE VILLE
-- =========================
CREATE TABLE bngrc_ville (
    id INT AUTO_INCREMENT PRIMARY KEY,
    region_id INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    nombre_sinistres INT DEFAULT 0,

    CONSTRAINT fk_ville_region
        FOREIGN KEY (region_id)
        REFERENCES bngrc_region(id)
        ON DELETE CASCADE
);

-- =========================
-- TABLE CATEGORIE
-- (nature, materiaux, argent)
-- =========================
CREATE TABLE bngrc_categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL UNIQUE
);

-- =========================
-- TABLE STATUS
-- =========================
CREATE TABLE bngrc_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE
);

-- =========================
-- TABLE DONNATEUR
-- =========================
CREATE TABLE bngrc_donateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    telephone VARCHAR(20),
    email VARCHAR(100)
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

    CONSTRAINT fk_besoin_ville
        FOREIGN KEY (ville_id)
        REFERENCES bngrc_ville(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_besoin_categorie
        FOREIGN KEY (categorie_id)
        REFERENCES bngrc_categorie(id),

    CONSTRAINT fk_besoin_status
        FOREIGN KEY (status_id)
        REFERENCES bngrc_status(id)
);

-- =========================
-- TABLE DON
-- =========================
CREATE TABLE bngrc_don (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville_id INT NOT NULL,
    categorie_id INT NOT NULL,
    donateur_id INT NOT NULL,
    date_don DATETIME DEFAULT CURRENT_TIMESTAMP,
    quantite INT NOT NULL,

    CONSTRAINT fk_don_ville
        FOREIGN KEY (ville_id)
        REFERENCES bngrc_ville(id),

    CONSTRAINT fk_don_categorie
        FOREIGN KEY (categorie_id)
        REFERENCES bngrc_categorie(id),

    CONSTRAINT fk_don_donateur
        FOREIGN KEY (donateur_id)
        REFERENCES bngrc_donateur(id)
);

