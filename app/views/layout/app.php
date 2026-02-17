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
    <?php include 'partials/navbar.php'; ?>

    <!-- Main Content -->
    <main class="layout-content" style="padding: var(--spacing-8); min-height: calc(100vh - 200px);">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <?php include 'partials/footer.php'; ?>

    <script src="<?= Flight::get('flight.base_url') ?>/js/app.js"></script>
</body>
</html>
