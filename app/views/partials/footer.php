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
                        <li style="margin-bottom: var(--spacing-2);"><a href="<?= Flight::get('flight.base_url') ?>/don-global/nouveau" style="color: var(--gray-400); text-decoration: none; transition: color var(--transition-fast);"><i class="bi bi-gift"></i> Créer Don Global</a></li>
                        <li style="margin-bottom: var(--spacing-2);"><a href="<?= Flight::get('flight.base_url') ?>/don-global" style="color: var(--gray-400); text-decoration: none; transition: color var(--transition-fast);"><i class="bi bi-box-seam"></i> Dons Globaux</a></li>
                        <li style="margin-bottom: var(--spacing-2);"><a href="<?= Flight::get('flight.base_url') ?>/achat" style="color: var(--gray-400); text-decoration: none; transition: color var(--transition-fast);"><i class="bi bi-cart3"></i> Achats</a></li>
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
                <p>ETU004168 & ETU004349 & ETU004123</p>
            </div>
        </div>
    </footer>