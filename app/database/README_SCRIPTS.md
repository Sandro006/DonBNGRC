# Scripts de Base de Données - Guide d'Exécution

## 📋 Ordre d'Exécution des Scripts

### Nouvelle Installation (Migration Complete vers Dons Globaux)

Exécutez dans cet ordre :

1. **`base001.sql`** - Création des tables de base
2. **`base002_modifications.sql`** - Ajout des tables don_global et distribution 
3. **`base003.sql`** - Ajout de la table achat
4. **`base004.sql`** - **MIGRATION CRITIQUE** - Suppression des dons par ville
5. **`Insertion001.sql`** *(optionnel)* - Données de base (régions, villes, catégories, donateurs, besoins)
6. **`Insertion_dons_globaux.sql`** - Insertion des dons globaux d'exemple
7. **`verification_migration.sql`** *(optionnel)* - Vérification que la migration s'est bien passée

### ⚠️ ATTENTION - Script base004.sql

**Le script `base004.sql` supprime définitivement :**
- La table `bngrc_don` (dons par ville)
- La table `bngrc_dispatch` (ancien système de dispatch)

**Avant d'exécuter base004.sql :**
- Sauvegardez vos données si nécessaire
- Assurez-vous que le code a été mis à jour pour utiliser les dons globaux
- Décommentez les lignes de migration dans base004.sql si vous voulez transférer les dons par ville vers les dons globaux

## 🗂️ Description des Scripts

### Scripts de Structure

- **`base001.sql`** : Tables fondamentales (région, ville, catégorie, status, donateur, besoin, don)
- **`base002.sql`** : Table dispatch (obsolète après migration)
- **`base002_modifications.sql`** : Tables don_global et distribution + optimisations
- **`base003.sql`** : Table d'achats
- **`base004.sql`** : Migration vers dons globaux uniquement

### Scripts de Données

- **`Insertion001.sql`** : Données d'exemple pour toutes les tables (⚠️ contient des dons par ville obsolètes)
- **`Insertion002.sql`** : Données supplémentaires
- **`Insertion_dons_globaux.sql`** : Dons globaux d'exemple (remplace les dons par ville)
- **`exemples_insertions.sql`** : Exemple d'insertions avancées

### Scripts de Maintenance

- **`verification_migration.sql`** : Vérification post-migration
- **`README_SCRIPTS.md`** : Ce fichier d'aide

## 🔧 États du Système

### Avant Migration (base001 + base002 + base003)
```
✅ bngrc_don (dons par ville)
✅ bngrc_dispatch (distribution par ville) 
✅ bngrc_don_global (dons globaux)
✅ bngrc_distribution (distribution globale)
```

### Après Migration (+ base004)
```
❌ bngrc_don (SUPPRIMÉE)
❌ bngrc_dispatch (SUPPRIMÉE)
✅ bngrc_don_global (dons globaux uniquement)
✅ bngrc_distribution (distribution globale uniquement)
```

## 🗃️ Migration des Données Existantes

Si vous avez des données dans `bngrc_don` et voulez les migrer vers `bngrc_don_global`, décommentez ces lignes dans `base004.sql` :

```sql
-- INSERT INTO bngrc_don_global (categorie_id, donateur_id, date_don, quantite, status_distribution)
-- SELECT categorie_id, donateur_id, date_don, quantite, 'disponible'
-- FROM bngrc_don;
```

## 🎯 Résultat Final

Après migration complète, le système fonctionne uniquement avec des **dons globaux** :
- Les dons ne sont plus liés à des villes spécifiques
- La distribution se fait de manière optimisée selon les besoins
- Interface utilisateur mise à jour pour refléter cette architecture