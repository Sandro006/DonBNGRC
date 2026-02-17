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
    <link href="<?= Flight::get('flight.base_url') ?>/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
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
                    <a href="<?= Flight::get('flight.base_url') ?>/dashboard" class="sidebar-menu-link active">
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
                                <label class="form-label">Ville <small>(besoins uniquement)</small></label>
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
                            <span class="stat-label">Besoins Urgents</span>
                            <span class="stat-value"><?= htmlspecialchars($urgent_needs_count ?? 0) ?></span>
                            <span class="stat-change">Requierent attention</span>
                        </div>
                        <div class="stat-icon danger">
                            <i class="bi bi-exclamation-triangle-fill"></i>
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
                        <div class="stat-icon info">
                            <i class="bi bi-list-check"></i>
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
                        <a href="<?= Flight::get('flight.base_url') ?>/don-global/nouveau" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus"></i> Créer Don Global
                        </a>
                    </div>

                    <!-- EXPIRING DONATIONS ALERT -->
                    <?php if (!empty($expiring_donations) && count($expiring_donations) > 0): ?>
                    <div class="card border-danger mb-4">
                        <div class="card-header bg-danger bg-opacity-10">
                            <h6 style="margin: 0; color: var(--color-danger);">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                Dons périmant bientôt (<?= count($expiring_donations) ?> éléments)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach (array_slice($expiring_donations, 0, 5) as $exp): ?>
                                <span class="badge badge-danger">
                                    <?= htmlspecialchars($exp['donateur_nom'] ?? '') ?> - 
                                    <?= htmlspecialchars($exp['categorie_nom'] ?? '') ?>
                                    (<?= date('d/m/Y', strtotime($exp['date_expiration'] ?? 'now')) ?>)
                                </span>
                                <?php endforeach; ?>
                                <?php if (count($expiring_donations) > 5): ?>
                                <span class="badge badge-outline">+<?= count($expiring_donations) - 5 ?> autres...</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body p-0">
                            <div style="overflow-x: auto;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Donateur</th>
                                            <th>Type</th>
                                            <th>Catégorie</th>
                                            <th>Quantité</th>
                                            <th>Valeur Unit.</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($dons)) {
                                            foreach (array_slice($dons, 0, 10) as $d) { ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($d['date_don'] ?? 'now')) ?></td>
                                            <td><?= htmlspecialchars($d['donateur_nom'] ?? '') ?></td>
                                            <td>
                                                <span class="badge <?= 
                                                    ($d['type_donateur'] ?? '') === 'entreprise' ? 'badge-primary' : 
                                                    (($d['type_donateur'] ?? '') === 'gouvernement' ? 'badge-success' : 'badge-info') 
                                                ?>">
                                                    <?= htmlspecialchars($d['type_donateur'] ?? 'Individu') ?>
                                                </span>
                                            </td>
                                            <td><span class="badge badge-secondary"><?= htmlspecialchars($d['categorie_nom'] ?? '') ?></span></td>
                                            <td><strong><?= htmlspecialchars($d['quantite'] ?? '') ?></strong></td>
                                            <td><?= !empty($d['valeur_unitaire']) ? number_format($d['valeur_unitaire'], 0, ',', ' ') . ' Ar' : '-' ?></td>
                                            <td>
                                                <a href="<?= Flight::get('flight.base_url') ?>/simulation/<?= htmlspecialchars($d['id'] ?? '') ?>" 
                                                   class="btn btn-sm btn-secondary" title="Distribuer">
                                                    <i class="bi bi-arrow-right"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php }
                                        } else { ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-6 text-gray-500">
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

                    <!-- DONOR TYPE STATISTICS -->
                    <?php if (!empty($donations_by_donor_type) && count($donations_by_donor_type) > 0): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 style="margin: 0;">
                                <i class="bi bi-pie-chart"></i>
                                Distribution par type de donateur
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-4">
                                <?php foreach ($donations_by_donor_type as $type): ?>
                                <div class="text-center">
                                    <div class="fw-bold text-lg"><?= htmlspecialchars($type['total_quantity'] ?? 0) ?></div>
                                    <div class="text-sm text-muted"><?= htmlspecialchars(ucfirst($type['type_donateur'] ?? 'Inconnu')) ?></div>
                                    <div class="text-xs">
                                        <?= !empty($type['total_value']) ? number_format($type['total_value'], 0, ',', ' ') . ' Ar' : '' ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body p-0">
                            <div style="overflow-x: auto;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Catégorie</th>
                                            <th>Quantité</th>
                                            <th>Priorité</th>
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
                                                $priorityClass = match(strtolower($b['priorite'] ?? 'normale')) {
                                                    'urgente' => 'badge-danger',
                                                    'elevee' => 'badge-warning',
                                                    'normale' => 'badge-info',
                                                    'faible' => 'badge-secondary',
                                                    default => 'badge-outline'
                                                };
                                            ?>
                                        <tr>
                                            <td><?= htmlspecialchars($b['categorie_nom'] ?? '') ?></td>
                                            <td><strong><?= htmlspecialchars($b['quantite'] ?? '') ?></strong></td>
                                            <td>
                                                <span class="badge <?= $priorityClass ?>">
                                                    <?= htmlspecialchars(ucfirst($b['priorite'] ?? 'normale')) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($b['ville_nom'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($b['region_nom'] ?? '') ?></td>
                                            <td><span class="badge <?= $statusClass ?>"><?= htmlspecialchars($b['status_nom'] ?? '') ?></span></td>
                                            <td><?= htmlspecialchars(substr($b['description'] ?? '', 0, 50)) ?>...</td>
                                        </tr>
                                        <?php }
                                        } else { ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-6 text-gray-500">
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

        <?php include __DIR__ . '/partials/footer.php'; ?>

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