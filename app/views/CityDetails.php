<!DOCTYPE html>
<html class="light" lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Détails - Ville</title>
    
    <!-- Design System -->
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/design-system.css" rel="stylesheet" />
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/components.css" rel="stylesheet" />
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/layout.css" rel="stylesheet" />
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/utilities.css" rel="stylesheet" />
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/pages.css" rel="stylesheet" />
    
    <!-- Bootstrap Icons -->
    <link href="<?php echo Flight::get('flight.base_url'); ?>/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet" />
</head>

<body>
    <!-- Layout Container -->
    <div class="layout">
        <!-- Header -->
        <header class="header">
            <div class="header-container">
                <div class="header-left">
                    <button class="sidebar-toggle" aria-label="Toggle menu">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="header-title">
                        <i class="bi bi-geo-alt"></i> Détails Ville
                    </h1>
                </div>
                <div class="header-right">
                    <a href="<?= Flight::get('flight.base_url') ?>" class="header-link" title="Retour">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </header>

        <!-- Sidebar -->
        <aside class="sidebar">
            <nav class="sidebar-menu">
                <a href="<?= Flight::get('flight.base_url') ?>" class="sidebar-menu-link">
                    <i class="bi bi-house-fill"></i> Accueil
                </a>
                <a href="<?= Flight::get('flight.base_url') ?>/add-don" class="sidebar-menu-link">
                    <i class="bi bi-plus-circle"></i> Ajouter Don
                </a>
                <a href="<?= Flight::get('flight.base_url') ?>/simulation" class="sidebar-menu-link">
                    <i class="bi bi-shuffle"></i> Simulation
                </a>
                <a href="<?= Flight::get('flight.base_url') ?>/" class="sidebar-menu-link">
                    <i class="bi bi-map"></i> Retour
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="layout-content">
            <!-- Breadcrumb -->
            <nav class="breadcrumb-nav mb-6" aria-label="breadcrumb">
                <a href="<?= Flight::get('flight.base_url') ?>">Accueil</a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current"><?= htmlspecialchars($ville['nom'] ?? 'Ville') ?></span>
            </nav>

            <!-- City Header -->
            <div class="city-header mb-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="page-title flex items-center gap-2">
                            <i class="bi bi-geo-alt-fill text-primary"></i>
                            <?= htmlspecialchars($ville['nom'] ?? 'N/A') ?>
                        </h2>
                        <p class="page-subtitle flex items-center gap-2 mt-2">
                            <i class="bi bi-map text-gray-500"></i>
                            Région: <strong><?= htmlspecialchars($ville['region_nom'] ?? '') ?></strong>
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="<?= Flight::get('flight.base_url') ?>/don/ajouter?ville_id=<?= htmlspecialchars($ville['id'] ?? '') ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Ajouter Don
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-row mb-8">
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #5968ff10; color: #5968ff;">
                        <i class="bi bi-gift"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Dons</div>
                        <div class="stat-value"><?= count($dons ?? []) ?></div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #2a9d8f10; color: #2a9d8f;">
                        <i class="bi bi-hand-thumbs-up"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Besoins</div>
                        <div class="stat-value"><?= count($besoins ?? []) ?></div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid gap-8" style="grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));">
                <!-- Donations Section -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <i class="bi bi-gift text-lg text-primary"></i>
                        <h3 class="text-lg font-bold">Dons pour <?= htmlspecialchars($ville['nom'] ?? '') ?></h3>
                    </div>

                    <div class="card overflow-hidden">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th data-sortable="true">Date</th>
                                        <th data-sortable="true">Donateur</th>
                                        <th data-sortable="true">Catégorie</th>
                                        <th data-sortable="true">Quantité</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($dons)) {
                                        foreach ($dons as $d) { ?>
                                            <tr>
                                                <td>
                                                    <span class="text-sm">
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
                                                    <div class="flex gap-2">
                                                        <a href="<?= Flight::get('flight.base_url') ?>/simulation/<?= htmlspecialchars($d['id'] ?? '') ?>" 
                                                           class="btn btn-sm btn-secondary" title="Simuler la distribution">
                                                            <i class="bi bi-arrow-repeat"></i>
                                                        </a>
                                                        <form method="POST" action="<?= Flight::get('flight.base_url') ?>/don/supprimer/<?= htmlspecialchars($d['id'] ?? '') ?>" 
                                                              style="display:inline;">
                                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce don ?');"
                                                                    title="Supprimer ce don">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php }
                                    } else { ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-6">
                                                <div class="flex flex-col items-center justify-center gap-2">
                                                    <i class="bi bi-inbox text-3xl text-gray-400"></i>
                                                    <p class="text-gray-500">Aucun don pour cette ville</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Needs Section -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <i class="bi bi-hand-thumbs-up text-lg text-success"></i>
                        <h3 class="text-lg font-bold">Besoins pour <?= htmlspecialchars($ville['nom'] ?? '') ?></h3>
                    </div>

                    <div class="card overflow-hidden">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th data-sortable="true">Catégorie</th>
                                        <th data-sortable="true">Quantité</th>
                                        <th data-sortable="true">Montant</th>
                                        <th data-sortable="true">Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($besoins)) {
                                        foreach ($besoins as $b) {
                                            $statusBadgeClass = match(strtolower($b['status_nom'] ?? '')) {
                                                'pending' => 'badge-warning',
                                                'satisfied' => 'badge-success',
                                                'cancelled' => 'badge-danger',
                                                default => 'badge-outline'
                                            };
                                        ?>
                                            <tr>
                                                <td><?= htmlspecialchars($b['categorie_nom'] ?? '') ?></td>
                                                <td><strong><?= htmlspecialchars($b['quantite'] ?? '-') ?></strong></td>
                                                <td>
                                                    <span <?= isset($b['montant_total']) ? '' : 'class="text-gray-400"' ?>>
                                                        <?= isset($b['montant_total']) ? number_format((float)$b['montant_total'], 2, ',', '.') . ' Ar' : '-' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge <?= $statusBadgeClass ?>">
                                                        <?= htmlspecialchars($b['status_nom'] ?? 'N/A') ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php }
                                    } else { ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-6">
                                                <div class="flex flex-col items-center justify-center gap-2">
                                                    <i class="bi bi-inbox text-3xl text-gray-400"></i>
                                                    <p class="text-gray-500">Aucun besoin enregistré pour cette ville</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="<?php echo Flight::get('flight.base_url'); ?>/js/app.js"></script>
</body>

</html>