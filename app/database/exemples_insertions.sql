-- =========================
-- INSERTIONS D'EXEMPLE POUR TESTER LE NOUVEAU SYSTÈME
-- =========================

USE bngrc;

-- =========================
-- Mise à jour des besoins existants avec des dates
-- (Si vous avez déjà des données, adaptez ces dates selon vos besoins)
-- =========================

-- Exemple de mise à jour des dates de besoin pour simuler des priorités
UPDATE bngrc_besoin SET date_besoin = '2026-01-15 10:00:00' WHERE id = 1;
UPDATE bngrc_besoin SET date_besoin = '2026-01-20 14:30:00' WHERE id = 2;
UPDATE bngrc_besoin SET date_besoin = '2026-01-18 09:15:00' WHERE id = 3;

-- =========================
-- EXEMPLES DE DONS GLOBAUX
-- =========================

-- Supposons que vous ayez déjà des donateurs et des catégories
INSERT INTO bngrc_don_global (categorie_id, donateur_id, quantite, date_don) VALUES
-- Don de matériaux de construction (catégorie 1) par le donateur 1
(1, 1, 100, '2026-02-01 08:00:00'),

-- Don de nourriture (catégorie 2) par le donateur 2  
(2, 2, 50, '2026-02-02 10:30:00'),

-- Don d'argent (catégorie 3) par le donateur 3
(3, 3, 200, '2026-02-03 15:45:00'),

-- Autre don de matériaux par le donateur 1
(1, 1, 75, '2026-02-05 11:20:00');

-- =========================
-- EXEMPLES D'UTILISATION DES PROCÉDURES
-- =========================

-- Pour distribuer tous les dons automatiquement selon la priorité des dates
-- CALL DistribuerDonsAutomatiquement();

-- Pour distribuer un don spécifique (exemple: don_global_id = 1)
-- CALL DistribuerDonSpecifique(1, 100, 1);

-- =========================
-- REQUÊTES DE VÉRIFICATION
-- =========================

-- Voir tous les dons globaux disponibles
SELECT 
    dg.id,
    c.libelle as categorie,
    d.nom as donateur,
    dg.quantite,
    dg.date_don,
    dg.status_distribution
FROM bngrc_don_global dg
JOIN bngrc_categorie c ON dg.categorie_id = c.id
JOIN bngrc_donateur d ON dg.donateur_id = d.id
ORDER BY dg.date_don;

-- Voir tous les besoins par ordre de priorité
SELECT 
    b.id,
    v.nom as ville,
    c.libelle as categorie,
    b.quantite,
    b.date_besoin,
    DATEDIFF(NOW(), b.date_besoin) as jours_attente
FROM bngrc_besoin b
JOIN bngrc_ville v ON b.ville_id = v.id
JOIN bngrc_categorie c ON b.categorie_id = c.id
ORDER BY b.date_besoin ASC;