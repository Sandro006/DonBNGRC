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
                            Dons (Nature & Matériaux)
                        </h2>
                        <p style="margin: var(--spacing-2) 0 0 0; color: var(--text-secondary);">
                            Listing de tous les dons qui ne sont pas en argent
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
                            <span class="stat-label">Total Dons</span>
                            <span class="stat-value"><?= count($dons ?? []) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Donations Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-2);">
                            <i class="bi bi-table"></i>
                            Liste des Dons (Non-Argent)
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($dons) && is_array($dons)): ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Donateur</th>
                                            <th>Catégorie</th>
                                            <th>Ville</th>
                                            <th>Région</th>
                                            <th>Quantité</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dons as $d): ?>
                                            <tr>
                                                <td>
                                                    <span style="font-size: var(--font-size-sm);">
                                                        <?= date('d/m/Y', strtotime($d['date_don'] ?? 'now')) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong><?= htmlspecialchars($d['donateur_nom'] ?? 'N/A') ?></strong>
                                                    <br />
                                                    <small style="color: var(--text-secondary);">
                                                        <?= htmlspecialchars($d['donateur_email'] ?? '') ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge" style="background-color: var(--primary-100); color: var(--primary);">
                                                        <?= htmlspecialchars($d['categorie_nom'] ?? 'N/A') ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars($d['ville_nom'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($d['region_nom'] ?? 'N/A') ?></td>
                                                <td>
                                                    <strong><?= number_format($d['quantite'] ?? 0, 2) ?></strong>
                                                </td>
                                                <td>
                                                    <div style="display: flex; gap: var(--spacing-2);">
                                                        <a href="<?= Flight::get('flight.base_url') ?>/don/<?= htmlspecialchars($d['id'] ?? '') ?>" 
                                                           class="btn btn-sm" title="Voir détails">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div style="padding: var(--spacing-6); text-align: center; color: var(--text-secondary);">
                                <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: var(--spacing-2);"></i>
                                <p>Aucun don trouvé</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Toggle sidebar on mobile
        document.querySelector('.sidebar-toggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>

</html>
