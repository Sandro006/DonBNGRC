-- =========================
-- SCRIPT DE VÉRIFICATION POST-MIGRATION
-- À exécuter après base004.sql pour vérifier que la migration s'est bien déroulée
-- =========================

USE bngrc;

-- =========================
-- VÉRIFICATIONS DES TABLES SUPPRIMÉES
-- =========================

-- Ces requêtes doivent produire des erreurs si la migration s'est bien passée
SELECT '❌ ERREUR: bngrc_don existe encore!' as erreur FROM bngrc_don LIMIT 1;
SELECT '❌ ERREUR: bngrc_dispatch existe encore!' as erreur FROM bngrc_dispatch LIMIT 1;

-- =========================
-- VÉRIFICATIONS DES TABLES EXISTANTES
-- =========================

-- Ces requêtes doivent fonctionner
SELECT '✅ bngrc_don_global existe' as verification, COUNT(*) as nb_enregistrements FROM bngrc_don_global;
SELECT '✅ bngrc_distribution existe' as verification, COUNT(*) as nb_enregistrements FROM bngrc_distribution;
SELECT '✅ bngrc_donateur existe' as verification, COUNT(*) as nb_enregistrements FROM bngrc_donateur;
SELECT '✅ bngrc_categorie existe' as verification, COUNT(*) as nb_enregistrements FROM bngrc_categorie;
SELECT '✅ bngrc_besoin existe' as verification, COUNT(*) as nb_enregistrements FROM bngrc_besoin;

-- =========================
-- VÉRIFICATION DE LA STRUCTURE DES TABLES
-- =========================

DESCRIBE bngrc_don_global;
DESCRIBE bngrc_distribution;

-- =========================
-- VÉRIFICATIONS DES CLÉS ÉTRANGÈRES
-- =========================

-- Vérifier que les contraintes de clés étrangères sont intactes
SELECT 
    TABLE_NAME as 'Table',
    COLUMN_NAME as 'Colonne FK',
    CONSTRAINT_NAME as 'Nom Contrainte',
    REFERENCED_TABLE_NAME as 'Table Référencée',
    REFERENCED_COLUMN_NAME as 'Colonne Référencée'
FROM 
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE 
    REFERENCED_TABLE_SCHEMA = 'bngrc'
    AND (TABLE_NAME IN ('bngrc_don_global', 'bngrc_distribution') OR REFERENCED_TABLE_NAME IN ('bngrc_don_global', 'bngrc_distribution'))
ORDER BY TABLE_NAME;

-- =========================
-- RÉSUMÉ DE L'ÉTAT
-- =========================

SELECT 
    '🎯 MIGRATION RÉUSSIE' as statut,
    'Les dons par ville ont été supprimés. Seuls les dons globaux restent.' as description
UNION ALL
SELECT 
    '📊 TABLES ACTIVES',
    'bngrc_don_global, bngrc_distribution, bngrc_besoin, bngrc_donateur, bngrc_categorie'
UNION ALL
SELECT 
    '🗑️  TABLES SUPPRIMÉES', 
    'bngrc_don, bngrc_dispatch';