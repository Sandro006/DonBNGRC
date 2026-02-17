<!DOCTYPE html>
<html class="light" lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails - Ville</title>
    
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
            <!-- Header -->
            <header class="header">
                <div class="header-container">
                    <button class="sidebar-toggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 style="margin: 0; font-size: 1.25rem; font-weight: 700;">
                        <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($ville['nom'] ?? 'Ville') ?>
                    </h1>
                    <div class="header-actions" style="margin-left: auto;">
                        <a href="<?= Flight::get('flight.base_url') ?>" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="layout-content">
                <!-- Breadcrumb -->
                <div class="breadcrumb-nav">
                    <ol>
                        <li><a href="<?= Flight::get('flight.base_url') ?>">Accueil</a></li>
                        <li><a href="<?= Flight::get('flight.base_url') ?>/dashboard">Tableau de bord</a></li>
                        <li><?= htmlspecialchars($ville['nom'] ?? 'Ville') ?></li>
                    </ol>
                </div>

                <!-- Page Header -->
                <div class="page-header" style="margin-bottom: var(--spacing-8);">
                    <div class="page-title">
                        <h2 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-2);">
                            <i class="bi bi-geo-alt-fill"></i>
                            <?= htmlspecialchars($ville['nom'] ?? 'N/A') ?>
                        </h2>
                        <p style="margin: var(--spacing-2) 0 0 0; color: var(--text-secondary);">
                            Région: <strong><?= htmlspecialchars($ville['region_nom'] ?? '') ?></strong>
                        </p>
                    </div>
                    <div class="page-actions">
                        <a href="<?= Flight::get('flight.base_url') ?>/don-global" class="btn btn-primary">
                            <i class="bi bi-box-seam"></i> Voir Dons Globaux
                        </a>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-row" style="margin-bottom: var(--spacing-8);">
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: var(--success-100); color: var(--success);">
                            <i class="bi bi-hand-thumbs-up"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">Total Besoins</span>
                            <span class="stat-value"><?= count($besoins ?? []) ?></span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: var(--info-100); color: var(--info);">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">Dons Globaux</span>
                            <span class="stat-value">Gérer par région</span>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div style="display: grid; grid-template-columns: 1fr; gap: var(--spacing-8);">
                    <!-- Needs Section -->
                    <div>
                        <div class="sim-section-title">
                            <i class="bi bi-hand-thumbs-up"></i>
                            <h3>Besoins pour <?= htmlspecialchars($ville['nom'] ?? '') ?></h3>
                        </div>

                        <div class="card">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Catégorie</th>
                                            <th>Quantité</th>
                                            <th>Montant</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($besoins)): ?>
                                            <?php foreach ($besoins as $b): 
                                                $statusBadgeClass = match(strtolower($b['status_nom'] ?? '')) {
                                                    'pending' => 'badge-warning',
                                                    'satisfied' => 'badge-success',
                                                    'cancelled' => 'badge-danger',
                                                    default => 'badge-secondary'
                                                };
                                            ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($b['categorie_nom'] ?? '') ?></td>
                                                    <td><strong><?= htmlspecialchars($b['quantite'] ?? '-') ?></strong></td>
                                                    <td>
                                                        <?= isset($b['montant_total']) ? number_format((float)$b['montant_total'], 2, ',', '.') . ' Ar' : '-' ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge <?= $statusBadgeClass ?>">
                                                            <?= htmlspecialchars($b['status_nom'] ?? 'N/A') ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" style="text-align: center; padding: var(--spacing-6) 0;">
                                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: var(--spacing-2);">
                                                        <i class="bi bi-inbox" style="font-size: 1.875rem; color: var(--text-tertiary);"></i>
                                                        <p style="color: var(--text-secondary);">Aucun besoin enregistré pour cette ville</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Information about Global Donations -->
                        <div class="card" style="margin-top: var(--spacing-6);">
                            <div class="card-body">
                                <h5 style="color: var(--info); display: flex; align-items: center; gap: var(--spacing-2);">
                                    <i class="bi bi-info-circle"></i>
                                    Information sur les Dons
                                </h5>
                                <p style="color: var(--text-secondary); margin: var(--spacing-3) 0 0 0;">
                                    Les dons sont désormais gérés de manière globale au niveau régional pour optimiser la distribution. 
                                    Consultez la section <a href="<?= Flight::get('flight.base_url') ?>/don-global" style="color: var(--primary);">Dons Globaux</a> 
                                    pour voir tous les dons disponibles.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <footer class="layout-footer" style="margin-top: auto;">
                    <p>&copy; <?= date('Y') ?> Bureau National de Gestion des Risques et Catastrophes (BNGRC). Tous droits réservés.</p>
                </footer>