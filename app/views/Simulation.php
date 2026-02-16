<!DOCTYPE html>
<html class="light" lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Simulation - BNGRC</title>
    
    <!-- Design System -->
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/design-system.css" rel="stylesheet" />
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/components.css" rel="stylesheet" />
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/layout.css" rel="stylesheet" />
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/utilities.css" rel="stylesheet" />
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/pages.css" rel="stylesheet" />
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/custom.css" rel="stylesheet" />
    
    <!-- Bootstrap Icons -->
    <link href="<?php echo Flight::get('flight.base_url'); ?>/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet" />

    <style>
        /* Simulation Detail Styles */
        .sim-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: var(--spacing-6);
            margin-bottom: var(--spacing-8);
        }

        .sim-info-item {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-1);
            padding: var(--spacing-3) 0;
        }

        .sim-info-item + .sim-info-item {
            border-top: 1px solid var(--border-color);
        }

        .sim-info-label {
            font-size: var(--font-size-xs);
            color: var(--text-tertiary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }

        .sim-info-value {
            font-size: var(--font-size-base);
            font-weight: 600;
            color: var(--text-primary);
        }

        .sim-quantity-highlight {
            font-size: var(--font-size-2xl);
            font-weight: 700;
            color: var(--primary);
        }

        .sim-quantity-unit {
            font-size: var(--font-size-xs);
            font-weight: 500;
            color: var(--text-secondary);
        }

        .dispatch-list {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-4);
        }

        .dispatch-item {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: var(--spacing-4) var(--spacing-6);
            transition: all var(--transition-base);
        }

        .dispatch-item:hover {
            border-color: var(--primary-200);
            box-shadow: var(--shadow-md);
        }

        .dispatch-item-header {
            display: flex;
            align-items: center;
            gap: var(--spacing-2);
            margin-bottom: var(--spacing-3);
            flex-wrap: wrap;
        }

        .dispatch-item-desc {
            font-size: var(--font-size-sm);
            color: var(--text-secondary);
            margin-bottom: var(--spacing-4);
        }

        .dispatch-flow {
            display: flex;
            align-items: center;
            gap: var(--spacing-4);
            flex-wrap: wrap;
            justify-content: center;
            background: var(--bg-secondary);
            border-radius: var(--radius-md);
            padding: var(--spacing-3) var(--spacing-4);
        }

        .dispatch-flow-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: var(--spacing-1);
        }

        .dispatch-flow-arrow {
            color: var(--text-tertiary);
            font-size: var(--font-size-xl);
        }

        .sim-actions {
            display: flex;
            gap: var(--spacing-4);
            justify-content: center;
            flex-wrap: wrap;
            margin: var(--spacing-8) 0;
        }

        .sim-section-title {
            display: flex;
            align-items: center;
            gap: var(--spacing-2);
            margin-bottom: var(--spacing-6);
        }

        .sim-section-title h3 {
            margin: 0;
            font-size: var(--font-size-xl);
            font-weight: 700;
        }

        .sim-section-title i {
            color: var(--primary);
            font-size: var(--font-size-xl);
        }

        @media (max-width: 768px) {
            .sim-info-grid {
                grid-template-columns: 1fr;
            }

            .dispatch-flow {
                flex-direction: column;
                gap: var(--spacing-2);
            }

            .dispatch-flow-arrow {
                transform: rotate(90deg);
            }

            .dispatch-item {
                padding: var(--spacing-4);
            }
        }
    </style>
</head>

<body>
    <!-- Layout Container -->
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
                    <a href="<?= Flight::get('flight.base_url') ?>/simulation" class="sidebar-menu-link active">
                        <i class="bi bi-diagram-3"></i>
                        <span>Simulation</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <div class="layout-main">
            <!-- Header -->
            <header class="header">
                <div class="header-container">
                    <button class="sidebar-toggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 style="margin: 0; font-size: 1.25rem; font-weight: 700;">📊 BNGRC - Simulation</h1>
                    <div class="header-actions" style="margin-left: auto;">
                        <div class="header-user">
                            <div class="header-user-avatar">AD</div>
                            <span style="font-size: 0.875rem;">Admin</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="layout-content">

                <?php if (!empty($is_list)): ?>
                <!-- ============================================ -->
                <!-- SIMULATION LIST VIEW                         -->
                <!-- ============================================ -->

                    <!-- BREADCRUMB -->
                    <div class="breadcrumb-nav">
                        <ol>
                            <li><a href="<?= Flight::get('flight.base_url') ?>">Accueil</a></li>
                            <li>Simulation</li>
                        </ol>
                    </div>

                    <!-- PAGE HEADER -->
                    <div class="page-header">
                        <div class="page-title">
                            <h1><i class="bi bi-diagram-3"></i> Simulations</h1>
                            <p>Sélectionnez un don pour simuler sa distribution</p>
                        </div>
                    </div>

                    <!-- DONATIONS LIST -->
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h5 style="margin: 0; color: white; display: flex; align-items: center; gap: var(--spacing-2);">
                                <i class="bi bi-list-ul"></i>
                                Dons disponibles pour simulation
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($dons)): ?>
                                <div class="cards-grid">
                                    <?php foreach ($dons as $d): ?>
                                        <div class="card" style="cursor: pointer;">
                                            <div class="card-body">
                                                <h4 style="margin-top: 0; color: var(--primary);">
                                                    <i class="bi bi-gift"></i>
                                                    <?= htmlspecialchars($d['categorie_nom'] ?? 'Sans catégorie') ?>
                                                </h4>
                                                <p style="margin: var(--spacing-2) 0; color: var(--text-secondary);">
                                                    <strong>Quantité:</strong> <?= htmlspecialchars($d['quantite'] ?? 0) ?>
                                                </p>
                                                <p style="margin: var(--spacing-2) 0; color: var(--text-secondary);">
                                                    <strong>Ville:</strong> <?= htmlspecialchars($d['ville_nom'] ?? 'Non spécifiée') ?>
                                                </p>
                                                <p style="margin: var(--spacing-2) 0; color: var(--text-secondary);">
                                                    <strong>Donateur:</strong> <?= htmlspecialchars($d['donateur_nom'] ?? 'Anonyme') ?>
                                                </p>
                                                <a href="<?= Flight::get('flight.base_url') ?>/simulation/<?= htmlspecialchars($d['id']) ?>" class="btn btn-primary btn-block" style="margin-top: var(--spacing-3);">
                                                    <i class="bi bi-play"></i> Simuler la distribution
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-inbox"></i>
                                    </div>
                                    <div class="empty-state-title">Aucun don disponible</div>
                                    <p class="empty-state-description">Aucun don disponible pour la simulation</p>
                                    <div class="empty-state-action">
                                        <a href="<?= Flight::get('flight.base_url') ?>/don/ajouter" class="btn btn-primary">
                                            <i class="bi bi-plus"></i> Ajouter un don
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php else: ?>
                <!-- ============================================ -->
                <!-- SIMULATION DETAIL VIEW                       -->
                <!-- ============================================ -->

                    <!-- BREADCRUMB -->
                    <div class="breadcrumb-nav">
                        <ol>
                            <li><a href="<?= Flight::get('flight.base_url') ?>">Accueil</a></li>
                            <li><a href="<?= Flight::get('flight.base_url') ?>/simulation">Simulation</a></li>
                            <li>Dispatch #<?= (int)$don_id ?></li>
                        </ol>
                    </div>

                    <!-- PAGE HEADER -->
                    <div class="page-header">
                        <div class="page-title">
                            <h1><i class="bi bi-diagram-3"></i> Simulation de Dispatch</h1>
                            <p>Visualiser et valider la distribution du don sur les besoins</p>
                        </div>
                        <div class="page-actions">
                            <a href="<?= Flight::get('flight.base_url') ?>/simulation" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Retour
                            </a>
                        </div>
                    </div>

                    <!-- DONATION & DONOR INFO CARDS -->
                    <div class="sim-info-grid">
                        <!-- Donation Info -->
                        <div class="card">
                            <div class="card-header bg-primary">
                                <h5 style="margin: 0; color: white; display: flex; align-items: center; gap: var(--spacing-2);">
                                    <i class="bi bi-gift"></i> Détails du Don
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="sim-info-item">
                                    <span class="sim-info-label">Donateur</span>
                                    <span class="sim-info-value"><?= htmlspecialchars($don['donateur_nom'] ?? 'Anonyme') ?></span>
                                </div>
                                <div class="sim-info-item">
                                    <span class="sim-info-label">Catégorie</span>
                                    <span><span class="badge badge-primary"><?= htmlspecialchars($don['categorie_nom'] ?? '-') ?></span></span>
                                </div>
                                <div class="sim-info-item">
                                    <span class="sim-info-label">Localisation</span>
                                    <span class="sim-info-value"><?= htmlspecialchars($don['ville_nom'] ?? '-') ?></span>
                                    <span class="text-sm text-gray-500"><?= htmlspecialchars($don['region_nom'] ?? '-') ?></span>
                                </div>
                                <div class="sim-info-item" style="border-top: 2px solid var(--primary-100);">
                                    <span class="sim-info-label">Quantité totale</span>
                                    <span class="sim-quantity-highlight">
                                        <?= (int)$don['quantite'] ?> <span class="sim-quantity-unit">unités</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Donor Info -->
                        <div class="card">
                            <div class="card-header" style="background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-800) 100%); color: white;">
                                <h5 style="margin: 0; color: white; display: flex; align-items: center; gap: var(--spacing-2);">
                                    <i class="bi bi-person-circle"></i> Donateur
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="sim-info-item">
                                    <span class="sim-info-label">Email</span>
                                    <a href="mailto:<?= htmlspecialchars($don['donateur_email'] ?? '') ?>" style="color: var(--primary); text-decoration: none;">
                                        <?= htmlspecialchars($don['donateur_email'] ?? '-') ?>
                                    </a>
                                </div>
                                <div class="sim-info-item">
                                    <span class="sim-info-label">Téléphone</span>
                                    <span class="sim-info-value"><?= htmlspecialchars($don['donateur_telephone'] ?? '-') ?></span>
                                </div>
                                <div class="sim-info-item">
                                    <span class="sim-info-label">Date du don</span>
                                    <span class="sim-info-value" style="display: flex; align-items: center; gap: var(--spacing-2);">
                                        <i class="bi bi-calendar" style="color: var(--primary);"></i>
                                        <?= date('d/m/Y H:i', strtotime($don['date_don'] ?? 'now')) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STATISTICS ROW -->
                    <div class="stats-row">
                        <div class="stat-card">
                            <div class="stat-icon" style="background-color: var(--primary-100); color: var(--primary);">
                                <i class="bi bi-boxes"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-label">Total à dispatcher</span>
                                <span class="stat-value" id="stat-total" style="color: var(--primary);"><?= (int)$don['quantite'] ?></span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon" style="background-color: var(--info-100); color: var(--info);">
                                <i class="bi bi-search"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-label">Besoins trouvés</span>
                                <span class="stat-value" id="stat-needs" style="color: var(--info);"><?= count($simulation['dispatch_results'] ?? []) ?></span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon" style="background-color: var(--success-100); color: var(--success);">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-label">Besoins satisfaits</span>
                                <span class="stat-value" id="stat-satisfied" style="color: var(--success);">
                                    <?php
                                    $satisfied = 0;
                                    if (!empty($simulation['dispatch_results'])) {
                                        foreach ($simulation['dispatch_results'] as $dr) {
                                            if (((int)($dr['new_need_quantity'] ?? 0)) === 0) {
                                                $satisfied++;
                                            }
                                        }
                                    }
                                    echo $satisfied;
                                    ?>
                                </span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon" style="background-color: var(--warning-100); color: var(--warning);">
                                <i class="bi bi-exclamation-circle"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-label">Quantité restante</span>
                                <span class="stat-value" id="stat-remaining" style="color: var(--warning);"><?= (int)$simulation['remaining_quantity'] ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- SIMULATION RESULTS -->
                    <div id="simulation-container">
                        <?php if (!empty($simulation['dispatch_results'])): ?>

                            <!-- Info Alert -->
                            <div class="alert alert-info" style="margin-bottom: var(--spacing-6);">
                                <i class="bi bi-info-circle" style="font-size: var(--font-size-xl); flex-shrink: 0;"></i>
                                <div>
                                    <strong>Résultats de la simulation</strong>
                                    <p style="margin: var(--spacing-1) 0 0 0;">
                                        La donation sera dispatchée sur <strong><?= count($simulation['dispatch_results']) ?> besoin(s)</strong>.
                                    </p>
                                </div>
                            </div>

                            <!-- Section Title -->
                            <div class="sim-section-title">
                                <i class="bi bi-arrow-left-right"></i>
                                <h3>Distribution des besoins</h3>
                            </div>

                            <!-- Dispatch Items -->
                            <div class="dispatch-list">
                                <?php foreach ($simulation['dispatch_results'] as $index => $dispatch): ?>
                                    <div class="dispatch-item">
                                        <!-- Header -->
                                        <div class="dispatch-item-header">
                                            <span class="badge badge-primary">Besoin #<?= htmlspecialchars($dispatch['besoin_id']) ?></span>
                                            <span class="badge badge-gray">
                                                <i class="bi bi-geo-alt"></i>&nbsp;<?= htmlspecialchars($dispatch['besoin_ville_nom'] ?? '-') ?>
                                            </span>
                                        </div>

                                        <!-- Description -->
                                        <p class="dispatch-item-desc">
                                            <?= htmlspecialchars($dispatch['besoin_description'] ?? 'Sans description') ?>
                                        </p>

                                        <!-- Dispatch Flow -->
                                        <div class="dispatch-flow">
                                            <div class="dispatch-flow-step">
                                                <span class="sim-info-label">Besoin</span>
                                                <span class="badge badge-warning" style="font-size: var(--font-size-sm); padding: var(--spacing-2) var(--spacing-3);">
                                                    <?= (int)$dispatch['besoin_quantite_needed'] ?>
                                                </span>
                                            </div>

                                            <i class="bi bi-arrow-right dispatch-flow-arrow"></i>

                                            <div class="dispatch-flow-step">
                                                <span class="sim-info-label">Dispatché</span>
                                                <span class="badge badge-success" style="font-size: var(--font-size-sm); padding: var(--spacing-2) var(--spacing-3);">
                                                    <?= (int)$dispatch['dispatched_quantity'] ?>
                                                </span>
                                            </div>

                                            <i class="bi bi-arrow-right dispatch-flow-arrow"></i>

                                            <div class="dispatch-flow-step">
                                                <span class="sim-info-label">Reste</span>
                                                <span class="badge <?= ((int)$dispatch['new_need_quantity'] === 0) ? 'badge-success' : 'badge-danger' ?>" style="font-size: var(--font-size-sm); padding: var(--spacing-2) var(--spacing-3);">
                                                    <?= (int)$dispatch['new_need_quantity'] ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if ($simulation['remaining_quantity'] > 0): ?>
                                <div class="alert alert-warning" style="margin-top: var(--spacing-6);">
                                    <i class="bi bi-exclamation-triangle" style="font-size: var(--font-size-xl); flex-shrink: 0;"></i>
                                    <div>
                                        <strong>Quantité non utilisée</strong>
                                        <p style="margin: var(--spacing-1) 0 0 0;">
                                            <?= (int)$simulation['remaining_quantity'] ?> unité(s) restante(s) après satisfaction de tous les besoins.
                                            Cette quantité peut être conservée pour d'autres distributions.
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>

                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-circle" style="font-size: var(--font-size-xl); flex-shrink: 0;"></i>
                                <div>
                                    <strong>Aucun besoin trouvé</strong>
                                    <p style="margin: var(--spacing-1) 0 0 0;">
                                        Aucun besoin correspondant à cette catégorie et cette ville n'a été trouvé.
                                    </p>
                                </div>
                            </div>

                        <?php endif; ?>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="sim-actions">
                        <button class="btn btn-secondary btn-lg" id="btn-simulate" title="Rafraîchir la simulation">
                            <i class="bi bi-arrow-repeat"></i>
                            <span>Rafraîchir</span>
                            <span class="loading" id="loading-simulate" style="display: none;">
                                <span class="spinner spinner-sm"></span>
                            </span>
                        </button>
                        <button class="btn btn-success btn-lg" id="btn-validate"
                                <?= empty($simulation['dispatch_results']) ? 'disabled' : '' ?>
                                title="Valider et exécuter le dispatch">
                            <i class="bi bi-check-circle"></i>
                            <span>Valider le Dispatch</span>
                            <span class="loading" id="loading-validate" style="display: none;">
                                <span class="spinner spinner-sm"></span>
                            </span>
                        </button>
                    </div>

                    <!-- Alert Container -->
                    <div id="alert-container"></div>

                <?php endif; ?>

                <!-- FOOTER -->
                <footer class="layout-footer" style="margin-top: auto;">
                    <p>&copy; <?= date('Y') ?> Bureau National de Gestion des Risques et Catastrophes (BNGRC). Tous droits réservés.</p>
                </footer>

            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script nonce="<?= Flight::get('csp_nonce') ?>" src="<?php echo Flight::get('flight.base_url'); ?>/js/app.js"></script>

    <?php if (empty($is_list)): ?>
    <script nonce="<?= Flight::get('csp_nonce') ?>" src="<?php echo Flight::get('flight.base_url'); ?>/js/simulation.js" 
            data-base-url="<?= Flight::get('flight.base_url') ?>" 
            data-don-id="<?= (int)$don_id ?>"></script>
    <?php endif; ?>

    <!-- Responsive sidebar toggle script -->
    <script nonce="<?= Flight::get('csp_nonce') ?>">
        document.querySelector('.sidebar-toggle')?.addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('show');
        });
    </script>
</body>

</html>
