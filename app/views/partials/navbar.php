<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-container">
        <a href="<?= Flight::get('flight.base_url') ?>" class="sidebar-brand">
            <div class="sidebar-brand-icon">🌊</div>
            <div>
                <h1>BNGRC</h1>
                <small>Gestion des Risques</small>
            </div>
        </a>

        <nav class="sidebar-nav">
            <ul>
                <li class="sidebar-item"><a href="<?= Flight::get('flight.base_url') ?>"><i class="bi bi-house-door"></i> Accueil</a></li>
                <li class="sidebar-item"><a href="<?= Flight::get('flight.base_url') ?>/dashboard"><i class="bi bi-speedometer2"></i> Tableau de bord</a></li>
                <li class="sidebar-item"><a href="<?= Flight::get('flight.base_url') ?>/don-global/nouveau"><i class="bi bi-gift"></i> Créer Don Global</a></li>
                <li class="sidebar-item"><a href="<?= Flight::get('flight.base_url') ?>/don-global"><i class="bi bi-box-seam"></i> Dons Globaux</a></li>
                <li class="sidebar-item"><a href="<?= Flight::get('flight.base_url') ?>/don-global/methodes"><i class="bi bi-diagram-2"></i> Méthodes Distribution</a></li>
                <li class="sidebar-item"><a href="<?= Flight::get('flight.base_url') ?>/besoin"><i class="bi bi-list-check"></i> Liste Besoins</a></li>
                <li class="sidebar-item"><a href="<?= Flight::get('flight.base_url') ?>/besoin/create"><i class="bi bi-exclamation-triangle"></i> Nouveau Besoin</a></li>
                <li class="sidebar-item"><a href="<?= Flight::get('flight.base_url') ?>/achat"><i class="bi bi-cart3"></i> Achats</a></li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-user-avatar">AD</div>
                <div class="sidebar-user-info">
                    <span>Admin</span>
                </div>
            </div>
        </div>
    </div>
</aside>