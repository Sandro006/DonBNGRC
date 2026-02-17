# Mise à Jour du Système de Dons - BNGRC

## Résumé des Modifications

Ce document décrit les modifications apportées au système de dons BNGRC pour implémenter les nouveaux besoins :

1. **Ajout de `date_besoin`** à la table des besoins pour la prioritisation
2. **Création d'une table `bngrc_don_global`** pour les dons qui ne sont pas liés à une ville spécifique
3. **Système de distribution automatique** basé sur les priorités (date de besoin la plus ancienne en premier)
4. **Nouvelles fonctionnalités de simulation** pour optimiser la distribution

## Modifications de la Base de Données

### Fichiers SQL Créés

1. **`base002_modifications.sql`** - Contient toutes les modifications structurelles :
   - ALTER TABLE pour ajouter `date_besoin` à `bngrc_besoin`
   - Création de `bngrc_don_global`
   - Création de `bngrc_distribution` pour tracer les distributions
   - Index optimisés pour les requêtes de simulation

2. **`requetes_simulation.sql`** - Procédures stockées et requêtes utiles :
   - Procédures pour la distribution automatique
   - Vues pour l'analyse des distributions
   - Requêtes de rapport et statistiques

3. **`exemples_insertions.sql`** - Exemples d'utilisation du nouveau système

### Nouvelles Tables

#### `bngrc_don_global`
```sql
- id (PK)
- categorie_id (FK vers bngrc_categorie)
- donateur_id (FK vers bngrc_donateur)
- date_don (DATETIME)
- quantite (INT)
- status_distribution (ENUM: 'disponible', 'distribue', 'reserve')
```

#### `bngrc_distribution`
```sql
- id (PK)
- don_global_id (FK vers bngrc_don_global)
- besoin_id (FK vers bngrc_besoin)
- quantite_distribuee (INT)
- date_distribution (DATETIME)
```

### Modifications de Tables Existantes

#### `bngrc_besoin`
- **Ajout** : `date_besoin DATETIME DEFAULT CURRENT_TIMESTAMP`

## Nouveaux Modèles PHP

### 1. DonGlobal (app/models/DonGlobal.php)
- Gère les dons globaux (sans ville spécifique)
- Méthodes pour récupérer les dons disponibles par catégorie
- Statistiques des dons par catégorie
- Gestion des statuts de distribution

### 2. Distribution (app/models/Distribution.php)
- Gère les enregistrements de distribution
- Liens entre dons globaux et besoins
- Mise à jour automatique des statuts
- Statistiques de distribution

### 3. Besoin (Mise à jour)
- Nouvelles méthodes pour la gestion des priorités basées sur `date_besoin`
- Méthodes pour la simulation
- Tri par priorité (date la plus ancienne en premier)

## Nouveau Service

### SimulationDistributionService (app/services/SimulationDistributionService.php)
- **Simulation** : Simule la distribution automatique sans modifier les données
- **Distribution automatique** : Effectue la distribution réelle basée sur la simulation
- **Algorithme de priorité** : Distribue aux besoins les plus anciens en premier
- **Suggestions** : Propose des distributions optimales par catégorie

## Nouveau Contrôleur

### DonGlobalController (app/controllers/DonGlobalController.php)
**Routes principales :**
- `GET /don-global` - Liste des dons globaux
- `GET /don-global/create` - Formulaire d'ajout
- `POST /don-global/store` - Enregistrer un nouveau don
- `GET /don-global/simulation` - Page de simulation
- `POST /don-global/execute-distribution` - Exécuter la distribution

**API endpoints :**
- `GET /api/don-global/categorie/{id}` - Dons disponibles par catégorie
- `GET /api/don-global/suggestions/{categorie_id}` - Suggestions de distribution
- `POST /api/don-global/distribution-manuelle` - Distribution manuelle

## Algorithme de Distribution

### Comment ça fonctionne :

1. **Récupération des besoins** triés par `date_besoin` (plus ancien = plus prioritaire)
2. **Récupération des dons globaux disponibles**
3. **Distribution séquentielle** :
   - Pour chaque besoin (dans l'ordre de priorité)
   - Trouve les dons de la même catégorie
   - Distribue la quantité nécessaire en commençant par les dons les plus anciens
4. **Mise à jour des statuts** automatiquement

### Exemple concret :
```
Besoins (par date) :
- Besoin A: 100 unités, Catégorie 1, Date: 2026-01-15
- Besoin B: 50 unités, Catégorie 1, Date: 2026-01-20
- Besoin C: 75 unités, Catégorie 2, Date: 2026-01-18

Dons disponibles :
- Don X: 60 unités, Catégorie 1
- Don Y: 80 unités, Catégorie 1
- Don Z: 100 unités, Catégorie 2

Distribution automatique :
1. Besoin A (le plus ancien) reçoit 60 unités du Don X + 40 unités du Don Y
2. Besoin C reçoit 75 unités du Don Z
3. Besoin B reçoit 40 unités restantes du Don Y
```

## Migration - Comment Appliquer les Changements

### 1. Exécuter les Scripts SQL
```sql
-- Exécuter dans l'ordre :
SOURCE app/database/base002_modifications.sql;
SOURCE app/database/requetes_simulation.sql;
```

### 2. Configurations Optionnelles
```sql
-- Pour les données existantes, mettre à jour les dates de besoin
UPDATE bngrc_besoin 
SET date_besoin = DATE_SUB(CURRENT_TIMESTAMP, INTERVAL FLOOR(RAND() * 30) DAY)
WHERE date_besoin IS NULL;
```

### 3. Test du Système
```sql
-- Insérer des données de test
SOURCE app/database/exemples_insertions.sql;

-- Tester la distribution automatique
CALL DistribuerDonsAutomatiquement();
```

## Utilisation du Nouveau Système

### Pour ajouter un don global (sans ville) :
1. Aller sur `/don-global/create`
2. Choisir catégorie, donateur, quantité
3. Le don sera disponible pour distribution automatique

### Pour simuler la distribution :
1. Aller sur `/don-global/simulation`
2. Voir quels besoins seraient satisfaits
3. Voir l'ordre de priorité
4. Exécuter la distribution si satisfait

### Pour la distribution manuelle :
1. Utiliser l'API `/api/don-global/suggestions/{categorie_id}`
2. Choisir manuellement quels dons distribuer
3. Utiliser `/don-global/distribution-manuelle`

## Avantages du Nouveau Système

1. **Équité** : Les besoins les plus anciens sont servis en premier
2. **Efficacité** : Distribution automatique optimisée
3. **Flexibilité** : Possibilité de dons globaux ou spécifiques à une ville
4. **Traçabilité** : Historique complet des distributions
5. **Simulation** : Test avant exécution réelle
6. **API complète** : Intégration facile avec applications mobiles/web

## États des Dons et Besoins

### Statuts des dons globaux :
- `disponible` : Prêt pour distribution
- `reserve` : Partiellement distribué
- `distribue` : Complètement distribué

### Statuts des besoins :
- `en_attente` : Pas encore satisfait
- `partiellement_satisfait` : Partiellement distribué
- `satisfait` : Complètement satisfait

## Requêtes Utiles

### Voir les besoins par priorité :
```sql
SELECT * FROM v_resume_besoins 
ORDER BY besoin_le_plus_ancien;
```

### Voir les distributions effectuées :
```sql
SELECT * FROM v_distributions 
ORDER BY date_distribution DESC;
```

### Statistiques globales :
```sql
CALL DistribuerDonsAutomatiquement(); -- Mode simulation
```

## Support et Dépannage

### Si la distribution ne fonctionne pas :
1. Vérifier que les dons ont le statut `disponible`
2. Vérifier que les besoins ne sont pas déjà `satisfait`
3. Vérifier la correspondance des catégories

### Pour réinitialiser les distributions :
```sql
DELETE FROM bngrc_distribution;
UPDATE bngrc_don_global SET status_distribution = 'disponible';
UPDATE bngrc_besoin SET status_id = 1; -- En attente
```