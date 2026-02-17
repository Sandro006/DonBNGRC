-- =========================
-- INSERTIONS MISES À JOUR POUR DONS GLOBAUX
-- Ce fichier remplace Insertion001.sql para la partie dons
-- =========================

USE bngrc;

-- ===== INSERTION DES DONS GLOBAUX (mise à jour du fichier Insertion001.sql) =====
-- Ces dons ne sont plus liés à des villes spécifiques mais sont globaux
INSERT INTO bngrc_don_global (categorie_id, donateur_id, date_don, quantite, status_distribution) VALUES
-- Catégorie 1 (Alimentation)
(1, 1, NOW(), 100, 'disponible'),
(1, 3, NOW() - INTERVAL 10 DAY, 300, 'disponible'),
(1, 6, NOW() - INTERVAL 1 DAY, 150, 'disponible'),
(1, 9, NOW(), 200, 'disponible'),
(1, 2, NOW() - INTERVAL 6 DAY, 50, 'disponible'),
(1, 4, NOW() - INTERVAL 11 DAY, 120, 'disponible'),

-- Catégorie 2 (Matériaux)
(2, 2, NOW() - INTERVAL 5 DAY, 50, 'disponible'),
(2, 5, NOW() - INTERVAL 8 DAY, 30, 'disponible'),
(2, 8, NOW() - INTERVAL 3 DAY, 80, 'disponible'),
(2, 1, NOW() - INTERVAL 4 DAY, 75, 'disponible'),
(2, 3, NOW() - INTERVAL 9 DAY, 200, 'disponible'),

-- Catégorie 3 (Argent)
(3, 4, NOW() - INTERVAL 2 DAY, 100, 'disponible'),
(3, 7, NOW() - INTERVAL 15 DAY, 500, 'disponible'),
(3, 10, NOW() - INTERVAL 7 DAY, 25, 'disponible'),
(3, 5, NOW() - INTERVAL 2 DAY, 300, 'disponible'),
(3, 6, NOW() - INTERVAL 1 DAY, 5000, 'disponible'),
(3, 7, NOW() - INTERVAL 3 DAY, 2000, 'disponible'),
(3, 8, NOW(), 1500, 'disponible');

-- ===== QUELQUES DONS SUPPLÉMENTAIRES POUR LA SIMULATION =====
INSERT INTO bngrc_don_global (categorie_id, donateur_id, date_don, quantite, status_distribution) VALUES
-- Plus de dons pour tester les distributions
(1, 1, NOW() - INTERVAL 12 DAY, 500, 'disponible'),
(1, 2, NOW() - INTERVAL 8 DAY, 250, 'disponible'),
(2, 3, NOW() - INTERVAL 5 DAY, 100, 'disponible'),
(2, 4, NOW() - INTERVAL 3 DAY, 180, 'disponible'),
(3, 5, NOW() - INTERVAL 1 DAY, 1000, 'disponible'),
(3, 6, NOW() - INTERVAL 14 DAY, 750, 'disponible'),

-- Quelques dons déjà distribués pour l'historique
(1, 7, NOW() - INTERVAL 20 DAY, 200, 'distribue'),
(2, 8, NOW() - INTERVAL 18 DAY, 50, 'distribue'),
(3, 9, NOW() - INTERVAL 16 DAY, 300, 'distribue'),

-- Quelques dons réservés
(1, 10, NOW() - INTERVAL 6 DAY, 150, 'reserve'),
(2, 1, NOW() - INTERVAL 4 DAY, 80, 'reserve');

-- =========================
-- NOTE IMPORTANTE
-- =========================
-- Ce fichier remplace les insertions de dons par ville du fichier Insertion001.sql
-- Tous les dons sont maintenant globaux et peuvent être distribués à n'importe quelle ville selon les besoins
-- Les status_distribution possibles sont : 'disponible', 'distribue', 'reserve'