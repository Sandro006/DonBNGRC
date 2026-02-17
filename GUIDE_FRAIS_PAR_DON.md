# Configuration des Frais par Don - Guide d'Intégration

## 📋 Résumé des modifications

J'ai créé un système permettant de configurer le pourcentage de frais **pour chaque don individuellement**, mais **seulement pour les dons qui ne sont pas de l'argent** (Nature et Matériaux).

## 📁 Fichiers créés/modifiés

### 1. **Base de données** - `app/database/base001.sql`
- ✅ Ajoutée colonne `frais_percent DECIMAL(5,2)` à `bngrc_don` (nullable)

### 2. **Contrôleur** - `app/controllers/DonFraisController.php` (nouveau)
- `updateDonFrais($id)` - Mise à jour des frais (POST)
- `getDonFrais($id)` - Récupération des infos (GET)
- Valide que la catégorie n'est pas "Argent"

### 3. **Modèle** - `app/models/Don.php` (modifié)
- Ajoutée méthode `getCategoryById($categorie_id)`

### 4. **Routes** - `app/config/routes.php` (modifiée)
```php
$router->get('/api/don/@id:[0-9]+/frais', ...)
$router->post('/api/don/@id:[0-9]+/frais', ...)
```

### 5. **Modale réutilisable** - `app/views/partials/modal-edit-frais.php` (nouveau)
- Modale Bootstrap avec formulaire de saisie
- Validation client-side
- Affichage d'erreurs/succès

### 6. **Helper** - `app/views/partials/frais-helper.php` (nouveau)
- `renderFraisButton($don)` - Rend le bouton normal
- `renderFraisButtonSmall($don)` - Rend le bouton petit
- `isMoneyCategory($categorieName)` - Vérifie si c'est de l'argent

## 🚀 Intégration dans les vues

### En haut de votre vue (Dashboard.php, CityDetails.php, etc.)

```php
<?php require_once 'app/views/partials/frais-helper.php'; ?>
```

### Intégrer la modale (avant le tag </body>)

```php
<?php include_once 'app/views/partials/modal-edit-frais.php'; ?>
```

### Ajouter le bouton pour chaque don dans un tableau

**Exemple dans Dashboard:**

```php
<table class="table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Donateur</th>
            <th>Catégorie</th>
            <th>Quantité</th>
            <th>Frais</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($dons)): ?>
            <?php foreach ($dons as $d): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($d['date_don'] ?? 'now')) ?></td>
                    <td><?= htmlspecialchars($d['donateur_nom'] ?? '') ?></td>
                    <td><?= htmlspecialchars($d['categorie_nom'] ?? '') ?></td>
                    <td><?= htmlspecialchars($d['quantite'] ?? '') ?></td>
                    <td>
                        <?php echo renderFraisButton($d); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
```

### Alternative: Bouton compact dans les actions

```php
<div class="action-buttons">
    <?php if (!isMoneyCategory($d['categorie_nom'] ?? '')): ?>
        <button 
            type="button" 
            class="btn btn-sm btn-warning" 
            onclick="openEditFraisModal(<?= $d['id'] ?>, <?= $d['frais_percent'] ?? 0 ?>)"
        >
            <i class="bi bi-percent"></i> Frais
        </button>
    <?php endif; ?>
    <!-- Autres boutons d'actions -->
</div>
```

## 🎯 Fonctionnement

1. **Clic sur le bouton Frais** → Ouverture de la modale
2. **Saisie du pourcentage** → Validation (0-100)
3. **Clic Enregistrer** → Appel API POST `/api/don/{id}/frais`
4. **Mise à jour** → Le bouton affiche le nouveau pourcentage
5. **Non visible pour l'argent** → Le bouton disparaît automatiquement

## 📊 Exemple API

### GET - Récupérer les frais d'un don
```bash
GET /api/don/5/frais

Réponse:
{
    "success": true,
    "don": {
        "id": 5,
        "categorie_nom": "Nature",
        "frais_percent": 5.50,
        "donateur_nom": "Jean Dupont"
    }
}
```

### POST - Mettre à jour les frais
```bash
POST /api/don/5/frais
Content-Type: application/json

{
    "frais_percent": 7.25
}

Réponse:
{
    "success": true,
    "message": "Frais mis à jour avec succès",
    "frais_percent": 7.25
}
```

## ✅ Points de vérification

- ✅ Bouton n'apparaît que pour Nature et Matériaux
- ✅ Validation du range 0-100
- ✅ Mise à jour en temps réel via AJAX
- ✅ Modal réutilisable dans toutes les vues
- ✅ Design cohérent avec Bootstrap

## 🔄 À ajouter dans vos vues existantes

Pour chaque vue (Dashboard, CityDetails, Simulation):

1. Ajouter en haut: `<?php require_once 'app/views/partials/frais-helper.php'; ?>`
2. Ajouter avant `</body>`: `<?php include_once 'app/views/partials/modal-edit-frais.php'; ?>`
3. Utiliser `<?php echo renderFraisButton($don); ?>` dans les tableaux

