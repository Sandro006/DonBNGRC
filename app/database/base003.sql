
CREATE TABLE bngrc_achat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville_id INT NOT NULL,
    besoin_id INT NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    frais_percent DECIMAL(5,2) NOT NULL,
    montant_total DECIMAL(10,2) NOT NULL,
    date_achat DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ville_id) REFERENCES bngrc_ville(id),
    FOREIGN KEY (besoin_id) REFERENCES bngrc_besoin(id)
);
-- =========================
-- TABLE CONFIGURATION FRAIS
-- (Configuration for frais_percent)
-- =========================
CREATE TABLE bngrc_config_frais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    frais_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);