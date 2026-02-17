-- =========================
-- INSERTION DES DONNÉES DE DON 1
-- Données fournies le 2026-02-17
-- =========================

USE bngrc;

-- Mapping des catégories:
-- argent => categorie_id 3
-- nature => categorie_id 1
-- materiel => categorie_id 2

-- Donateur BNGRC => donateur_id 1

INSERT INTO bngrc_don_global (categorie_id, donateur_id, date_don, quantite, status_distribution) VALUES
-- Dons en argent
(3, 1, '2026-02-16', 5000000, 'disponible'),
(3, 1, '2026-02-16', 3000000, 'disponible'),
(3, 1, '2026-02-17', 4000000, 'disponible'),
(3, 1, '2026-02-17', 1500000, 'disponible'),
(3, 1, '2026-02-17', 6000000, 'disponible'),

-- Dons de nature (alimentation)
(1, 1, '2026-02-16', 400, 'disponible'),  -- Riz (kg)
(1, 1, '2026-02-16', 600, 'disponible'),  -- Eau (L)
(1, 1, '2026-02-17', 100, 'disponible'),  -- Haricots
(1, 1, '2026-02-18', 2000, 'disponible'), -- Riz (kg)
(1, 1, '2026-02-18', 5000, 'disponible'), -- Eau (L)
(1, 1, '2026-02-17', 88, 'disponible'),   -- Haricots

-- Dons de matériel
(2, 1, '2026-02-17', 50, 'disponible'),   -- Tôle
(2, 1, '2026-02-17', 70, 'disponible'),   -- Bâche
(2, 1, '2026-02-18', 300, 'disponible'),  -- Tôle
(2, 1, '2026-02-19', 500, 'disponible'),  -- Bâche

-- Don en argent supplémentaire
(3, 1, '2026-02-19', 20000000, 'disponible');

-- =========================
-- VÉRIFICATION DES INSERTIONS
-- =========================
SELECT 
    'Dons insérés avec succès!' as status,
    COUNT(*) as total_dons
FROM bngrc_don_global 
WHERE date_don >= '2026-02-16';

-- Résumé par catégorie
SELECT 
    c.libelle as categorie,
    COUNT(*) as nombre_dons,
    SUM(dg.quantite) as total_quantite
FROM bngrc_don_global dg
INNER JOIN bngrc_categorie c ON dg.categorie_id = c.id
WHERE dg.date_don >= '2026-02-16'
GROUP BY c.id, c.libelle
ORDER BY c.libelle;
