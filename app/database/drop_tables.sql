-- Script pour supprimer les tables sans erreur de foreign key

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS bngrc_don;
DROP TABLE IF EXISTS bngrc_besoin;
DROP TABLE IF EXISTS bngrc_donateur;
DROP TABLE IF EXISTS bngrc_status;
DROP TABLE IF EXISTS bngrc_categorie;
DROP TABLE IF EXISTS bngrc_ville;
DROP TABLE IF EXISTS bngrc_region;
DROP TABLE IF EXISTS bngrc_dispatch;
DROP TABLE IF EXISTS bngrc_don_global;
DROP TABLE IF EXISTS bngrc_distribution;
DROP TABLE IF EXISTS bngrc_achat;


SET FOREIGN_KEY_CHECKS = 1;
