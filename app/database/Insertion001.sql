-- =========================
-- DONNÉES DE TEST POUR BNGRC
-- =========================

-- ===== INSERTION DES RÉGIONS =====
INSERT INTO bngrc_region (nom) VALUES
('Analamanga'),
('Vakinakaratra'),
('Antsinana');

-- ===== INSERTION DES VILLES =====
INSERT INTO bngrc_ville (region_id, nom, nombre_sinistres) VALUES
(1, 'Antananarivo', 5),
(1, 'Itasy', 2),
(2, 'Antsirabe', 8),
(2, 'Ambatolampy', 3),
(3, 'Tamatavy', 6),
(3, 'Brikavilla', 4);

-- ===== INSERTION DES CATÉGORIES =====
INSERT INTO bngrc_categorie (libelle) VALUES
('Nature'),
('Materiaux'),
('Argent');

-- ===== INSERTION DES STATUTS =====
INSERT INTO bngrc_status (libelle) VALUES
('valider'),
('En cours de traitement'),
('Annulé');

-- ===== INSERTION DES DONNATEURS =====
INSERT INTO bngrc_donateur (nom, telephone, email) VALUES
('Jean Dupont', '06 12 34 56 78', 'jean.dupont@email.com'),
('Marie Martin', '06 23 45 67 89', 'marie.martin@email.com'),
('Association Solidarité France', '01 23 45 67 89', 'contact@solidarite-france.fr'),
('Pierre Bernard', '06 34 56 78 90', 'pierre.bernard@email.com'),
('Luc Fontaine', '06 45 67 89 01', 'luc.fontaine@email.com'),
('Entreprise XYZ', '01 34 56 78 90', 'rh@entreprise-xyz.fr'),
('Sophie Laurent', '06 56 78 90 12', 'sophie.laurent@email.com'),
('ONG Aide Humanitaire', '01 45 67 89 01', 'contact@ong-aide.org'),
('Thomas Renaud', '06 67 89 01 23', 'thomas.renaud@email.com'),
('Céline Moreau', '06 78 90 12 34', 'celine.moreau@email.com');

-- ===== INSERTION DES BESOINS =====
INSERT INTO bngrc_besoin (ville_id, categorie_id, quantite, prix_unitaire, status_id) VALUES
(1, 1, 500, 2.50, 2),
(1, 2, 200, 15.00, 1),
(3, 1, 1000, 2.00, 3),
(3, 3, 300, 5.50, 1),
(5, 2, 100, 45.00, 2),
(2, 1, 400, 12.00, 1),
(2, 3, 2000, 0.80, 3),
(4, 2, 150, 20.00, 2),
(1, 1, 300, 3.00, 2),
(6, 2, 80, 50.00, 1),
(4, 1, 600, 10.00, 2),
(3, 3, 200, 6.00, 1),
(5, 1, 800, 2.50, 2),
(6, 2, 250, 18.00, 1),
(2, 3, 1500, 0.75, 3);

-- ===== INSERTION DES DONS =====
INSERT INTO bngrc_don (ville_id, categorie_id, donateur_id, date_don, quantite) VALUES
(1, 1, 1, NOW(), 100),
(1, 2, 2, NOW() - INTERVAL 5 DAY, 50),
(3, 1, 3, NOW() - INTERVAL 10 DAY, 300),
(3, 3, 4, NOW() - INTERVAL 2 DAY, 100),
(5, 2, 5, NOW() - INTERVAL 8 DAY, 30),
(2, 1, 6, NOW() - INTERVAL 1 DAY, 150),
(2, 3, 7, NOW() - INTERVAL 15 DAY, 500),
(4, 2, 8, NOW() - INTERVAL 3 DAY, 80),
(1, 1, 9, NOW(), 200),
(6, 3, 10, NOW() - INTERVAL 7 DAY, 25),
(4, 2, 1, NOW() - INTERVAL 4 DAY, 75),
(3, 1, 2, NOW() - INTERVAL 6 DAY, 50),
(5, 2, 3, NOW() - INTERVAL 9 DAY, 200),
(6, 1, 4, NOW() - INTERVAL 11 DAY, 120),
(2, 3, 5, NOW() - INTERVAL 2 DAY, 300),
(1, 3, 6, NOW() - INTERVAL 1 DAY, 5000),
(3, 3, 7, NOW() - INTERVAL 3 DAY, 2000),
(2, 3, 8, NOW(), 1500);
