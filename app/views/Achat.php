<!DOCTYPE html>
<html class="light" lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achat - BNGRC</title>
    
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
        <!-- Sidebar -->
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
                    <a href="<?= Flight::get('flight.base_url') ?>/achat/non-argent" class="sidebar-menu-link active">
                        <i class="bi bi-bag"></i>
                        <span>Achat</span>
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
                        <i class="bi bi-bag"></i> Achat
                    </h1>
                    <div class="header-actions" style="margin-left: auto;">
                        <a href="<?= Flight::get('flight.base_url') ?>/dashboard" class="btn btn-secondary btn-sm">
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
                        <li>Achat</li>
                    </ol>
                </div>

                <!-- Page Header -->
                <div class="page-header" style="margin-bottom: var(--spacing-8);">
                    <div class="page-title">
                        <h2 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-2);">
                            <i class="bi bi-bag-fill"></i>
                            Achats
                        </h2>
                        <p style="margin: var(--spacing-2) 0 0 0; color: var(--text-secondary);">
                            Listing de tous les achats avec gestion des frais
                        </p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-row" style="margin-bottom: var(--spacing-8);">
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: var(--primary-100); color: var(--primary);">
                            <i class="bi bi-bag"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">Total Achats</span>
                            <span class="stat-value"><?= count($achats ?? []) ?></span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: var(--success-100); color: var(--success);">
                            <i class="bi bi-currency-exchange"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">Montant Total</span>
                            <span class="stat-value"><?= number_format($stats['total_montant'] ?? 0, 0, ',', '.') ?> Ar</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: var(--warning-100); color: var(--warning);">
                            <i class="bi bi-percent"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">Total Frais</span>
                            <span class="stat-value"><?= number_format($stats['total_with_fees'] - ($stats['total_montant'] ?? 0), 0, ',', '.') ?> Ar</span>
                        </div>
                    </div>
                </div>

                <!-- Achats Table -->
                <div class="card">
                    <div class="card-body">
                        <?php if (!empty($achats) && is_array($achats)): ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Ville</th>
                                            <th>Catégorie</th>
                                            <th>Montant</th>
                                            <th>Frais %</th>
                                            <th>Montant Total</th>
                                            <th>Configuration</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($achats as $achat): ?>
                                            <tr>
                                                <td>
                                                    <span style="font-size: var(--font-size-sm);">
                                                        <?= date('d/m/Y', strtotime($achat['date_achat'] ?? 'now')) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong><?= htmlspecialchars($achat['ville_nom'] ?? 'N/A') ?></strong>
                                                </td>
                                                <td>
                                                    <span class="badge" style="background-color: var(--primary-100); color: var(--primary);">
                                                        <?= htmlspecialchars($achat['categorie_nom'] ?? 'N/A') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong><?= number_format($achat['montant'] ?? 0, 2, ',', '.') ?></strong>
                                                </td>
                                                <td>
                                                    <span style="font-weight: 600; color: var(--warning);">
                                                        <?= number_format($achat['frais_percent'] ?? 0, 2, ',', '.') ?>%
                                                    </span>
                                                </td>
                                                <td>
                                                    <span style="font-weight: 600; color: var(--success);">
                                                        <?= number_format($achat['montant_total'] ?? 0, 2, ',', '.') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="<?= Flight::get('flight.base_url') ?>/achat/<?= htmlspecialchars($achat['id'] ?? '') ?>/frais-config" 
                                                       class="btn btn-sm" 
                                                       title="Configurer les frais %"
                                                       style="background-color: var(--warning-100); color: var(--warning); border: 1px solid var(--warning); padding: var(--spacing-2) var(--spacing-3); border-radius: var(--radius); text-decoration: none; display: inline-flex; align-items: center; gap: var(--spacing-1); font-size: var(--font-size-sm); font-weight: 600;">
                                                        <i class="bi bi-percent"></i>
                                                        <span>Configuration%</span>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div style="padding: var(--spacing-6); text-align: center; color: var(--text-secondary);">
                                <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: var(--spacing-2);"></i>
                                <p>Aucun achat trouvé</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script nonce="<?= Flight::get('csp_nonce') ?>">
        // Toggle sidebar on mobile
        document.querySelector('.sidebar-toggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>

</html>
