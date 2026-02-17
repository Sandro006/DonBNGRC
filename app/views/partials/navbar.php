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
                <a href="<?= Flight::get('flight.base_url') ?>/don-global/nouveau"><i class="bi bi-gift"></i> Créer Don Global</a>
            </li>
            <li class="header-nav-item">
                <a href="<?= Flight::get('flight.base_url') ?>/don-global"><i class="bi bi-box-seam"></i> Dons Globaux</a>
            </li>
            <li class="header-nav-item">
                <a href="<?= Flight::get('flight.base_url') ?>/don-global/methodes"><i class="bi bi-diagram-2"></i> Méthodes Distribution</a>
            </li>
            <li class="header-nav-item">
                <a href="<?= Flight::get('flight.base_url') ?>/simulation"><i class="bi bi-diagram-3"></i> Simulation</a>
            </li>
            <li class="header-nav-item">
                <a href="<?= Flight::get('flight.base_url') ?>/achat"><i class="bi bi-cart3"></i> Achats</a>
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