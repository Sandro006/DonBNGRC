<!DOCTYPE html>
<html class="light" lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Don Global - BNGRC</title>
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
                    <h1 style="margin: 0; font-size: 1.25rem; font-weight: 700;">🌍 BNGRC - Nouveau Don Global</h1>
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
                        <li><a href="<?= Flight::get('flight.base_url') ?>/don-global">Dons Globaux</a></li>
                        <li>Nouveau Don</li>
                    </ol>
                </div>

                <!-- PAGE HEADER -->
                <div class="page-header">
                    <div class="page-title">
                        <h1><i class="bi bi-globe"></i> Nouveau Don Global</h1>
                        <p>Un don global sera distribué automatiquement selon les méthodes de distribution configurées</p>
                    </div>
                    <div class="page-actions">
                        <a href="<?= Flight::get('flight.base_url') ?>/don-global" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i>
                            Retour
                        </a>
                    </div>
                </div>

                <!-- FORM CARD -->
                <div class="card" style="max-width: 800px; margin: 0 auto;">
                    <form method="post" action="<?= Flight::get('flight.base_url') ?>/don-global/store" id="donGlobalForm">
                        <!-- SECTION 1: INFORMATIONS DU DON -->
                        <div class="card-header bg-success">
                            <h5 style="margin: 0; color: white; display: flex; align-items: center; gap: var(--spacing-2);">
                                <i class="bi bi-globe"></i>
                                Informations du Don Global
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                <strong>Don Global:</strong> Ce don ne sera pas affecté à une ville spécifique. 
                                Il sera distribué automatiquement aux villes ayant les besoins les plus prioritaires 
                                selon la méthode de distribution choisie.
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Catégorie</label>
                                <select name="categorie_id" class="form-select" required>
                                    <option value="">-- Sélectionnez une catégorie --</option>
                                    <?php foreach ($categories as $c) { ?>
                                        <option value="<?= htmlspecialchars($c['id']) ?>"><?= htmlspecialchars($c['libelle']) ?></option>
                                    <?php } ?>
                                </select>
                                <span class="form-helper">Sélectionnez le type de bien donné</span>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label required">Quantité</label>
                                    <input type="number" name="quantite" class="form-control" min="1" placeholder="0" required />
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Date du don</label>
                                    <input type="datetime-local" name="date_don" class="form-control" />
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: INFORMATIONS DU DONATEUR -->
                        <div class="card-header bg-success" style="margin-top: var(--spacing-6);">
                            <h5 style="margin: 0; color: white; display: flex; align-items: center; gap: var(--spacing-2);">
                                <i class="bi bi-person-check"></i>
                                Informations du Donateur
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">Sélectionner un donateur existant</label>
                                <select name="donateur_id" class="form-select">
                                    <option value="">-- Créer un nouveau donateur --</option>
                                    <?php foreach ($donateurs as $d) { ?>
                                        <option value="<?= htmlspecialchars($d['id']) ?>">
                                            <?= htmlspecialchars($d['nom']) ?>
                                            <?= !empty($d['telephone']) ? '(' . htmlspecialchars($d['telephone']) . ')' : '' ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <span class="form-helper">Ou remplissez les informations ci-dessous pour créer un nouveau donateur</span>
                            </div>

                            <div style="background: linear-gradient(135deg, var(--success-50) 0%, rgba(34, 197, 94, 0.05) 100%); padding: var(--spacing-4); border-radius: var(--radius-lg); margin: var(--spacing-4) 0; border-left: 4px solid var(--success);">
                                <p class="text-muted" style="margin: 0; font-size: var(--font-size-sm); color: var(--text-secondary);">
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Note:</strong> Ces informations ne seront utilisées que si vous n'avez pas sélectionné de donateur existant
                                </p>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Nom complet</label>
                                    <input type="text" name="donateur_nom" class="form-control" placeholder="Ex: Jean Dupont" />
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Téléphone</label>
                                    <input type="tel" name="donateur_telephone" class="form-control" placeholder="Ex: +261 34 40 40 40" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" name="donateur_email" class="form-control" placeholder="Ex: email@exemple.com" />
                            </div>
                        </div>

                        <!-- ACTIONS -->
                        <div class="card-footer" style="display: flex; gap: var(--spacing-3); justify-content: flex-end;">
                            <a href="<?= Flight::get('flight.base_url') ?>/don-global" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i>
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-globe"></i>
                                Enregistrer le Don Global
                            </button>
                        </div>
                    </form>
                </div>

                <!-- INFORMATION SUR LA DISTRIBUTION -->
                <div class="card mt-4" style="max-width: 800px; margin: 0 auto;">
                    <div class="card-header">
                        <h6><i class="bi bi-question-circle"></i> Comment sera distribué ce don ?</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>🎯 Distribution Intelligente</h6>
                                <p><small>Le don sera automatiquement attribué aux villes avec les besoins les plus prioritaires dans cette catégorie.</small></p>
                                
                                <h6>⚙️ Méthodes Disponibles</h6>
                                <ul>
                                    <li><small><strong>Par Date:</strong> Plus ancien besoin en premier</small></li>
                                    <li><small><strong>Par Urgence:</strong> Plus de jours d'attente</small></li>
                                    <li><small><strong>Par Région:</strong> Zones prioritaires</small></li>
                                    <li><small><strong>Équilibrée:</strong> Répartition homogène</small></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>📊 Traçabilité Complète</h6>
                                <p><small>Toutes les distributions sont enregistrées avec les détails complets pour assurer une totale transparence.</small></p>
                                
                                <h6>🔄 Flexible</h6>
                                <p><small>Vous pourrez toujours consulter et modifier les méthodes de distribution dans les paramètres.</small></p>
                                
                                <div class="mt-3">
                                    <a href="<?= Flight::get('flight.base_url') ?>/don-global/methodes" class="btn btn-info btn-sm">
                                        <i class="bi bi-gear"></i>
                                        Configurer les Méthodes
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

    <script>
        // Responsive sidebar toggle
        document.querySelector('.sidebar-toggle')?.addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('show');
        });

        // Simple form validation
        document.getElementById('donGlobalForm')?.addEventListener('submit', function(e) {
            const categorie = document.querySelector('select[name="categorie_id"]').value;
            const quantite = document.querySelector('input[name="quantite"]').value;
            
            if (!categorie) {
                e.preventDefault();
                alert('Veuillez sélectionner une catégorie');
                return false;
            }
            
            if (!quantite || quantite < 1) {
                e.preventDefault();
                alert('Veuillez entrer une quantité valide');
                return false;
            }
            
            // Validation donateur
            const donateurId = document.querySelector('select[name="donateur_id"]').value;
            const donateurNom = document.querySelector('input[name="donateur_nom"]').value;
            
            if (!donateurId && !donateurNom) {
                e.preventDefault();
                alert('Veuillez soit sélectionner un donateur existant, soit remplir le nom pour créer un nouveau donateur');
                return false;
            }
        });
        
        // Auto-clear donateur fields when selecting existing donateur
        document.querySelector('select[name="donateur_id"]')?.addEventListener('change', function() {
            if (this.value) {
                document.querySelector('input[name="donateur_nom"]').value = '';
                document.querySelector('input[name="donateur_telephone"]').value = '';
                document.querySelector('input[name="donateur_email"]').value = '';
            }
        });
    </script>
</body>

</html>