<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>BNGRC - National Office for Risk and Disaster Management</title>
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet" />
    <style>
        :root {
            --primary-color: #1152d4;
            --bg-light: #f6f6f8;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            margin: 0;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 256px;
            background: white;
            border-right: 1px solid #dee2e6;
            height: 100vh;
            position: sticky;
            top: 0;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex: 1;
            min-width: 0;
            overflow-y: auto;
        }

        .nav-link {
            color: #495057;
            padding: 0.625rem 1rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
        }

        .nav-link:hover {
            background-color: #f8f9fa;
        }

        .nav-link.active {
            background-color: var(--primary-color);
            color: white !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-emergency {
            background-color: #dc3545;
            color: white;
            font-weight: 700;
        }

        .kpi-card {
            border-radius: 0.75rem;
            border: 1px solid #dee2e6;
            background: white;
            padding: 1.5rem;
        }

        .progress-multi {
            display: flex;
            height: 0.75rem;
            border-radius: 999px;
            overflow: hidden;
            background-color: #e9ecef;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <aside class="sidebar">
        <div class="p-4 d-flex align-items-center gap-3">
            <div class="bg-primary rounded p-2 text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-water fs-5"></i>
            </div>
            <div>
                <h1 class="h6 fw-bold mb-0 text-primary">BNGRC</h1>
                <small class="text-muted d-block" style="font-size: 10px;">Management Office</small>
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
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4 p-3">
                <form method="get" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small">Date début</label>
                        <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Date fin</label>
                        <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Ville</label>
                        <select name="ville_id" class="form-select">
                            <option value="">Toutes</option>
                            <?php if (!empty($cities)) {
                                foreach ($cities as $ct) { ?>
                                    <option value="<?= htmlspecialchars($ct['id'] ?? '') ?>" <?= (isset($_GET['ville_id']) && $_GET['ville_id'] == ($ct['id'] ?? '')) ? 'selected' : '' ?>><?= htmlspecialchars($ct['nom'] ?? '') ?></option>
                            <?php }
                            } ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Catégorie</label>
                        <select name="categorie_id" class="form-select">
                            <option value="">Toutes</option>
                            <?php if (!empty($categories)) {
                                foreach ($categories as $cat) { ?>
                                    <option value="<?= htmlspecialchars($cat['id'] ?? '') ?>" <?= (isset($_GET['categorie_id']) && $_GET['categorie_id'] == ($cat['id'] ?? '')) ? 'selected' : '' ?>><?= htmlspecialchars($cat['libelle'] ?? '') ?></option>
                            <?php }
                            } ?>
                        </select>
                    </div>
                    <div class="col-12 text-end">
                        <button class="btn btn-primary">Appliquer</button>
                        <a href="<?= htmlspecialchars(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) ?>" class="btn btn-link">Réinitialiser</a>
                    </div>
                </form>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="kpi-card h-100">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="bg-light text-primary p-2 rounded"><i class="bi bi-patch-check fs-5"></i></div>
                            <span class="<?= ($coverage_percent ?? 0) >= 50 ? 'text-success' : 'text-warning' ?> small fw-bold"><?= ($coverage_percent ?? 0) ?>%</span>
                        </div>
                        <small class="text-muted fw-bold text-uppercase">Global Aid Coverage</small>
                        <h3 class="fw-black"><?= htmlspecialchars($coverage_percent ?? 0) ?>%</h3>
                        <div class="progress mt-3" style="height: 6px;">
                            <div class="progress-bar" style="width: <?= htmlspecialchars($coverage_percent ?? 0) ?>%;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card h-100">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="bg-light text-warning p-2 rounded"><i class="bi bi-geo-alt fs-5"></i></div>
                            <span class="text-muted small fw-bold">LIVE</span>
                        </div>
                        <small class="text-muted fw-bold text-uppercase">Active Regions</small>
                        <h3 class="fw-black"><?= htmlspecialchars($active_regions_count ?? 0) ?></h3>
                        <small class="text-muted">Receiving emergency assistance</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 rounded-4 overflow-hidden h-100 position-relative text-white">
                        <div class="position-absolute w-100 h-100" style="background: linear-gradient(to right, rgba(0,0,0,0.7), transparent), url('https://lh3.googleusercontent.com/aida-public/AB6AXuDwM_vgw6x8mcM9XFfGatLQxSa9-YFn6INqcxVPNaHNAECRj4lzfhjZs6Ee_uVbLLE3o4PqAWTw0TqR-90sftfSCTk4lCYg02Nn0PexhFmuUu29Z7OfytYFOSgJTYyuX3R0bErlW_Z6hbVg2XoEu33PjKKa19XQ0vgLgt80e-Bea5RitiotqTtvjUO_45HXuxncPu9qL1iVTJaGhkDgvt64yN6wKwxaLpUEx3S8uCtsFoTQIAt4ftvijFXngJGIPKEudVKQIBHRIJbt'); background-size: cover; background-position: center;"></div>
                        <div class="card-body position-relative d-flex flex-column justify-content-between p-4">
                            <div>
                                <span class="badge bg-danger rounded-pill mb-2">LIVE INCIDENT MAP</span>
                                <h4 class="fw-bold">Active Risk Zones</h4>
                            </div>
                            <a class="text-white text-decoration-none small fw-bold" href="#">View Full Map →</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white p-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="bi bi-graph-up text-primary"></i> Regional Needs Overview
                    </h5>
                    <div class="d-flex gap-3 small fw-bold text-muted">
                        <div class="d-flex align-items-center gap-1"><span class="bg-primary rounded-circle" style="width: 8px; height: 8px;"></span> Rice/Oil</div>
                        <div class="d-flex align-items-center gap-1"><span class="bg-secondary rounded-circle" style="width: 8px; height: 8px;"></span> Sheets/Nails</div>
                        <div class="d-flex align-items-center gap-1"><span class="bg-success rounded-circle" style="width: 8px; height: 8px;"></span> Funds</div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3 small text-muted text-uppercase">Region / City</th>
                                <th class="py-3 small text-muted text-uppercase w-50">Need Distribution</th>
                                <th class="py-3 small text-muted text-uppercase">Status</th>
                                <th class="py-3 small text-muted text-uppercase text-end pe-4">Action</th>
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
                                <td class="ps-4 py-4">
                                    <div class="fw-bold"><?= htmlspecialchars($region['ville_nom'] ?? '') ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($region['region_nom'] ?? '') ?> Region</small>
                                </td>
                                <td>
                                    <div class="progress-multi">
                                        <div class="bg-primary" style="width: <?= $nature_pct ?>%;"></div>
                                        <div class="bg-secondary" style="width: <?= $materiel_pct ?>%;"></div>
                                        <div class="bg-success" style="width: <?= $fonds_pct ?>%;"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2 small text-muted fw-bold">
                                        <span><?= $nature_pct ?>% Nature</span><span><?= $materiel_pct ?>% Material</span><span><?= $fonds_pct ?>% Funds</span>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($is_critical) { ?>
                                        <span class="badge bg-warning-subtle text-warning border-0 text-uppercase" style="font-size: 10px;">Critical</span>
                                    <?php } else { ?>
                                        <span class="badge bg-primary-subtle text-primary border-0 text-uppercase" style="font-size: 10px;">Stable</span>
                                    <?php } ?>
                                </td>
                                <td class="text-end pe-4"><button class="btn btn-link text-decoration-none fw-bold text-primary">Details</button></td>
                            </tr>
                            <?php }
                            } else { ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Aucune donnée régionale disponible</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white text-center py-3">
                    <button class="btn btn-link text-muted text-decoration-none fw-bold">View All <?= htmlspecialchars($active_regions_count ?? 0) ?> Assisted Regions</button>
                </div>
            </div>

            <!-- Filtered data tables: Donations & Needs -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card p-3">
                        <h5 class="fw-bold">Dons (extrait)</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Ville</th>
                                        <th>Catégorie</th>
                                        <th>Quantité</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($dons)) {
                                        foreach ($dons as $d) { ?>
                                            <tr>
                                                <td><?= htmlspecialchars($d['date_don'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($d['ville_nom'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($d['categorie_nom'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($d['quantite'] ?? '') ?></td>
                                            </tr>
                                        <?php }
                                    } else { ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Aucun don trouvé</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card p-3">
                        <h5 class="fw-bold">Besoins (extrait)</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Ville</th>
                                        <th>Catégorie</th>
                                        <th>Quantité</th>
                                        <th>Prix unitaire</th>
                                        <th>Montant total</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($besoins)) {
                                        foreach ($besoins as $b) { ?>
                                            <tr>
                                                <td><?= htmlspecialchars($b['ville_nom'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($b['categorie_nom'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($b['quantite'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($b['prix_unitaire'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($b['montant_total'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($b['status_nom'] ?? '') ?></td>
                                            </tr>
                                        <?php }
                                    } else { ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Aucun besoin trouvé</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>