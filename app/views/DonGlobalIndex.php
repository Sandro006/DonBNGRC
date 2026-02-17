<!DOCTYPE html>
<html class="light" lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dons Globaux - BNGRC</title>
    <link href="<?= Flight::get('flight.base_url') ?>/css/design-system.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/components.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/layout.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/utilities.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/pages.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/custom.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet" />
</head>

<body>
    <div class="layout">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">🌊</div>
                <div class="sidebar-brand-text">
                    <h2>BNGRC</h2>
                    <small>Management System</small>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="<?= Flight::get('flight.base_url') ?>/dashboard" class="sidebar-menu-link">
                        <i class="bi bi-speedometer2"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?= Flight::get('flight.base_url') ?>/don/ajouter" class="sidebar-menu-link">
                        <i class="bi bi-gift"></i>
                        <span>Ajouter don</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?= Flight::get('flight.base_url') ?>/don-global" class="sidebar-menu-link active">
                        <i class="bi bi-globe"></i>
                        <span>Dons Globaux</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?= Flight::get('flight.base_url') ?>/don-global/methodes" class="sidebar-menu-link">
                        <i class="bi bi-gear-fill"></i>
                        <span>Méthodes Distribution</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="layout-main">
            <!-- HEADER -->
            <header class="header">
                <div class="header-container">
                    <button class="sidebar-toggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 style="margin: 0; font-size: 1.25rem; font-weight: 700;">🌍 BNGRC - Dons Globaux</h1>
                    <div class="header-actions" style="margin-left: auto;">
                        <div class="header-user">
                            <div class="header-user-avatar">AD</div>
                            <span style="font-size: 0.875rem;">Admin</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- CONTENT -->
            <div class="layout-content">
                <!-- BREADCRUMB -->
                <div class="breadcrumb-nav">
                    <ol>
                        <li><a href="<?= Flight::get('flight.base_url') ?>">Accueil</a></li>
                        <li>Dons Globaux</li>
                    </ol>
                </div>

                <!-- PAGE HEADER -->
                <div class="page-header">
                    <div class="page-title">
                        <h1><i class="bi bi-globe"></i> Dons Globaux</h1>
                        <p>Gestion des dons non affectés à une ville spécifique</p>
                    </div>
                    <div class="page-actions">
                        <a href="<?= Flight::get('flight.base_url') ?>/don-global/methodes" class="btn btn-info">
                            <i class="bi bi-gear"></i>
                            Méthodes Distribution
                        </a>
                        <a href="<?= Flight::get('flight.base_url') ?>/don-global/create" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i>
                            Nouveau Don Global
                        </a>
                    </div>
                </div>

                <!-- SUCCESS MESSAGE -->
                <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
                    <div class="alert alert-success alert-dismissible">
                        <i class="bi bi-check-circle"></i>
                        <strong>Succès !</strong> Le don global a été ajouté avec succès.
                        <?php if (isset($_GET['id'])): ?>
                            <a href="<?= Flight::get('flight.base_url') ?>/don-global/<?= htmlspecialchars($_GET['id']) ?>" class="btn btn-outline-success btn-sm ms-2">
                                Voir les détails
                            </a>
                        <?php endif; ?>
                        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
                    </div>
                <?php endif; ?>

                <!-- STATISTIQUES -->
                <div class="row mb-4">
                    <?php if (!empty($stats)): ?>
                        <?php foreach ($stats as $stat): ?>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon bg-primary">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-number"><?= number_format($stat['quantite_totale'] ?? 0) ?></div>
                                        <div class="stat-label"><?= htmlspecialchars($stat['categorie']) ?></div>
                                        <small class="text-muted">
                                            <?= number_format($stat['quantite_disponible'] ?? 0) ?> disponible
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- LISTE DES DONS GLOBAUX -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-list"></i> Liste des Dons Globaux</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($dons_globaux)): ?>
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle"></i>
                                <strong>Aucun don global enregistré</strong><br>
                                <a href="<?= Flight::get('flight.base_url') ?>/don-global/create" class="btn btn-primary mt-2">
                                    <i class="bi bi-plus-circle"></i>
                                    Ajouter le premier don global
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><i class="bi bi-hash"></i> ID</th>
                                            <th><i class="bi bi-person"></i> Donateur</th>
                                            <th><i class="bi bi-tag"></i> Catégorie</th>
                                            <th><i class="bi bi-box"></i> Quantité</th>
                                            <th><i class="bi bi-calendar"></i> Date Don</th>
                                            <th><i class="bi bi-speedometer"></i> Statut</th>
                                            <th><i class="bi bi-gear"></i> Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dons_globaux as $don): ?>
                                            <?php 
                                            $statusClass = match($don['status_distribution']) {
                                                'disponible' => 'success',
                                                'distribue' => 'secondary',
                                                'reserve' => 'warning',
                                                default => 'secondary'
                                            };
                                            $statusIcon = match($don['status_distribution']) {
                                                'disponible' => 'check-circle',
                                                'distribue' => 'x-circle',
                                                'reserve' => 'dash-circle',
                                                default => 'question-circle'
                                            };
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong>#<?= $don['id'] ?></strong>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong><?= htmlspecialchars($don['donateur_nom']) ?></strong>
                                                        <?php if (!empty($don['donateur_telephone'])): ?>
                                                            <br><small class="text-muted">
                                                                <i class="bi bi-telephone"></i>
                                                                <?= htmlspecialchars($don['donateur_telephone']) ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?= htmlspecialchars($don['categorie_nom']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong><?= number_format($don['quantite']) ?></strong>
                                                </td>
                                                <td>
                                                    <?= date('d/m/Y H:i', strtotime($don['date_don'])) ?>
                                                    <br><small class="text-muted">
                                                        <?= date('d/m/Y', strtotime($don['date_don'])) ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $statusClass ?>">
                                                        <i class="bi bi-<?= $statusIcon ?>"></i>
                                                        <?= ucfirst($don['status_distribution']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?= Flight::get('flight.base_url') ?>/don-global/<?= $don['id'] ?>" 
                                                           class="btn btn-outline-primary" title="Voir détails">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <?php if ($don['status_distribution'] === 'disponible'): ?>
                                                            <button class="btn btn-outline-success" 
                                                                    onclick="suggestDistribution(<?= $don['categorie_id'] ?>)" 
                                                                    title="Suggestions de distribution">
                                                                <i class="bi bi-lightbulb"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- SECTION DISTRIBUTION -->
                <?php if (!empty($dons_globaux)): ?>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6><i class="bi bi-diagram-3"></i> Distribution Automatique</h6>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <p class="mb-0">
                                        <i class="bi bi-info-circle text-info"></i>
                                        Utilisez les <strong>méthodes de distribution</strong> pour optimiser 
                                        la répartition des dons selon vos critères.
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="<?= Flight::get('flight.base_url') ?>/don-global/methodes" class="btn btn-primary">
                                        <i class="bi bi-gear"></i>
                                        Configurer Distribution
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- FOOTER -->
            <footer class="layout-footer">
                <p>&copy; <?= date('Y') ?> Bureau National de Gestion des Risques et Catastrophes (BNGRC). Tous droits réservés.</p>
            </footer>
        </div>
    </div>

    <script>
        // Responsive sidebar toggle
        document.querySelector('.sidebar-toggle')?.addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('show');
        });

        // Suggestions de distribution pour une catégorie
        function suggestDistribution(categorieId) {
            fetch(`<?= Flight::get('flight.base_url') ?>/api/don-global/suggestions/${categorieId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    let message = "Suggestions de distribution :\n\n";
                    data.data.slice(0, 3).forEach((suggestion, index) => {
                        const besoin = suggestion.besoin;
                        message += `${index + 1}. ${besoin.ville_nom} (${besoin.region_nom})\n`;
                        message += `   Quantité: ${suggestion.quantite_satisfiable}/${besoin.quantite_manquante}\n`;
                        message += `   Attente: ${besoin.jours_attente} jours\n\n`;
                    });
                    message += "Voulez-vous aller aux méthodes de distribution ?";
                    
                    if (confirm(message)) {
                        window.location.href = '<?= Flight::get('flight.base_url') ?>/don-global/methodes';
                    }
                } else {
                    alert("Aucune suggestion de distribution disponible pour cette catégorie.");
                }
            })
            .catch(error => {
                alert("Erreur lors du chargement des suggestions: " + error.message);
            });
        }
    </script>
</body>

</html>