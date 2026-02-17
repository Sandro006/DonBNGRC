-- =========================
-- MIGRATION BASE004
-- Suppression complète de la table bngrc_don et migration vers bngrc_don_global
-- =========================

USE bngrc;

-- =========================
-- ÉTAPE 1: SAUVEGARDE ET MIGRATION DES DONNÉES (optionnel)
-- Décommentez ces lignes si vous voulez migrer les données existantes
-- =========================

-- Migrer les dons par ville vers les dons globaux (optionnel)
-- INSERT INTO bngrc_don_global (categorie_id, donateur_id, date_don, quantite, status_distribution)
-- SELECT categorie_id, donateur_id, date_don, quantite, 'disponible'
-- FROM bngrc_don;

-- =========================
-- ÉTAPE 2: SUPPRESSION DES CONTRAINTES ET TABLES DÉPENDANTES
-- =========================

-- Supprimer la table bngrc_dispatch qui dépend de bngrc_don
DROP TABLE IF EXISTS bngrc_dispatch;

-- =========================
-- ÉTAPE 3: SUPPRESSION DE LA TABLE BNGRC_DON
-- =========================

-- Supprimer la table bngrc_don (plus utilisée - remplacée par bngrc_don_global)
DROP TABLE IF EXISTS bngrc_don;

-- =========================
-- ÉTAPE 4: VÉRIFICATIONS ET OPTIMISATIONS
-- =========================

-- Ajouter des index supplémentaires pour optimiser les performances
-- (Ces index sont probablement déjà créés dans base002_modifications.sql)

-- Index pour la table bngrc_don_global si pas déjà présent
-- CREATE INDEX IF NOT EXISTS idx_don_global_status ON bngrc_don_global(status_distribution);
-- CREATE INDEX IF NOT EXISTS idx_don_global_categorie ON bngrc_don_global(categorie_id);
-- CREATE INDEX IF NOT EXISTS idx_don_global_date ON bngrc_don_global(date_don);

-- Index pour la table bngrc_distribution si pas déjà présent  
-- CREATE INDEX IF NOT EXISTS idx_distribution_don ON bngrc_distribution(don_global_id);
-- CREATE INDEX IF NOT EXISTS idx_distribution_besoin ON bngrc_distribution(besoin_id);
-- CREATE INDEX IF NOT EXISTS idx_distribution_date ON bngrc_distribution(date_distribution);

-- =========================
-- ÉTAPE 5: COMMENTAIRES ET DOCUMENTATION
-- =========================

-- IMPORTANT: Cette migration supprime définitivement :
-- 1. La table bngrc_don (dons liés à des villes spécifiques)
-- 2. La table bngrc_dispatch (distributions basées sur bngrc_don)
--
-- Le système utilise maintenant exclusivement :
-- 1. bngrc_don_global (dons globaux non liés à des villes spécifiques)
-- 2. bngrc_distribution (distributions des dons globaux vers les besoins)
--
-- Cette migration est cohérente avec les modifications du code qui ont
-- supprimé DonController, DonService, et le modèle Don.

-- =========================
-- ÉTAPE 6: VÉRIFICATION DE LA STRUCTURE FINALE
-- =========================

-- Vérifications que les bonnes tables existent
-- SELECT 'bngrc_don_global existe' as verification, COUNT(*) as nb_enregistrements FROM bngrc_don_global;
-- SELECT 'bngrc_distribution existe' as verification, COUNT(*) as nb_enregistrements FROM bngrc_distribution;

-- Vérifier que les tables supprimées n'existent plus
-- (Ces requêtes produiront une erreur si les tables existent encore)
-- SELECT 'ERREUR: bngrc_don existe encore!' FROM bngrc_don LIMIT 1;
-- SELECT 'ERREUR: bngrc_dispatch existe encore!' FROM bngrc_dispatch LIMIT 1;

-- =========================
-- FIN DE LA MIGRATION BASE004
-- =========================

-- RÉSUMÉ DES CHANGEMENTS:
-- ✅ Supprimé bngrc_don (table des dons par ville)
-- ✅ Supprimé bngrc_dispatch (table de dispatching basée sur bngrc_don)  
-- ✅ Conservé bngrc_don_global (table des dons globaux)
-- ✅ Conservé bngrc_distribution (table de distribution des dons globaux)
-- 
-- Le système fonctionne maintenant uniquement avec les dons globaux,
-- cohérent avec les modifications du code effectuées précédemment.
