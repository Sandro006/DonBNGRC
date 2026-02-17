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
                    <a href="<?= Flight::get('flight.base_url') ?>/don/ajouter" class="sidebar-menu-link">
                        <i class="bi bi-gift"></i>
                        <span>Ajouter don</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?= Flight::get('flight.base_url') ?>/simulation" class="sidebar-menu-link">
                        <i class="bi bi-diagram-3"></i>
                        <span>Simulation</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Layout -->
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
                        <a href="<?= Flight::get('flight.base_url') ?>/don/ajouter?ville_id=<?= htmlspecialchars($ville['id'] ?? '') ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Ajouter Don
                        </a>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-row" style="margin-bottom: var(--spacing-8);">
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: var(--primary-100); color: var(--primary);">
                            <i class="bi bi-gift"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">Total Dons</span>
                            <span class="stat-value"><?= count($dons ?? []) ?></span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: var(--success-100); color: var(--success);">
                            <i class="bi bi-hand-thumbs-up"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">Total Besoins</span>
                            <span class="stat-value"><?= count($besoins ?? []) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); gap: var(--spacing-8);">
                    <!-- Donations Section -->
                    <div>
                        <div class="sim-section-title">
                            <i class="bi bi-gift"></i>
                            <h3>Dons pour <?= htmlspecialchars($ville['nom'] ?? '') ?></h3>
                        </div>

                        <div class="card">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Donateur</th>
                                            <th>Catégorie</th>
                                            <th>Quantité</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($dons)): ?>
                                            <?php foreach ($dons as $d): ?>
                                                <tr>
                                                    <td>
                                                        <span style="font-size: var(--font-size-sm);">
                                                            <?= date('d/m/Y', strtotime($d['date_don'] ?? 'now')) ?>
                                                        </span>
                                                    </td>
                                                    <td><?= htmlspecialchars($d['donateur_nom'] ?? '') ?></td>
                                                    <td>
                                                        <span class="badge badge-primary">
                                                            <?= htmlspecialchars($d['categorie_nom'] ?? '') ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($d['quantite'] ?? '') ?></strong>
                                                    </td>
                                                    <td>
                                                        <div style="display: flex; gap: var(--spacing-2);">
                                                            <a href="<?= Flight::get('flight.base_url') ?>/simulation/<?= htmlspecialchars($d['id'] ?? '') ?>" 
                                                               class="btn btn-sm btn-secondary" title="Simuler la distribution">
                                                                <i class="bi bi-arrow-repeat"></i>
                                                            </a>
                                                            <form method="POST" action="<?= Flight::get('flight.base_url') ?>/don/supprimer/<?= htmlspecialchars($d['id'] ?? '') ?>" 
                                                                  style="display: inline;">
                                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce don ?');"
                                                                        title="Supprimer ce don">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" style="text-align: center; padding: var(--spacing-6) 0;">
                                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: var(--spacing-2);">
                                                        <i class="bi bi-inbox" style="font-size: 1.875rem; color: var(--text-tertiary);"></i>
                                                        <p style="color: var(--text-secondary);">Aucun don pour cette ville</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

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
                    </div>
                </div>

                <!-- Footer -->
                <footer class="layout-footer" style="margin-top: auto;">
                    <p>&copy; <?= date('Y') ?> Bureau National de Gestion des Risques et Catastrophes (BNGRC). Tous droits réservés.</p>
                </footer>