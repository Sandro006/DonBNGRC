DROP DATABASE IF EXISTS bngrc;
CREATE DATABASE IF NOT EXISTS bngrc;
USE bngrc;

-- =========================
-- TABLE REGION
-- =========================
CREATE TABLE region (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
);

-- =========================
-- TABLE VILLE
-- =========================
CREATE TABLE ville (
    id INT AUTO_INCREMENT PRIMARY KEY,
    region_id INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    nombre_sinistres INT DEFAULT 0,

    CONSTRAINT fk_ville_region
        FOREIGN KEY (region_id)
        REFERENCES region(id)
        ON DELETE CASCADE
);

-- =========================
-- TABLE CATEGORIE
-- (nature, materiaux, argent)
-- =========================
CREATE TABLE categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL UNIQUE
);

-- =========================
-- TABLE STATUS
-- =========================
CREATE TABLE status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE
);

-- =========================
-- TABLE DONNATEUR
-- =========================
CREATE TABLE donateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    telephone VARCHAR(20),
    email VARCHAR(100)
);

-- =========================
-- TABLE BESOIN
-- =========================
CREATE TABLE besoin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville_id INT NOT NULL,
    categorie_id INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    status_id INT DEFAULT 1,

    CONSTRAINT fk_besoin_ville
        FOREIGN KEY (ville_id)
        REFERENCES ville(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_besoin_categorie
        FOREIGN KEY (categorie_id)
        REFERENCES categorie(id),

    CONSTRAINT fk_besoin_status
        FOREIGN KEY (status_id)
        REFERENCES status(id)
);

-- =========================
-- TABLE DON
-- =========================
CREATE TABLE don (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville_id INT NOT NULL,
    categorie_id INT NOT NULL,
    donateur_id INT NOT NULL,
    date_don DATETIME DEFAULT CURRENT_TIMESTAMP,
    quantite INT NOT NULL,

    CONSTRAINT fk_don_ville
        FOREIGN KEY (ville_id)
        REFERENCES ville(id),

    CONSTRAINT fk_don_categorie
        FOREIGN KEY (categorie_id)
        REFERENCES categorie(id),

    CONSTRAINT fk_don_donateur
        FOREIGN KEY (donateur_id)
        REFERENCES donateur(id)
);

