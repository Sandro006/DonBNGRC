<!DOCTYPE html>
<html class="light" lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BNGRC</title>
    <link href="<?= Flight::get('flight.base_url') ?>/css/design-system.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/components.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/layout.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/utilities.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/pages.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/custom.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet" />
</head>

<body>
<<<<<<< Updated upstream
    <div class="layout">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">🌊</div>
                <div class="sidebar-brand-text">
                    <h2>BNGRC</h2>
                    <small>Management System</small>
=======
    <aside class="sidebar">
        <div class="p-4 d-flex align-items-center gap-3">
            <div class="bg-primary rounded p-2 text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-water fs-5"></i>
            </div>
<<<<<<< Updated upstream
            <div>
                <h1 class="h6 fw-bold mb-0 text-primary">BNGRC</h1>
                <small class="text-muted d-block" style="font-size: 10px;">Management Office</small>
=======

            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="<?= Flight::get('flight.base_path') ?>/dashboard" class="sidebar-menu-link active">
                        <i class="bi bi-speedometer2"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?= Flight::get('flight.base_path') ?>/don/ajouter" class="sidebar-menu-link">
                        <i class="bi bi-gift"></i>
                        <span>Ajouter don</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?= Flight::get('flight.base_path') ?>/simulation" class="sidebar-menu-link">
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
                    <h1 style="margin: 0; font-size: 1.25rem; font-weight: 700;">📊 BNGRC - Tableau de Bord</h1>
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
                <!-- FILTER SECTION -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-2);">
                            <i class="bi bi-funnel"></i>
                            Filtres
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="get" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-4);">
                            <div class="form-group">
                                <label class="form-label">Date début</label>
                                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date fin</label>
                                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">Ville</label>
                                <select name="ville_id" class="form-select">
                                    <option value="">Toutes les villes</option>
                                    <?php if (!empty($cities)) {
                                        foreach ($cities as $ct) { ?>
                                            <option value="<?= htmlspecialchars($ct['id'] ?? '') ?>" <?= (isset($_GET['ville_id']) && $_GET['ville_id'] == ($ct['id'] ?? '')) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($ct['nom'] ?? '') ?>
                                            </option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Catégorie</label>
                                <select name="categorie_id" class="form-select">
                                    <option value="">Toutes les catégories</option>
                                    <?php if (!empty($categories)) {
                                        foreach ($categories as $cat) { ?>
                                            <option value="<?= htmlspecialchars($cat['id'] ?? '') ?>" <?= (isset($_GET['categorie_id']) && $_GET['categorie_id'] == ($cat['id'] ?? '')) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['libelle'] ?? '') ?>
                                            </option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>
                            <div style="grid-column: 1 / -1; display: flex; gap: var(--spacing-3);">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i>
                                    Appliquer
                                </button>
                                <a href="<?= htmlspecialchars(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) ?>" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise"></i>
                                    Réinitialiser
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- KPI SECTION -->
                <div class="cards-grid mb-6">
                    <div class="stat-card">
                        <div class="stat-content">
                            <span class="stat-label">Couverture Globale</span>
                            <span class="stat-value"><?= htmlspecialchars($coverage_percent ?? 0) ?>%</span>
                            <div class="progress mt-2">
                                <div class="progress-bar" style="width: <?= htmlspecialchars($coverage_percent ?? 0) ?>%;"></div>
                            </div>
                        </div>
                        <div class="stat-icon primary">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-content">
                            <span class="stat-label">Régions Actives</span>
                            <span class="stat-value"><?= htmlspecialchars($active_regions_count ?? 0) ?></span>
                            <span class="stat-change">En assistance d'urgence</span>
                        </div>
                        <div class="stat-icon warning">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-content">
                            <span class="stat-label">Total Dons</span>
                            <span class="stat-value"><?= count($dons ?? []) ?></span>
                            <span class="stat-change">Enregistrés</span>
                        </div>
                        <div class="stat-icon success">
                            <i class="bi bi-gift"></i>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-content">
                            <span class="stat-label">Total Besoins</span>
                            <span class="stat-value"><?= count($besoins ?? []) ?></span>
                            <span class="stat-change">Identifiés</span>
                        </div>
                        <div class="stat-icon danger">
                            <i class="bi bi-exclamation-circle"></i>
                        </div>
                    </div>
                </div>

                <!-- REGIONAL OVERVIEW -->
                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title">Vue d'ensemble régionale</h2>
                    </div>

                    <div class="card">
                        <div class="card-body p-0">
                            <div style="overflow-x: auto;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Région / Ville</th>
                                            <th>Distribution des besoins</th>
                                            <th>État</th>
                                            <th style="text-align: right;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($regional_stats)) {
                                            foreach ($regional_stats as $region) {
                                                $total = (float)($region['total_amount'] ?? 1);
                                                $nature_pct = $total > 0 ? round(((float)($region['nature_qty'] ?? 0) / max(1, (float)($region['total_quantity'] ?? 1))) * 100) : 0;
                                                $materiel_pct = $total > 0 ? round(((float)($region['materiel_qty'] ?? 0) / max(1, (float)($region['total_quantity'] ?? 1))) * 100) : 0;
                                                $fonds_pct = $total > 0 ? round(((float)($region['fonds_amount'] ?? 0) / $total) * 100) : 0;
                                                $is_critical = !empty($region['is_critical']);
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($region['ville_nom'] ?? '') ?></strong>
                                                <br/><small class="text-muted"><?= htmlspecialchars($region['region_nom'] ?? '') ?></small>
                                            </td>
                                            <td>
                                                <div style="display: flex; gap: var(--spacing-1); height: 30px; align-items: center;">
                                                    <?php if ($nature_pct > 0): ?>
                                                        <div class="progress-segment" style="width: <?= $nature_pct ?>%; background-color: #22c55e; border-radius: 4px 0 0 4px;"></div>
                                                    <?php endif; ?>
                                                    <?php if ($materiel_pct > 0): ?>
                                                        <div class="progress-segment" style="width: <?= $materiel_pct ?>%; background-color: #3b82f6;"></div>
                                                    <?php endif; ?>
                                                    <?php if ($fonds_pct > 0): ?>
                                                        <div class="progress-segment" style="width: <?= $fonds_pct ?>%; background-color: #f59e0b; border-radius: 0 4px 4px 0;"></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div style="display: flex; gap: var(--spacing-2); margin-top: var(--spacing-1); font-size: 0.75rem;">
                                                    <span><i class="bi bi-square-fill" style="color: #22c55e; margin-right: 4px;"></i>Nature</span>
                                                    <span><i class="bi bi-square-fill" style="color: #3b82f6; margin-right: 4px;"></i>Matériel</span>
                                                    <span><i class="bi bi-square-fill" style="color: #f59e0b; margin-right: 4px;"></i>Fonds</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge <?= $is_critical ? 'badge-danger' : 'badge-success' ?>">
                                                    <?= $is_critical ? '🔴 Critique' : '🟢 Normal' ?>
                                                </span>
                                            </td>
                                            <td style="text-align: right;">
                                                <a href="<?= Flight::get('flight.base_path') ?>/ville/<?= htmlspecialchars($region['ville_id'] ?? '') ?>" class="btn btn-sm btn-secondary" title="Voir détails">
                                                    <i class="bi bi-arrow-right"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php }
                                        } else { ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-6 text-gray-500">
                                                <i class="bi bi-inbox text-3xl block mb-2"></i>
                                                Aucune donnée disponible
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RECENT DONATIONS -->
                <div class="section mt-8">
                    <div class="section-header">
                        <h2 class="section-title">Dons récents</h2>
                        <a href="<?= Flight::get('flight.base_path') ?>/don/ajouter" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus"></i> Ajouter
                        </a>
                    </div>

                    <div class="card">
                        <div class="card-body p-0">
                            <div style="overflow-x: auto;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Donateur</th>
                                            <th>Catégorie</th>
                                            <th>Quantité</th>
                                            <th>Ville</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($dons)) {
                                            foreach (array_slice($dons, 0, 10) as $d) { ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($d['date_don'] ?? 'now')) ?></td>
                                            <td><?= htmlspecialchars($d['donateur_nom'] ?? '') ?></td>
                                            <td><span class="badge badge-primary"><?= htmlspecialchars($d['categorie_nom'] ?? '') ?></span></td>
                                            <td><strong><?= htmlspecialchars($d['quantite'] ?? '') ?></strong></td>
                                            <td><?= htmlspecialchars($d['ville_nom'] ?? '') ?></td>
                                            <td>
                                                <a href="<?= Flight::get('flight.base_path') ?>/simulation/<?= htmlspecialchars($d['id'] ?? '') ?>" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-right"></i></a>
                                            </td>
                                        </tr>
                                        <?php }
                                        } else { ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-6 text-gray-500">
                                                <i class="bi bi-inbox text-3xl block mb-2"></i>
                                                Aucun don enregistré
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RECENT NEEDS -->
                <div class="section mt-8">
                    <div class="section-header">
                        <h2 class="section-title">Besoins récents</h2>
                    </div>

                    <div class="card">
                        <div class="card-body p-0">
                            <div style="overflow-x: auto;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Catégorie</th>
                                            <th>Quantité</th>
                                            <th>Ville</th>
                                            <th>Région</th>
                                            <th>Statut</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($besoins)) {
                                            foreach (array_slice($besoins, 0, 10) as $b) {
                                                $statusClass = match(strtolower($b['status_nom'] ?? '')) {
                                                    'satisfied' => 'badge-success',
                                                    'pending' => 'badge-warning',
                                                    'critical' => 'badge-danger',
                                                    default => 'badge-outline'
                                                };
                                            ?>
                                        <tr>
                                            <td><?= htmlspecialchars($b['categorie_nom'] ?? '') ?></td>
                                            <td><strong><?= htmlspecialchars($b['quantite'] ?? '') ?></strong></td>
                                            <td><?= htmlspecialchars($b['ville_nom'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($b['region_nom'] ?? '') ?></td>
                                            <td><span class="badge <?= $statusClass ?>"><?= htmlspecialchars($b['status_nom'] ?? '') ?></span></td>
                                            <td><?= htmlspecialchars(substr($b['description'] ?? '', 0, 50)) ?>...</td>
                                        </tr>
                                        <?php }
                                        } else { ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-6 text-gray-500">
                                                <i class="bi bi-inbox text-3xl block mb-2"></i>
                                                Aucun besoin enregistré
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
>>>>>>> Stashed changes
            </div>
        </div>
        <nav class="nav flex-column px-3 mt-3 flex-grow-1 gap-1">
            <a class="nav-link active" href="#"><i class="bi bi-house-fill"></i> Home</a>
            <a class="nav-link" href="#"><i class="bi bi-map"></i> Map</a>
            <a class="nav-link" href="#"><i class="bi bi-bar-chart"></i> Data</a>
            <a class="nav-link" href="#"><i class="bi bi-file-text"></i> Reports</a>
            <a class="nav-link mt-auto" href="#"><i class="bi bi-gear"></i> Settings</a>
        </nav>
        <div class="p-3 border-top">
            <button class="btn btn-emergency w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i> Emergency Alert
            </button>
        </div>
    </aside>
    <main class="main-content">
        <header class="navbar bg-white border-bottom px-4 py-2 sticky-top">
            <div class="container-fluid">
                <form class="d-flex col-md-4">
                    <div class="input-group bg-light rounded">
                        <span class="input-group-text bg-transparent border-0"><i class="bi bi-search text-muted"></i></span>
                        <input class="form-control border-0 bg-transparent" placeholder="Search data or regions..." type="text" />
                    </div>
                </form>
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex gap-2">
                        <button class="btn btn-link text-muted p-2 position-relative">
                            <i class="bi bi-bell fs-5"></i>
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                        </button>
                        <button class="btn btn-link text-muted p-2"><i class="bi bi-question-circle fs-5"></i></button>
                    </div>
                    <div class="vr h-100 mx-2"></div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="text-end">
                            <div class="fw-bold small">Admin BNGRC</div>
                            <div class="text-muted" style="font-size: 11px;">National Coordinator</div>
                        </div>
                        <img class="rounded-circle border" height="36" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD_iOZFABdinTQc0LkG0xWcb-BJdTC37Zs6nEVsmcgD2805PVqQZEWv14oMR6oaMGrvLWIxG7dY-aWP7DMQFXSf6-ZtoT-0fQZjUisUK673pNXhaVSoQH5CSnIwQs7IjLE8Kz7pOV81ujyvXjP7G3nyOBonieJ4SwtN7GEoM5kdQyLaWSxH7NXVrzDGf1NiOhSQrJ51L9UtptgXmkLRQh2RaEQ9EcF8jQ9eGesCBQ_pw5FM_85uOvCOnShXb9MTa_UaXEPB0goI2NkB" width="36" />
                    </div>
                </div>
            </div>
        </header>
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="fw-black mb-0">Welcome, Administrator</h2>
                    <p class="text-muted mb-0"><?= date('l, d M') ?> • <?= date('H:i') ?> local time</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2">
                        <i class="bi bi-plus-circle"></i> Record New Need
                    </button>
                    <button class="btn btn-outline-secondary bg-white text-dark d-flex align-items-center gap-2 px-4 py-2 fw-bold">
                        <i class="bi bi-heart"></i> Log New Donation
                    </button>
>>>>>>> Stashed changes
                </div>
            </div>

            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="<?= Flight::get('flight.base_url') ?>/dashboard" class="sidebar-menu-link active">
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

        <!-- MAIN CONTENT -->
        <div class="layout-main">
            <!-- HEADER -->
            <header class="header">
                <div class="header-container">
                    <button class="sidebar-toggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 style="margin: 0; font-size: 1.25rem; font-weight: 700;">📊 BNGRC - Tableau de Bord</h1>
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
                <!-- FILTER SECTION -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-2);">
                            <i class="bi bi-funnel"></i>
                            Filtres
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="get" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-4);">
                            <div class="form-group">
                                <label class="form-label">Date début</label>
                                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date fin</label>
                                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">Ville</label>
                                <select name="ville_id" class="form-select">
                                    <option value="">Toutes les villes</option>
                                    <?php if (!empty($cities)) {
                                        foreach ($cities as $ct) { ?>
                                            <option value="<?= htmlspecialchars($ct['id'] ?? '') ?>" <?= (isset($_GET['ville_id']) && $_GET['ville_id'] == ($ct['id'] ?? '')) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($ct['nom'] ?? '') ?>
                                            </option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Catégorie</label>
                                <select name="categorie_id" class="form-select">
                                    <option value="">Toutes les catégories</option>
                                    <?php if (!empty($categories)) {
                                        foreach ($categories as $cat) { ?>
                                            <option value="<?= htmlspecialchars($cat['id'] ?? '') ?>" <?= (isset($_GET['categorie_id']) && $_GET['categorie_id'] == ($cat['id'] ?? '')) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['libelle'] ?? '') ?>
                                            </option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>
                            <div style="grid-column: 1 / -1; display: flex; gap: var(--spacing-3);">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i>
                                    Appliquer
                                </button>
                                <a href="<?= htmlspecialchars(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) ?>" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise"></i>
                                    Réinitialiser
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- KPI SECTION -->
                <div class="cards-grid mb-6">
                    <div class="stat-card">
                        <div class="stat-content">
                            <span class="stat-label">Couverture Globale</span>
                            <span class="stat-value"><?= htmlspecialchars($coverage_percent ?? 0) ?>%</span>
                            <div class="progress mt-2">
                                <div class="progress-bar" style="width: <?= htmlspecialchars($coverage_percent ?? 0) ?>%;"></div>
                            </div>
                        </div>
                        <div class="stat-icon primary">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-content">
                            <span class="stat-label">Régions Actives</span>
                            <span class="stat-value"><?= htmlspecialchars($active_regions_count ?? 0) ?></span>
                            <span class="stat-change">En assistance d'urgence</span>
                        </div>
                        <div class="stat-icon warning">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-content">
                            <span class="stat-label">Total Dons</span>
                            <span class="stat-value"><?= count($dons ?? []) ?></span>
                            <span class="stat-change">Enregistrés</span>
                        </div>
                        <div class="stat-icon success">
                            <i class="bi bi-gift"></i>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-content">
                            <span class="stat-label">Total Besoins</span>
                            <span class="stat-value"><?= count($besoins ?? []) ?></span>
                            <span class="stat-change">Identifiés</span>
                        </div>
                        <div class="stat-icon danger">
                            <i class="bi bi-exclamation-circle"></i>
                        </div>
                    </div>
                </div>

                <!-- REGIONAL OVERVIEW -->
                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title">Vue d'ensemble régionale</h2>
                    </div>

                    <div class="card">
                        <div class="card-body p-0">
                            <div style="overflow-x: auto;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Région / Ville</th>
                                            <th>Distribution des besoins</th>
                                            <th>État</th>
                                            <th style="text-align: right;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($regional_stats)) {
                                            foreach ($regional_stats as $region) {
                                                $total = (float)($region['total_amount'] ?? 1);
                                                $nature_pct = $total > 0 ? round(((float)($region['nature_qty'] ?? 0) / max(1, (float)($region['total_quantity'] ?? 1))) * 100) : 0;
                                                $materiel_pct = $total > 0 ? round(((float)($region['materiel_qty'] ?? 0) / max(1, (float)($region['total_quantity'] ?? 1))) * 100) : 0;
                                                $fonds_pct = $total > 0 ? round(((float)($region['fonds_amount'] ?? 0) / $total) * 100) : 0;
                                                $is_critical = !empty($region['is_critical']);
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($region['ville_nom'] ?? '') ?></strong>
                                                <br/><small class="text-muted"><?= htmlspecialchars($region['region_nom'] ?? '') ?></small>
                                            </td>
                                            <td>
                                                <div style="display: flex; gap: var(--spacing-1); height: 30px; align-items: center;">
                                                    <?php if ($nature_pct > 0): ?>
                                                        <div class="progress-segment" style="width: <?= $nature_pct ?>%; background-color: #22c55e; border-radius: 4px 0 0 4px;"></div>
                                                    <?php endif; ?>
                                                    <?php if ($materiel_pct > 0): ?>
                                                        <div class="progress-segment" style="width: <?= $materiel_pct ?>%; background-color: #3b82f6;"></div>
                                                    <?php endif; ?>
                                                    <?php if ($fonds_pct > 0): ?>
                                                        <div class="progress-segment" style="width: <?= $fonds_pct ?>%; background-color: #f59e0b; border-radius: 0 4px 4px 0;"></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div style="display: flex; gap: var(--spacing-2); margin-top: var(--spacing-1); font-size: 0.75rem;">
                                                    <span><i class="bi bi-square-fill" style="color: #22c55e; margin-right: 4px;"></i>Nature</span>
                                                    <span><i class="bi bi-square-fill" style="color: #3b82f6; margin-right: 4px;"></i>Matériel</span>
                                                    <span><i class="bi bi-square-fill" style="color: #f59e0b; margin-right: 4px;"></i>Fonds</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge <?= $is_critical ? 'badge-danger' : 'badge-success' ?>">
                                                    <?= $is_critical ? '🔴 Critique' : '🟢 Normal' ?>
                                                </span>
                                            </td>
                                            <td style="text-align: right;">
                                                <a href="<?= Flight::get('flight.base_url') ?>/ville/<?= htmlspecialchars($region['ville_id'] ?? '') ?>" class="btn btn-sm btn-secondary" title="Voir détails">
                                                    <i class="bi bi-arrow-right"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php }
                                        } else { ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-6 text-gray-500">
                                                <i class="bi bi-inbox text-3xl block mb-2"></i>
                                                Aucune donnée disponible
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RECENT DONATIONS -->
                <div class="section mt-8">
                    <div class="section-header">
                        <h2 class="section-title">Dons récents</h2>
                        <a href="<?= Flight::get('flight.base_url') ?>/don/ajouter" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus"></i> Ajouter
                        </a>
                    </div>

                    <div class="card">
                        <div class="card-body p-0">
                            <div style="overflow-x: auto;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Donateur</th>
                                            <th>Catégorie</th>
                                            <th>Quantité</th>
                                            <th>Ville</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($dons)) {
                                            foreach (array_slice($dons, 0, 10) as $d) { ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($d['date_don'] ?? 'now')) ?></td>
                                            <td><?= htmlspecialchars($d['donateur_nom'] ?? '') ?></td>
                                            <td><span class="badge badge-primary"><?= htmlspecialchars($d['categorie_nom'] ?? '') ?></span></td>
                                            <td><strong><?= htmlspecialchars($d['quantite'] ?? '') ?></strong></td>
                                            <td><?= htmlspecialchars($d['ville_nom'] ?? '') ?></td>
                                            <td>
                                                <a href="<?= Flight::get('flight.base_url') ?>/simulation/<?= htmlspecialchars($d['id'] ?? '') ?>" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-right"></i></a>
                                            </td>
                                        </tr>
                                        <?php }
                                        } else { ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-6 text-gray-500">
                                                <i class="bi bi-inbox text-3xl block mb-2"></i>
                                                Aucun don enregistré
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RECENT NEEDS -->
                <div class="section mt-8">
                    <div class="section-header">
                        <h2 class="section-title">Besoins récents</h2>
                    </div>

                    <div class="card">
                        <div class="card-body p-0">
                            <div style="overflow-x: auto;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Catégorie</th>
                                            <th>Quantité</th>
                                            <th>Ville</th>
                                            <th>Région</th>
                                            <th>Statut</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($besoins)) {
                                            foreach (array_slice($besoins, 0, 10) as $b) {
                                                $statusClass = match(strtolower($b['status_nom'] ?? '')) {
                                                    'satisfied' => 'badge-success',
                                                    'pending' => 'badge-warning',
                                                    'critical' => 'badge-danger',
                                                    default => 'badge-outline'
                                                };
                                            ?>
                                        <tr>
                                            <td><?= htmlspecialchars($b['categorie_nom'] ?? '') ?></td>
                                            <td><strong><?= htmlspecialchars($b['quantite'] ?? '') ?></strong></td>
                                            <td><?= htmlspecialchars($b['ville_nom'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($b['region_nom'] ?? '') ?></td>
                                            <td><span class="badge <?= $statusClass ?>"><?= htmlspecialchars($b['status_nom'] ?? '') ?></span></td>
                                            <td><?= htmlspecialchars(substr($b['description'] ?? '', 0, 50)) ?>...</td>
                                        </tr>
                                        <?php }
                                        } else { ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-6 text-gray-500">
                                                <i class="bi bi-inbox text-3xl block mb-2"></i>
                                                Aucun besoin enregistré
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <footer class="layout-footer">
                <p>&copy; <?= date('Y') ?> Bureau National de Gestion des Risques et Catastrophes (BNGRC). Tous droits réservés.</p>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Responsive sidebar toggle
        document.querySelector('.sidebar-toggle')?.addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('show');
        });
    </script>
    <script src="<?= Flight::get('flight.base_url') ?>/js/app.js"></script>
</body>

</html>