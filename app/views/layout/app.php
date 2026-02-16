<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'BNGRC' ?></title>
    
    <!-- Design System -->
    <link href="<?= Flight::get('flight.base_url') ?>/css/design-system.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/components.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/layout.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/utilities.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/pages.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/custom.css" rel="stylesheet" />
    
    <!-- Bootstrap Icons -->
    <link href="<?= Flight::get('flight.base_url') ?>/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet" />
</head>
<body>
    <!-- Navigation Header -->
    <nav class="header">
        <div class="header-container">
            <a href="<?= Flight::get('flight.base_url') ?>" class="header-brand">
                <div class="header-brand-icon">🌊</div>
                <div>
                    <h1>BNGRC</h1>
                    <small>Gestion des Risques</small>
                </div>
            </a>
            <ul class="header-nav">
                <li class="header-nav-item">
                    <a href="<?= Flight::get('flight.base_url') ?>"><i class="bi bi-house-door"></i> Accueil</a>
                </li>
                <li class="header-nav-item">
                    <a href="<?= Flight::get('flight.base_url') ?>/dashboard"><i class="bi bi-speedometer2"></i> Tableau de bord</a>
                </li>
                <li class="header-nav-item">
                    <a href="<?= Flight::get('flight.base_url') ?>/don/ajouter"><i class="bi bi-gift"></i> Faire un don</a>
                </li>
                <li class="header-nav-item">
                    <a href="<?= Flight::get('flight.base_url') ?>/simulation"><i class="bi bi-diagram-3"></i> Simulation</a>
                </li>
            </ul>
            <div class="header-actions">
                <div class="header-user">
                    <div class="header-user-avatar">AD</div>
                    <span>Admin</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="layout-content" style="padding: var(--spacing-8); min-height: calc(100vh - 200px);">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer style="background: var(--gray-900); color: var(--gray-400); padding: var(--spacing-8) var(--spacing-4);">
        <div style="max-width: 1200px; margin: 0 auto;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--spacing-8); margin-bottom: var(--spacing-6);">
                <div>
                    <h5 style="color: white; font-size: var(--font-size-lg); margin-bottom: var(--spacing-4); font-weight: 700;">À propos</h5>
                    <p style="line-height: 1.7;">Bureau National de Gestion des Risques et Catastrophes - Ensemble pour une Madagascar plus résiliente.</p>
                </div>
                <div>
                    <h5 style="color: white; font-size: var(--font-size-lg); margin-bottom: var(--spacing-4); font-weight: 700;">Liens rapides</h5>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li style="margin-bottom: var(--spacing-2);"><a href="<?= Flight::get('flight.base_url') ?>" style="color: var(--gray-400); text-decoration: none; transition: color var(--transition-fast);"><i class="bi bi-house-door"></i> Accueil</a></li>
                        <li style="margin-bottom: var(--spacing-2);"><a href="<?= Flight::get('flight.base_url') ?>/dashboard" style="color: var(--gray-400); text-decoration: none; transition: color var(--transition-fast);"><i class="bi bi-speedometer2"></i> Tableau de bord</a></li>
                        <li style="margin-bottom: var(--spacing-2);"><a href="<?= Flight::get('flight.base_url') ?>/don/ajouter" style="color: var(--gray-400); text-decoration: none; transition: color var(--transition-fast);"><i class="bi bi-gift"></i> Faire un don</a></li>
                    </ul>
                </div>
                <div>
                    <h5 style="color: white; font-size: var(--font-size-lg); margin-bottom: var(--spacing-4); font-weight: 700;">Contact</h5>
                    <p style="line-height: 1.7;">
                        <i class="bi bi-geo-alt"></i> Antananarivo, Madagascar<br>
                        <i class="bi bi-telephone"></i> +261 20 22 XXX XX<br>
                        <i class="bi bi-envelope"></i> contact@bngrc.mg
                    </p>
                </div>
            </div>
            <hr style="border-color: var(--gray-700); margin: var(--spacing-6) 0;">
            <div style="text-align: center;">
                <p>&copy; <?= date('Y') ?> BNGRC. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="<?= Flight::get('flight.base_url') ?>/js/app.js"></script>
</body>
</html>
