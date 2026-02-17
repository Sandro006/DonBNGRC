# Configuration des Frais - Documentation

## Résumé des modifications

J'ai créé un formulaire complet de configuration du `frais_percent` de la table `bngrc_achat`. Voici ce qui a été implémenté:

## 📁 Fichiers créés/modifiés

### 1. **Vue - [app/views/ConfigurationFrais.php](app/views/ConfigurationFrais.php) (créée)**
   - Formulaire HTML pour configurer le pourcentage de frais
   - Affichage du pourcentage actuel
   - Historique des modifications (optionnel)
   - Validation client-side (JavaScript)
   - Soumission AJAX du formulaire
   - Styles Bootstrap 5

### 2. **Contrôleur - [app/controllers/ConfigurationFraisController.php](app/controllers/ConfigurationFraisController.php) (créé)**
   - `index()` - Affiche le formulaire de configuration
   - `updateFrais()` - Met à jour le pourcentage via API (POST)
   - `getFraisConfig()` - Récupère la configuration actuelle (GET API)

### 3. **Service - [app/services/ConfigurationFraisService.php](app/services/ConfigurationFraisService.php) (créé)**
   - Gère la logique métier de la configuration
   - Valide les entrées (0-100)
   - Maintient l'historique

### 4. **Modèle - [app/models/ConfigurationFrais.php](app/models/ConfigurationFrais.php) (créé)**
   - Interactions avec la base de données
   - Extend BaseModel
   - Méthodes CRUD pour la configuration des frais

### 5. **Routes - [app/config/routes.php](app/config/routes.php) (modifiée)**
   ```php
   // Afficher le formulaire
   $router->get('/configuration/frais', [app\controllers\ConfigurationFraisController::class, 'index']);

   // API endpoints
   $router->get('/api/configuration/frais', [app\controllers\ConfigurationFraisController::class, 'getFraisConfig']);
   $router->post('/api/configuration/frais', [app\controllers\ConfigurationFraisController::class, 'updateFrais']);
   ```

### 6. **Base de données - [app/database/base003.sql](app/database/base003.sql) (modifiée)**
   - Création de la table `bngrc_config_frais`:
   ```sql
   CREATE TABLE bngrc_config_frais (
       id INT AUTO_INCREMENT PRIMARY KEY,
       frais_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,
       description TEXT,
       created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
       updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
   );
   ```

### 7. **Données initiales - [app/database/Insertion002.sql](app/database/Insertion002.sql) (modifiée)**
   - Insertion d'une configuration de frais par défaut (0.00%)

## 🚀 Utilisation

### Accéder au formulaire
```
GET http://localhost/configuration/frais
```

### API - Récupérer la configuration actuelle
```
GET http://localhost/api/configuration/frais

Réponse:
{
    "success": true,
    "current_frais_percent": 5.50
}
```

### API - Mettre à jour la configuration
```
POST http://localhost/api/configuration/frais
Content-Type: application/json

{
    "frais_percent": 5.50,
    "description": "Nouveau pourcentage appliqué à partir de février 2026"
}

Réponse:
{
    "success": true,
    "message": "Configuration des frais mise à jour avec succès",
    "frais_percent": 5.50
}
```

## ✅ Fonctionnalités

- ✅ Affichage du pourcentage de frais actuel
- ✅ Formulaire de mise à jour avec validation
- ✅ Validation du range (0-100%)
- ✅ Description optionnelle pour documenter les changements
- ✅ Historique des modifications
- ✅ Messages de succès/erreur
- ✅ Interface responsive Bootstrap 5
- ✅ Soumission AJAX du formulaire
- ✅ API endpoints pour l'intégration

## 📝 Notes importantes

1. **Initialiser la base de données**: Assurez-vous d'exécuter les scripts SQL pour créer la table `bngrc_config_frais`:
   ```bash
   mysql -u user -p database_name < app/database/base003.sql
   mysql -u user -p database_name < app/database/Insertion002.sql
   ```

2. **Pourcentage**: Entrez des valeurs entre 0 et 100, avec deux décimales max (ex: 5.50 pour 5.50%)

3. **Historique**: La table conserve l'historique de toutes les modifications pour un audit trail complet

4. **Intégration**: Le service peut être utilisé dans d'autres contrôleurs pour obtenir le pourcentage de frais:
   ```php
   $fraisService = new ConfigurationFraisService();
   $currentPercent = $fraisService->getCurrentFraisPercent();
   ```

## 🔗 Points d'accès

- **Formulaire**: `/configuration/frais`
- **API GET**: `/api/configuration/frais`
- **API POST**: `/api/configuration/frais`

