<!DOCTYPE html>
<html class="light" lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Accueil - BNGRC</title>
    
    <!-- Design System -->
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/design-system.css" rel="stylesheet" />
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/components.css" rel="stylesheet" />
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/layout.css" rel="stylesheet" />
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/utilities.css" rel="stylesheet" />
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/pages.css" rel="stylesheet" />
    
    <!-- Bootstrap Icons -->
    <link href="<?php echo Flight::get('flight.base_url'); ?>/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet" />
    
    <style>
        .hero-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: var(--spacing-16) var(--spacing-4);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.5;
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hero-title {
            font-size: var(--font-size-4xl);
            font-weight: 700;
            margin-bottom: var(--spacing-4);
            line-height: 1.2;
        }
        
        .hero-subtitle {
            font-size: var(--font-size-xl);
            opacity: 0.9;
            margin-bottom: var(--spacing-8);
        }
        
        .hero-buttons {
            display: flex;
            gap: var(--spacing-4);
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .hero-btn {
            padding: var(--spacing-4) var(--spacing-8);
            font-size: var(--font-size-lg);
            border-radius: var(--radius-xl);
            font-weight: 600;
            transition: all var(--transition-base);
        }
        
        .hero-btn-primary {
            background: white;
            color: var(--primary);
            border: 2px solid white;
        }
        
        .hero-btn-primary:hover {
            background: transparent;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .hero-btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.5);
        }
        
        .hero-btn-secondary:hover {
            border-color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        
        .features-section {
            padding: var(--spacing-16) var(--spacing-4);
            background: var(--bg-primary);
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: var(--spacing-8);
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .feature-card {
            text-align: center;
            padding: var(--spacing-8);
            background: var(--bg-primary);
            border-radius: var(--radius-xl);
            border: 1px solid var(--border-color);
            transition: all var(--transition-base);
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
            border-color: var(--primary-200);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto var(--spacing-6);
            background: linear-gradient(135deg, var(--primary-100) 0%, var(--primary-200) 100%);
            border-radius: var(--radius-2xl);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--primary);
        }
        
        .feature-title {
            font-size: var(--font-size-xl);
            font-weight: 600;
            margin-bottom: var(--spacing-3);
            color: var(--text-primary);
        }
        
        .feature-description {
            color: var(--text-secondary);
            line-height: 1.7;
        }
        
        .stats-section {
            padding: var(--spacing-12) var(--spacing-4);
            background: var(--bg-secondary);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--spacing-6);
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .stat-item {
            text-align: center;
            padding: var(--spacing-6);
        }
        
        .stat-number {
            font-size: var(--font-size-4xl);
            font-weight: 700;
            color: var(--primary);
            line-height: 1;
            margin-bottom: var(--spacing-2);
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .cta-section {
            padding: var(--spacing-16) var(--spacing-4);
            background: linear-gradient(135deg, var(--success) 0%, var(--success-700) 100%);
            color: white;
            text-align: center;
        }
        
        .cta-title {
            font-size: var(--font-size-3xl);
            font-weight: 700;
            margin-bottom: var(--spacing-4);
        }
        
        .cta-description {
            font-size: var(--font-size-lg);
            opacity: 0.9;
            margin-bottom: var(--spacing-8);
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .footer-section {
            padding: var(--spacing-8) var(--spacing-4);
            background: var(--gray-900);
            color: var(--gray-400);
            text-align: center;
        }
        
        .footer-links {
            display: flex;
            gap: var(--spacing-6);
            justify-content: center;
            margin-bottom: var(--spacing-6);
            flex-wrap: wrap;
        }
        
        .footer-links a {
            color: var(--gray-400);
            text-decoration: none;
            transition: color var(--transition-fast);
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .section-title {
            text-align: center;
            font-size: var(--font-size-3xl);
            font-weight: 700;
            margin-bottom: var(--spacing-4);
            color: var(--text-primary);
        }
        
        .section-subtitle {
            text-align: center;
            color: var(--text-secondary);
            margin-bottom: var(--spacing-12);
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: var(--font-size-3xl);
            }
            
            .hero-subtitle {
                font-size: var(--font-size-lg);
            }
            
            .hero-btn {
                padding: var(--spacing-3) var(--spacing-6);
                font-size: var(--font-size-base);
            }
        }
    </style>
</head>

<<<<<<< Updated upstream
<body>
    <!-- Navigation -->
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
                    <a href="<?= Flight::get('flight.base_url') ?>" class="active">Accueil</a>
                </li>
                <li class="header-nav-item">
                    <a href="<?= Flight::get('flight.base_url') ?>/dashboard">Tableau de bord</a>
                </li>
                <li class="header-nav-item">
                    <a href="<?= Flight::get('flight.base_url') ?>/don/ajouter">Faire un don</a>
                </li>
            </ul>
        </div>
    </nav>

=======
<<<<<<< Updated upstream
<?php if (!empty($besoin_stats)) { ?>
    <h4>Besoins</h4>
    <ul>
        <li>Total de besoins: <?= htmlspecialchars($besoin_stats['total_needs'] ?? 0) ?></li>
        <li>Quantité totale: <?= htmlspecialchars($besoin_stats['total_quantity'] ?? 0) ?></li>
        <li>Montant total estimé: <?= htmlspecialchars($besoin_stats['total_amount'] ?? 0) ?></li>
    </ul>
<?php } ?>

<?php if (!empty($cities)) { ?>
    <h4>Villes (extrait)</h4>
    <ul>
        <?php foreach (array_slice($cities, 0, 10) as $c) { ?>
            <li><?= htmlspecialchars($c['nom'] ?? ($c['name'] ?? '')) ?> - Région: <?= htmlspecialchars($c['region_nom'] ?? '') ?></li>
        <?php } ?>
    </ul>
<?php } ?>
=======
<body>
    <!-- Navigation -->
    <nav class="header">
        <div class="header-container">
            <a href="<?= Flight::get('flight.base_path') ?>" class="header-brand">
                <div class="header-brand-icon">🌊</div>
                <div>
                    <h1>BNGRC</h1>
                    <small>Gestion des Risques</small>
                </div>
            </a>
            <ul class="header-nav">
                <li class="header-nav-item">
                    <a href="<?= Flight::get('flight.base_path') ?>" class="active">Accueil</a>
                </li>
                <li class="header-nav-item">
                    <a href="<?= Flight::get('flight.base_path') ?>/dashboard">Tableau de bord</a>
                </li>
                <li class="header-nav-item">
                    <a href="<?= Flight::get('flight.base_path') ?>/don/ajouter">Faire un don</a>
                </li>
            </ul>
        </div>
    </nav>

>>>>>>> Stashed changes
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Bureau National de Gestion des Risques et Catastrophes</h1>
            <p class="hero-subtitle">Ensemble, aidons les communautés touchées par les catastrophes naturelles à Madagascar</p>
            <div class="hero-buttons">
<<<<<<< Updated upstream
                <a href="<?= Flight::get('flight.base_url') ?>/don/ajouter" class="btn hero-btn hero-btn-primary">
                    <i class="bi bi-gift"></i> Faire un don
                </a>
                <a href="<?= Flight::get('flight.base_url') ?>/dashboard" class="btn hero-btn hero-btn-secondary">
=======
                <a href="<?= Flight::get('flight.base_path') ?>/don/ajouter" class="btn hero-btn hero-btn-primary">
                    <i class="bi bi-gift"></i> Faire un don
                </a>
                <a href="<?= Flight::get('flight.base_path') ?>/dashboard" class="btn hero-btn hero-btn-secondary">
>>>>>>> Stashed changes
                    <i class="bi bi-bar-chart"></i> Voir le tableau de bord
                </a>
            </div>
        </div>
    </section>
<<<<<<< Updated upstream
=======
>>>>>>> Stashed changes
>>>>>>> Stashed changes

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">23</div>
                <div class="stat-label">Régions couvertes</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">150+</div>
                <div class="stat-label">Dons collectés</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">500+</div>
                <div class="stat-label">Familles aidées</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Support actif</div>
            </div>
        </div>
    </section>

<<<<<<< Updated upstream
=======
<<<<<<< Updated upstream
<?php if (!empty($recent_besoins)) { ?>
    <h4>Derniers besoins</h4>
    <table border="1" cellpadding="4" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Ville</th>
            <th>Quantité</th>
            <th>Prix unitaire</th>
            <th>Montant total</th>
            <th>Statut</th>
        </tr>
        <?php foreach ($recent_besoins as $b) { ?>
            <tr>
                <td><?= htmlspecialchars($b['id'] ?? '') ?></td>
                <td><?= htmlspecialchars($b['ville_nom'] ?? '') ?></td>
                <td><?= htmlspecialchars($b['quantite'] ?? '') ?></td>
                <td><?= htmlspecialchars($b['prix_unitaire'] ?? '') ?></td>
                <td><?= htmlspecialchars($b['montant_total'] ?? '') ?></td>
                <td><?= htmlspecialchars($b['status_nom'] ?? '') ?></td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>
=======
>>>>>>> Stashed changes
    <!-- Features Section -->
    <section class="features-section">
        <h2 class="section-title">Nos Services</h2>
        <p class="section-subtitle">Découvrez comment nous gérons efficacement les dons et l'aide humanitaire</p>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="bi bi-gift"></i>
                </div>
                <h3 class="feature-title">Gestion des Dons</h3>
                <p class="feature-description">Enregistrement et suivi complet de tous les dons reçus avec traçabilité totale de leur distribution.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <h3 class="feature-title">Distribution Optimisée</h3>
                <p class="feature-description">Simulation et dispatch intelligent des dons vers les zones les plus touchées selon les besoins identifiés.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <h3 class="feature-title">Couverture Nationale</h3>
                <p class="feature-description">Présence dans toutes les régions de Madagascar pour une réponse rapide aux catastrophes.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="bi bi-bar-chart"></i>
                </div>
                <h3 class="feature-title">Rapports Détaillés</h3>
                <p class="feature-description">Tableaux de bord et statistiques en temps réel pour un suivi transparent des opérations.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-section">
        <div class="footer-links">
<<<<<<< Updated upstream
            <a href="<?= Flight::get('flight.base_url') ?>">Accueil</a>
            <a href="<?= Flight::get('flight.base_url') ?>/dashboard">Tableau de bord</a>
            <a href="<?= Flight::get('flight.base_url') ?>/don/ajouter">Faire un don</a>
=======
            <a href="<?= Flight::get('flight.base_path') ?>">Accueil</a>
            <a href="<?= Flight::get('flight.base_path') ?>/dashboard">Tableau de bord</a>
            <a href="<?= Flight::get('flight.base_path') ?>/don/ajouter">Faire un don</a>
>>>>>>> Stashed changes
        </div>
        <p>&copy; <?= date('Y') ?> Bureau National de Gestion des Risques et Catastrophes (BNGRC). Tous droits réservés.</p>
    </footer>

    <script src="<?php echo Flight::get('flight.base_url'); ?>/js/app.js"></script>
</body>

</html>
<<<<<<< Updated upstream
=======
>>>>>>> Stashed changes
>>>>>>> Stashed changes
