<!DOCTYPE html>
<html class="light" lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - BNGRC</title>
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
        <!-- SIDEBAR -->
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
                    <a href="<?= Flight::get('flight.base_url') ?>/don-global" class="sidebar-menu-link active">
                        <i class="bi bi-globe"></i>
                        <span>Dons Globaux</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?= Flight::get('flight.base_url') ?>/don-global/methodes" class="sidebar-menu-link">
                        <i class="bi bi-gear-fill"></i>
                        <span>Méthodes Distribution</span>
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
                    <h1 style="margin: 0; font-size: 1.25rem; font-weight: 700;">🌍 BNGRC - <?= htmlspecialchars($title) ?></h1>
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
                        <li>Détails #<?= $don_global['id'] ?></li>
                    </ol>
                </div>

                <!-- PAGE HEADER -->
                <div class="page-header">
                    <div class="page-title">
                        <h1><i class="bi bi-box-seam"></i> <?= htmlspecialchars($title) ?></h1>
                        <p>Informations détaillées du don global</p>
                    </div>
                    <div class="page-actions">
                        <a href="<?= Flight::get('flight.base_url') ?>/don-global" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                        <?php if ($remaining_quantity > 0): ?>
                            <a href="<?= Flight::get('flight.base_url') ?>/don-global/simulation?don_id=<?= $don_global['id'] ?>" class="btn btn-primary">
                                <i class="bi bi-diagram-3"></i> Simuler Distribution
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- MAIN CONTENT AREA -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Informations du Don Global -->
                    <div class="lg:col-span-2">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="bi bi-info-circle text-primary"></i>
                                    Informations du Don
                                </h3>
                                <div class="card-header-actions">
                                    <span class="badge badge-<?= $don_global['status'] === 'disponible' ? 'success' : ($don_global['status'] === 'partiellement_distribue' ? 'warning' : 'secondary') ?>">
                                        <?= ucfirst(str_replace('_', ' ', $don_global['status'])) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="form-group">
                                        <label class="form-label">Catégorie</label>
                                        <div class="flex items-center space-x-2">
                                            <span class="badge badge-primary"><?= htmlspecialchars($don_global['categorie_nom']) ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">Quantité Initiale</label>
                                        <div class="text-lg font-semibold text-primary">
                                            <?= number_format($don_global['quantite'], 0, ',', ' ') ?> unités
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">Quantité Restante</label>
                                        <div class="text-lg font-semibold <?= $remaining_quantity > 0 ? 'text-success' : 'text-danger' ?>">
                                            <?= number_format($remaining_quantity, 0, ',', ' ') ?> unités
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">Date de Don</label>
                                        <div class="flex items-center space-x-2">
                                            <i class="bi bi-calendar3 text-gray-500"></i>
                                            <span><?= date('d/m/Y', strtotime($don_global['date_don'])) ?></span>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($don_global['donateur_nom'])): ?>
                                    <div class="form-group">
                                        <label class="form-label">Donateur</label>
                                        <div class="flex items-center space-x-2">
                                            <i class="bi bi-person-fill text-gray-500"></i>
                                            <span><?= htmlspecialchars($don_global['donateur_nom']) ?></span>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($don_global['donateur_email'])): ?>
                                    <div class="form-group">
                                        <label class="form-label">Email du Donateur</label>
                                        <div class="flex items-center space-x-2">
                                            <i class="bi bi-envelope text-gray-500"></i>
                                            <a href="mailto:<?= htmlspecialchars($don_global['donateur_email']) ?>" class="text-primary hover:underline">
                                                <?= htmlspecialchars($don_global['donateur_email']) ?>
                                            </a>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (!empty($don_global['description'])): ?>
                                <div class="mt-6">
                                    <label class="form-label">Description</label>
                                    <div class="text-gray-700 whitespace-pre-line">
                                        <?= htmlspecialchars($don_global['description']) ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Statistiques -->
                    <div class="space-y-6">
                        <!-- Progress Card -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="bi bi-bar-chart text-primary"></i>
                                    Progression
                                </h3>
                            </div>
                            <div class="card-body">
                                <?php 
                                $distribue = $don_global['quantite'] - $remaining_quantity;
                                $pourcentage = $don_global['quantite'] > 0 ? ($distribue / $don_global['quantite']) * 100 : 0;
                                ?>
                                <div class="space-y-4">
                                    <div class="flex justify-between text-sm">
                                        <span>Distribué</span>
                                        <span><?= number_format($pourcentage, 1) ?>%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar <?= $pourcentage >= 100 ? 'bg-success' : 'bg-primary' ?>" 
                                             style="width: <?= min($pourcentage, 100) ?>%"></div>
                                    </div>
                                    <div class="flex justify-between text-sm text-gray-600">
                                        <span><?= number_format($distribue, 0, ',', ' ') ?> distribué</span>
                                        <span><?= number_format($remaining_quantity, 0, ',', ' ') ?> restant</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions Card -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="bi bi-gear text-primary"></i>
                                    Actions
                                </h3>
                            </div>
                            <div class="card-body space-y-3">
                                <?php if ($remaining_quantity > 0): ?>
                                    <a href="<?= Flight::get('flight.base_url') ?>/don-global/simulation?don_id=<?= $don_global['id'] ?>" 
                                       class="btn btn-primary w-full">
                                        <i class="bi bi-diagram-3"></i>
                                        Simuler Distribution
                                    </a>
                                    <a href="<?= Flight::get('flight.base_url') ?>/don-global/distribution-manuelle?don_id=<?= $don_global['id'] ?>" 
                                       class="btn btn-outline-primary w-full">
                                        <i class="bi bi-hand-index"></i>
                                        Distribution Manuelle
                                    </a>
                                <?php endif; ?>
                                
                                <a href="<?= Flight::get('flight.base_url') ?>/don-global/<?= $don_global['id'] ?>/rapport" 
                                   class="btn btn-outline-secondary w-full">
                                    <i class="bi bi-file-earmark-text"></i>
                                    Générer Rapport
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historique des Distributions -->
                <?php if (!empty($distributions)): ?>
                <div class="card mt-8">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-clock-history text-primary"></i>
                            Historique des Distributions
                        </h3>
                        <div class="card-header-actions">
                            <span class="badge badge-success"><?= count($distributions) ?> distributions</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Ville</th>
                                        <th>Quantité</th>
                                        <th>Méthode</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($distributions as $distribution): ?>
                                    <tr>
                                        <td>
                                            <div class="flex items-center space-x-2">
                                                <i class="bi bi-calendar3 text-gray-500"></i>
                                                <span><?= date('d/m/Y', strtotime($distribution['date_distribution'])) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex items-center space-x-2">
                                                <i class="bi bi-geo-alt text-gray-500"></i>
                                                <span><?= htmlspecialchars($distribution['ville_nom'] ?? 'N/A') ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="font-semibold text-primary">
                                                <?= number_format($distribution['quantite'], 0, ',', ' ') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-outline-primary">
                                                <?= htmlspecialchars($distribution['methode'] ?? 'Standard') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $distribution['status'] === 'termine' ? 'success' : ($distribution['status'] === 'en_cours' ? 'warning' : 'secondary') ?>">
                                                <?= ucfirst(str_replace('_', ' ', $distribution['status'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= Flight::get('flight.base_url') ?>/distribution/<?= $distribution['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> Voir
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="card mt-8">
                    <div class="card-body text-center py-12">
                        <div class="text-gray-400 mb-4">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">Aucune Distribution</h3>
                        <p class="text-gray-500 mb-6">Ce don global n'a pas encore été distribué.</p>
                        <?php if ($remaining_quantity > 0): ?>
                            <a href="<?= Flight::get('flight.base_url') ?>/don-global/simulation?don_id=<?= $don_global['id'] ?>" 
                               class="btn btn-primary">
                                <i class="bi bi-diagram-3"></i>
                                Créer une Distribution
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>

    <script src="<?= Flight::get('flight.base_url') ?>/js/app.js"></script>
    <script>
    // Auto-refresh page every 30 seconds if there are active distributions
    <?php if (!empty($distributions) && array_filter($distributions, fn($d) => $d['status'] === 'en_cours')): ?>
    setTimeout(() => {
        window.location.reload();
    }, 30000);
    <?php endif; ?>
    </script>
</body>
</html>