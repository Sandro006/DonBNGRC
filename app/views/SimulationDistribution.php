<!DOCTYPE html>
<html class="light" lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulation de Distribution - BNGRC</title>
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

    <!-- MAIN CONTENT -->
    <div class="layout-content" style="padding: var(--spacing-8); min-height: calc(100vh - 200px);">
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
                    <a href="<?= Flight::get('flight.base_url') ?>/don-global" class="sidebar-menu-link">
                        <i class="bi bi-box-seam"></i>
                        <span>Dons Globaux</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?= Flight::get('flight.base_url') ?>/don-global" class="sidebar-menu-link">
                        <i class="bi bi-globe"></i>
                        <span>Dons Globaux</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?= Flight::get('flight.base_url') ?>/don-global/simulation" class="sidebar-menu-link active">
                        <i class="bi bi-diagram-3"></i>
                        <span>Simulation</span>
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
                    <h1 style="margin: 0; font-size: 1.25rem; font-weight: 700;">📊 BNGRC - Simulation de Distribution</h1>
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
                        <li><a href="<?= Flight::get('flight.base_url') ?>/don-global">Dons Globaux</a></li>
                        <li>Simulation</li>
                    </ol>
                </div>

                <!-- PAGE HEADER -->
                <div class="page-header">
                    <div class="page-title">
                        <h1><i class="bi bi-diagram-3-fill"></i> Simulation de Distribution</h1>
                        <p>
                            <strong>Méthode utilisée:</strong> 
                            <span class="badge bg-primary"><?= ucfirst($methode_courante ?? 'date') ?></span>
                            <?php if (isset($available_methods) && isset($methode_courante) && isset($available_methods[$methode_courante])): ?>
                                - <?= htmlspecialchars($available_methods[$methode_courante]['nom']) ?>
                            <?php endif; ?>
                        </p>
                        <?php if ($just_executed ?? false): ?>
                            <div class="alert alert-success mt-2">
                                <i class="bi bi-check-circle"></i>
                                <strong>Distribution exécutée avec succès!</strong> 
                                <?= $nb_distributions ?> distributions ont été effectuées.
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="page-actions">
                        <a href="<?= Flight::get('flight.base_url') ?>/don-global/methodes" class="btn btn-secondary">
                            <i class="bi bi-gear"></i>
                            Changer de Méthode
                        </a>
                        <button type="button" class="btn btn-success" id="executeDistribution">
                            <i class="bi bi-play-circle"></i>
                            Exécuter cette Distribution
                        </button>
                        <button type="button" class="btn btn-primary" onclick="location.reload()">
                            <i class="bi bi-arrow-clockwise"></i>
                            Actualiser
                        </button>
                    </div>
                </div>

                <!-- RESUME GLOBAL -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stat-card bg-primary">
                            <div class="stat-icon"><i class="bi bi-clipboard-check"></i></div>
                            <div class="stat-content">
                                <div class="stat-number"><?= $simulation['resume']['besoins']['total'] ?? 0 ?></div>
                                <div class="stat-label">Besoins à traiter</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-success">
                            <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                            <div class="stat-content">
                                <div class="stat-number"><?= $simulation['resume']['besoins']['satisfaits'] ?? 0 ?></div>
                                <div class="stat-label">Besoins satisfaits</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-warning">
                            <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
                            <div class="stat-content">
                                <div class="stat-number"><?= $simulation['resume']['besoins']['partiellement_satisfaits'] ?? 0 ?></div>
                                <div class="stat-label">Partiellement satisfaits</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-info">
                            <div class="stat-icon"><i class="bi bi-gift"></i></div>
                            <div class="stat-content">
                                <div class="stat-number"><?= $simulation['resume']['dons']['total_disponibles'] ?? 0 ?></div>
                                <div class="stat-label">Dons disponibles</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DETAILS DE LA SIMULATION -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-list-check"></i> Résultats de la Simulation par Priorité</h5>
                        <small class="text-muted">Ordre de traitement basé sur la date de besoin (plus ancienne = plus prioritaire)</small>
                    </div>
                    <div class="card-body">
                        <?php if (empty($simulation['details'])): ?>
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle"></i>
                                <strong>Aucun besoin à distribuer</strong><br>
                                Soit tous les besoins sont déjà satisfaits, soit aucun don global n'est disponible.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><i class="bi bi-hash"></i> Priorité</th>
                                            <th><i class="bi bi-geo-alt"></i> Ville / Région</th>
                                            <th><i class="bi bi-tag"></i> Catégorie</th>
                                            <th><i class="bi bi-calendar"></i> Date Besoin</th>
                                            <th><i class="bi bi-hourglass"></i> Jours d'attente</th>
                                            <th><i class="bi bi-box"></i> Quantité Demandée</th>
                                            <th><i class="bi bi-check-square"></i> Quantité Satisfaite</th>
                                            <th><i class="bi bi-speedometer"></i> Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($simulation['details'] as $index => $result): ?>
                                            <?php 
                                            $besoin = $result['besoin_info'];
                                            $statusClass = match($result['statut_final']) {
                                                'satisfait' => 'success',
                                                'partiellement_satisfait' => 'warning', 
                                                'non_satisfait' => 'danger',
                                                default => 'secondary'
                                            };
                                            $statusIcon = match($result['statut_final']) {
                                                'satisfait' => 'check-circle-fill',
                                                'partiellement_satisfait' => 'exclamation-triangle-fill', 
                                                'non_satisfait' => 'x-circle-fill',
                                                default => 'question-circle-fill'
                                            };
                                            ?>
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary">#<?= $index + 1 ?></span>
                                                </td>
                                                <td>
                                                    <strong><?= htmlspecialchars($besoin['ville_nom']) ?></strong><br>
                                                    <small class="text-muted"><?= htmlspecialchars($besoin['region_nom']) ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info"><?= htmlspecialchars($besoin['categorie_nom']) ?></span>
                                                </td>
                                                <td>
                                                    <?= date('d/m/Y H:i', strtotime($besoin['date_besoin'])) ?><br>
                                                    <small class="text-muted"><?= date('d/m/Y', strtotime($besoin['date_besoin'])) ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $besoin['jours_attente'] > 30 ? 'danger' : ($besoin['jours_attente'] > 15 ? 'warning' : 'success') ?>">
                                                        <?= $besoin['jours_attente'] ?> jours
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong><?= number_format($besoin['quantite_manquante']) ?></strong>
                                                </td>
                                                <td>
                                                    <strong class="text-success"><?= number_format($result['quantite_satisfaite']) ?></strong>
                                                    <?php if ($result['quantite_satisfaite'] > 0): ?>
                                                        <br><small class="text-muted">
                                                            <?= round($result['pourcentage_satisfaction'], 1) ?>%
                                                        </small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $statusClass ?>">
                                                        <i class="bi bi-<?= $statusIcon ?>"></i>
                                                        <?= ucfirst(str_replace('_', ' ', $result['statut_final'])) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            
                                            <?php if (!empty($result['distributions'])): ?>
                                                <tr class="table-light">
                                                    <td colspan="8">
                                                        <div class="ms-3">
                                                            <small><strong><i class="bi bi-arrow-right"></i> Distributions prévues:</strong></small>
                                                            <div class="row mt-2">
                                                                <?php foreach ($result['distributions'] as $dist): ?>
                                                                    <div class="col-md-6 mb-2">
                                                                        <div class="border rounded p-2 bg-white">
                                                                            <small>
                                                                                <strong>Don #<?= $dist['don_global_id'] ?></strong> - 
                                                                                <?= htmlspecialchars($dist['don_info']['donateur_nom'] ?? 'Donateur inconnu') ?><br>
                                                                                <i class="bi bi-box"></i> <?= number_format($dist['quantite_distribuee']) ?> unités<br>
                                                                                <i class="bi bi-calendar"></i> Don du <?= date('d/m/Y', strtotime($dist['don_info']['date_don'])) ?>
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                            
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- STATISTIQUES DETAILLEES -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6><i class="bi bi-bar-chart"></i> Statistiques des Besoins</h6>
                            </div>
                            <div class="card-body">
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-success" style="width: <?= $simulation['resume']['besoins']['total'] > 0 ? ($simulation['resume']['besoins']['satisfaits'] / $simulation['resume']['besoins']['total'] * 100) : 0 ?>%"></div>
                                </div>
                                <ul class="list-unstyled">
                                    <li><i class="bi bi-check-circle text-success"></i> Satisfaits: <strong><?= $simulation['resume']['besoins']['satisfaits'] ?></strong></li>
                                    <li><i class="bi bi-exclamation-triangle text-warning"></i> Partiels: <strong><?= $simulation['resume']['besoins']['partiellement_satisfaits'] ?></strong></li>
                                    <li><i class="bi bi-x-circle text-danger"></i> Non satisfaits: <strong><?= $simulation['resume']['besoins']['non_satisfaits'] ?></strong></li>
                                </ul>
                                <small class="text-muted">
                                    Taux de satisfaction: <?= round($simulation['resume']['besoins']['pourcentage_satisfaction'] ?? 0, 1) ?>%
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6><i class="bi bi-gift"></i> Utilisation des Dons</h6>
                            </div>
                            <div class="card-body">
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-info" style="width: <?= round($simulation['resume']['dons']['pourcentage_utilisation'] ?? 0, 1) ?>%"></div>
                                </div>
                                <ul class="list-unstyled">
                                    <li><i class="bi bi-check-circle text-success"></i> Utilisés entièrement: <strong><?= $simulation['resume']['dons']['utilises_completement'] ?></strong></li>
                                    <li><i class="bi bi-dash-circle text-warning"></i> Utilisés partiellement: <strong><?= $simulation['resume']['dons']['utilises_partiellement'] ?></strong></li>
                                    <li><i class="bi bi-circle text-muted"></i> Non utilisés: <strong><?= $simulation['resume']['dons']['non_utilises'] ?></strong></li>
                                </ul>
                                <small class="text-muted">
                                    Taux d'utilisation: <?= round($simulation['resume']['dons']['pourcentage_utilisation'] ?? 0, 1) ?>%
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- INFO SIMULATION -->
                <div class="alert alert-info mt-4">
                    <h6><i class="bi bi-info-circle"></i> Comment fonctionne la simulation ?</h6>
                    <ul class="mb-0">
                        <li><strong>Priorité par date:</strong> Les besoins avec la date la plus ancienne sont traités en premier</li>
                        <li><strong>Distribution optimisée:</strong> Pour chaque besoin, les dons de la même catégorie sont distribués automatiquement</li>
                        <li><strong>Ordre des dons:</strong> Les dons les plus anciens sont utilisés en premier</li>
                        <li><strong>Simulation seulement:</strong> Aucune modification en base de données jusqu'à l'exécution</li>
                    </ul>
                </div>

            </div>

            <!-- FOOTER -->
            <footer class="layout-footer">
                <p>&copy; <?= date('Y') ?> Bureau National de Gestion des Risques et Catastrophes (BNGRC). Tous droits réservés.</p>
            </footer>
        </div>

        <?php include __DIR__ . '/partials/footer.php'; ?>

    <script>
        // Responsive sidebar toggle
        document.querySelector('.sidebar-toggle')?.addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('show');
        });

        // Exécuter la distribution
        document.getElementById('executeDistribution')?.addEventListener('click', function () {
            if (!confirm('Êtes-vous sûr de vouloir exécuter cette distribution ?\n\nCette action distribuera réellement les dons selon la simulation et ne peut pas être annulée.')) {
                return;
            }

            const button = this;
            const originalText = button.innerHTML;
            
            // Désactiver le bouton et afficher le loading
            button.disabled = true;
            button.innerHTML = '<i class="bi bi-hourglass-split"></i> Exécution en cours...';
            
            fetch('<?= Flight::get('flight.base_url') ?>/don-global/execute-distribution', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    methode: '<?= $methode_courante ?? 'date' ?>',
                    parametres: <?= json_encode($parametres_courants ?? []) ?>
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Distribution exécutée avec succès!\n\n${data.result.nombre_distributions} distributions effectuées.`);
                    // Redirect to same page with executed flag
                    const params = new URLSearchParams({
                        methode: '<?= $methode_courante ?? 'date' ?>',
                        executed: '1',
                        distributions: data.result.nombre_distributions
                    });
                    window.location.href = '<?= Flight::get('flight.base_url') ?>/don-global/simulation?' + params.toString();
                } else {
                    alert('Erreur lors de l\'exécution: ' + data.message);
                }
            })
            .catch(error => {
                alert('Erreur réseau: ' + error.message);
            })
            .finally(() => {
                // Réactiver le bouton
                button.disabled = false;
                button.innerHTML = originalText;
            });
        });
    </script>
</body>

</html>