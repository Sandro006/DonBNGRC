<!DOCTYPE html>
<html class="light" lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Besoin - BNGRC</title>
    <link href="<?= Flight::get('flight.base_url') ?>/css/design-system.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/components.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/layout.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/utilities.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/pages.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/custom.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet" />
</head>

<body>
    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <!-- Main Layout -->
    <div class="layout">
        <!-- Main Content Area -->
        <div class="layout-main">
            <!-- Main Content -->
            <main class="layout-content">
            <!-- HEADER -->
            <header class="header">
                <div class="header-container">
                    <button class="sidebar-toggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 style="margin: 0; font-size: 1.25rem; font-weight: 700;">🆘 BNGRC - Nouveau Besoin</h1>
                    <div class="header-actions" style="margin-left: auto;">
                        <div class="header-user">
                            <div class="header-user-avatar">AD</div>
                            <span style="font-size: 0.875rem;">Admin</span>
                        </div>
                    </div>
                </div>
            </header>

                <!-- BREADCRUMB -->
                <div class="breadcrumb-nav">
                    <ol>
                        <li><a href="<?= Flight::get('flight.base_url') ?>">Accueil</a></li>
                        <li><a href="<?= Flight::get('flight.base_url') ?>/besoin">Besoins</a></li>
                        <li>Nouveau Besoin</li>
                    </ol>
                </div>

                <!-- PAGE HEADER -->
                <div class="page-header">
                    <div class="page-title">
                        <h1><i class="bi bi-exclamation-triangle"></i> Nouveau Besoin</h1>
                        <p>Enregistrez un nouveau besoin identifié par les équipes terrain</p>
                    </div>
                    <div class="page-actions">
                        <a href="<?= Flight::get('flight.base_url') ?>/besoin" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i>
                            Retour
                        </a>
                    </div>
                </div>

                <!-- FORM CARD -->
                <div class="card" style="max-width: 800px; margin: 0 auto;">
                    <form method="post" action="<?= Flight::get('flight.base_url') ?>/besoin/store" id="besoinForm">
                        <!-- SECTION 1: INFORMATIONS DU BESOIN -->
                        <div class="card-header bg-warning">
                            <h5 style="margin: 0; color: white; display: flex; align-items: center; gap: var(--spacing-2);">
                                <i class="bi bi-exclamation-triangle"></i>
                                Informations du Besoin
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                <strong>Besoin identifié:</strong> Ce formulaire permet d'enregistrer un nouveau besoin 
                                identifié sur le terrain. Ces besoins seront utilisés pour orienter la distribution des dons.
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label required">Ville</label>
                                    <select name="ville_id" class="form-select" required>
                                        <option value="">-- Sélectionnez une ville --</option>
                                        <?php 
                                        $current_region = '';
                                        foreach ($villes as $v) { 
                                            if ($current_region !== $v['region_nom']) {
                                                if ($current_region !== '') echo '</optgroup>';
                                                $current_region = $v['region_nom'];
                                                echo '<optgroup label="' . htmlspecialchars($current_region) . '">';
                                            }
                                        ?>
                                            <option value="<?= htmlspecialchars($v['id']) ?>"><?= htmlspecialchars($v['nom']) ?></option>
                                        <?php } 
                                        if ($current_region !== '') echo '</optgroup>';
                                        ?>
                                    </select>
                                    <span class="form-helper">Sélectionnez la ville concernée par ce besoin</span>
                                </div>

                                <div class="form-group">
                                    <label class="form-label required">Catégorie</label>
                                    <select name="categorie_id" class="form-select" required>
                                        <option value="">-- Sélectionnez une catégorie --</option>
                                        <?php foreach ($categories as $c) { ?>
                                            <option value="<?= htmlspecialchars($c['id']) ?>"><?= htmlspecialchars($c['libelle']) ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="form-helper">Type de besoin identifié</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Description du besoin</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Décrivez précisément le besoin identifié..." required></textarea>
                                <span class="form-helper">Description détaillée du besoin (ex: eau potable pour 500 familles, médicaments urgents...)</span>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label required">Quantité</label>
                                    <input type="number" name="quantite" class="form-control" min="1" step="0.01" placeholder="0" required />
                                    <span class="form-helper">Quantité nécessaire</span>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Prix unitaire estimé (Ar)</label>
                                    <input type="number" name="prix_unitaire" class="form-control" min="0" step="0.01" placeholder="0" />
                                    <span class="form-helper">Coût estimé par unité (optionnel)</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Date du besoin</label>
                                <input type="date" name="date_besoin" class="form-control" value="<?= date('Y-m-d') ?>" />
                                <span class="form-helper">Date d'identification du besoin</span>
                            </div>
                        </div>

                        <!-- CARD FOOTER -->
                        <div class="card-footer">
                            <div style="display: flex; gap: var(--spacing-3); justify-content: flex-end;">
                                <a href="<?= Flight::get('flight.base_url') ?>/besoin" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i>
                                    Annuler
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-check-circle"></i>
                                    Enregistrer le Besoin
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript validation -->
    <script>
        document.getElementById('besoinForm').addEventListener('submit', function(e) {
            // Basic client-side validation
            const requiredFields = document.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            // Validate quantity is positive
            const quantite = document.querySelector('input[name="quantite"]');
            if (quantite && (isNaN(quantite.value) || quantite.value <= 0)) {
                quantite.classList.add('is-invalid');
                isValid = false;
            }
            
            // Validate prix_unitaire if provided
            const prix = document.querySelector('input[name="prix_unitaire"]');
            if (prix && prix.value && (isNaN(prix.value) || prix.value < 0)) {
                prix.classList.add('is-invalid');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Veuillez corriger les erreurs dans le formulaire');
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Enregistrement...';
        });
        
        // Remove invalid class on input
        document.querySelectorAll('.form-control, .form-select').forEach(field => {
            field.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    </script>

</body>

</html>