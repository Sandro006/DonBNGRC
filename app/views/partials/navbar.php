<!-- Sidebar Navigation -->
<aside class="sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <div class="sidebar-brand">🌊</div>
        <div class="sidebar-brand-text">
            <h2>BNGRC</h2>
            <small>Risk Management</small>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <ul class="sidebar-menu">
        <li class="sidebar-menu-item">
            <a href="<?= Flight::get('flight.base_url') ?>" class="sidebar-menu-link">
                <i class="bi bi-house-door"></i>
                <span>Accueil</span>
            </a>
        </li>
        <li class="sidebar-menu-item">
            <a href="<?= Flight::get('flight.base_url') ?>/dashboard" class="sidebar-menu-link">
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
            <a href="<?= Flight::get('flight.base_url') ?>/don-global/nouveau" class="sidebar-menu-link">
                <i class="bi bi-gift"></i>
                <span>Créer Don Global</span>
            </a>
        </li>
        <li class="sidebar-menu-item">
            <a href="<?= Flight::get('flight.base_url') ?>/don-global/methodes" class="sidebar-menu-link">
                <i class="bi bi-diagram-2"></i>
                <span>Méthodes Distribution</span>
            </a>
        </li>
        <li class="sidebar-menu-item">
            <a href="<?= Flight::get('flight.base_url') ?>/besoin" class="sidebar-menu-link">
                <i class="bi bi-list-check"></i>
                <span>Liste Besoins</span>
            </a>
        </li>
        <li class="sidebar-menu-item">
            <a href="<?= Flight::get('flight.base_url') ?>/besoin/create" class="sidebar-menu-link">
                <i class="bi bi-exclamation-triangle"></i>
                <span>Nouveau Besoin</span>
            </a>
        </li>
        <li class="sidebar-menu-item">
            <a href="<?= Flight::get('flight.base_url') ?>/achat" class="sidebar-menu-link">
                <i class="bi bi-cart3"></i>
                <span>Achats</span>
            </a>
        </li>
    </ul>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="sidebar-footer-item">
            <div style="display: flex; align-items: center; gap: var(--spacing-2); width: 100%;">
                <div class="header-user-avatar" style="width: 32px; height: 32px; font-size: var(--font-size-sm);">AD</div>
                <span>Admin</span>
            </div>
        </div>
    </div>
</aside>