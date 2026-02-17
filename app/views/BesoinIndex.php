<!DOCTYPE html>
<html class="light" lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Besoins - BNGRC</title>
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
            <!-- HEADER -->
            <header class="header">
                <div class="header-container">
                    <button class="sidebar-toggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 style="margin: 0; font-size: 1.25rem; font-weight: 700;">🆘 BNGRC - Liste des Besoins</h1>
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
                        <li>Besoins</li>
                    </ol>
                </div>

                <!-- PAGE HEADER -->
                <div class="page-header">
                    <div class="page-title">
                        <h1><i class="bi bi-exclamation-triangle"></i> Liste des Besoins</h1>
                        <p>Gestion des besoins identifiés par les équipes terrain</p>
                    </div>
                    <div class="page-actions">
                        <a href="<?= Flight::get('flight.base_url') ?>/besoin/create" class="btn btn-warning">
                            <i class="bi bi-plus-circle"></i>
                            Nouveau Besoin
                        </a>
                    </div>
                </div>

                <!-- SUCCESS MESSAGE -->
                <?php if (isset($_GET['message']) && $_GET['message'] === 'success'): ?>
                    <div class="alert alert-success alert-dismissible">
                        <i class="bi bi-check-circle"></i>
                        <strong>Succès !</strong> Le besoin a été enregistré avec succès.
                        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
                    </div>
                <?php endif; ?>

                <!-- LISTE DES BESOINS -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-list"></i> Liste des Besoins Identifiés</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($besoins)): ?>
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle"></i>
                                <strong>Aucun besoin enregistré</strong><br>
                                <a href="<?= Flight::get('flight.base_url') ?>/besoin/create" class="btn btn-warning mt-2">
                                    <i class="bi bi-plus-circle"></i>
                                    Enregistrer le premier besoin
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><i class="bi bi-hash"></i> ID</th>
                                            <th><i class="bi bi-geo-alt"></i> Ville</th>
                                            <th><i class="bi bi-map"></i> Région</th>
                                            <th><i class="bi bi-tag"></i> Catégorie</th>
                                            <th><i class="bi bi-card-text"></i> Description</th>
                                            <th><i class="bi bi-box"></i> Quantité</th>
                                            <th><i class="bi bi-currency-exchange"></i> Prix Unit.</th>
                                            <th><i class="bi bi-calculator"></i> Montant</th>
                                            <th><i class="bi bi-flag"></i> Priorité</th>
                                            <th><i class="bi bi-speedometer"></i> Statut</th>
                                            <th><i class="bi bi-calendar"></i> Date</th>
                                            <th><i class="bi bi-gear"></i> Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($besoins as $besoin): ?>
                                            <?php 
                                            $prioriteClass = match($besoin['priorite'] ?? 'normale') {
                                                'urgente' => 'danger',
                                                'haute' => 'warning',
                                                'normale' => 'info',
                                                'basse' => 'secondary',
                                                default => 'secondary'
                                            };
                                            $prioriteIcon = match($besoin['priorite'] ?? 'normale') {
                                                'urgente' => 'exclamation-triangle-fill',
                                                'haute' => 'exclamation-circle',
                                                'normale' => 'dash-circle',
                                                'basse' => 'arrow-down-circle',
                                                default => 'question-circle'
                                            };
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong>#<?= $besoin['id'] ?></strong>
                                                </td>
                                                <td>
                                                    <strong><?= htmlspecialchars($besoin['ville_nom'] ?? '-') ?></strong>
                                                </td>
                                                <td>
                                                    <span class="text-muted"><?= htmlspecialchars($besoin['region_nom'] ?? '-') ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?= htmlspecialchars($besoin['categorie_nom'] ?? '-') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span title="<?= htmlspecialchars($besoin['description'] ?? '') ?>">
                                                        <?= htmlspecialchars(substr($besoin['description'] ?? '', 0, 30)) ?><?= strlen($besoin['description'] ?? '') > 30 ? '...' : '' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong><?= number_format($besoin['quantite'] ?? 0) ?></strong>
                                                </td>
                                                <td>
                                                    <?= !empty($besoin['prix_unitaire']) 
                                                        ? number_format($besoin['prix_unitaire'], 0, ',', ' ') . ' <small class="text-muted">Ar</small>' 
                                                        : '<span class="text-muted">-</span>' 
                                                    ?>
                                                </td>
                                                <td>
                                                    <strong><?= number_format($besoin['montant_total'] ?? 0, 0, ',', ' ') ?></strong>
                                                    <small class="text-muted">Ar</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $prioriteClass ?>">
                                                        <i class="bi bi-<?= $prioriteIcon ?>"></i>
                                                        <?= htmlspecialchars(ucfirst($besoin['priorite'] ?? 'normale')) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?= htmlspecialchars($besoin['status_nom'] ?? '-') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small><?= date('d/m/Y', strtotime($besoin['date_besoin'] ?? 'now')) ?></small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?= Flight::get('flight.base_url') ?>/besoin/<?= $besoin['id'] ?>" 
                                                           class="btn btn-outline-primary btn-sm" 
                                                           title="Voir détails">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
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
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="<?= Flight::get('flight.base_url') ?>/js/bootstrap.bundle.min.js"></script>
    <script src="<?= Flight::get('flight.base_url') ?>/js/app.js"></script>
</body>

</html>
