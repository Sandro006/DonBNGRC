<!DOCTYPE html>
<html class="light" lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration Frais % - BNGRC</title>
    
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
                    <a href="<?= Flight::get('flight.base_url') ?>/achat" class="sidebar-menu-link active">
                        <i class="bi bi-bag"></i>
                        <span>Achat</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?= Flight::get('flight.base_url') ?>/simulation" class="sidebar-menu-link">
                        <i class="bi bi-diagram-3"></i>
                        <span>Simulation</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Layout -->
        <div class="layout-main">
            <!-- Header -->
            <header class="header">
                <div class="header-container">
                    <button class="sidebar-toggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 style="margin: 0; font-size: 1.25rem; font-weight: 700;">
                        <i class="bi bi-percent"></i> Configuration Frais
                    </h1>
                    <div class="header-actions" style="margin-left: auto;">
                        <a href="<?= Flight::get('flight.base_url') ?>/achat" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="layout-content">
                <!-- Breadcrumb -->
                <div class="breadcrumb-nav">
                    <ol>
                        <li><a href="<?= Flight::get('flight.base_url') ?>">Accueil</a></li>
                        <li><a href="<?= Flight::get('flight.base_url') ?>/dashboard">Tableau de bord</a></li>
                        <li><a href="<?= Flight::get('flight.base_url') ?>/achat">Achat</a></li>
                        <li>Configuration Frais</li>
                    </ol>
                </div>

                <!-- Page Header -->
                <div class="page-header" style="margin-bottom: var(--spacing-8);">
                    <div class="page-title">
                        <h2 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-2);">
                            <i class="bi bi-percent"></i>
                            Configuration du Pourcentage de Frais
                        </h2>
                        <p style="margin: var(--spacing-2) 0 0 0; color: var(--text-secondary);">
                            Modifiez le pourcentage de frais pour cet achat
                        </p>
                    </div>
                </div>

                <!-- Main Card -->
                <div class="card">
                    <div class="card-body">
                        <?php if (!empty($achat)): ?>
                            <!-- Purchase Details -->
                            <div style="background-color: var(--surface-200); padding: var(--spacing-4); border-radius: var(--radius); margin-bottom: var(--spacing-6);">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-4);">
                                    <div>
                                        <label style="display: block; color: var(--text-secondary); font-size: var(--font-size-sm); margin-bottom: var(--spacing-1);">Ville</label>
                                        <p style="margin: 0; font-weight: 600;"><?= htmlspecialchars($achat['ville_nom'] ?? 'N/A') ?></p>
                                    </div>
                                    <div>
                                        <label style="display: block; color: var(--text-secondary); font-size: var(--font-size-sm); margin-bottom: var(--spacing-1);">Catégorie</label>
                                        <p style="margin: 0; font-weight: 600;"><?= htmlspecialchars($achat['categorie_nom'] ?? 'N/A') ?></p>
                                    </div>
                                    <div>
                                        <label style="display: block; color: var(--text-secondary); font-size: var(--font-size-sm); margin-bottom: var(--spacing-1);">Montant de Base</label>
                                        <p style="margin: 0; font-weight: 600;"><?= number_format($achat['montant'] ?? 0, 2, ',', '.') ?> Ar</p>
                                    </div>
                                    <div>
                                        <label style="display: block; color: var(--text-secondary); font-size: var(--font-size-sm); margin-bottom: var(--spacing-1);">Date d'Achat</label>
                                        <p style="margin: 0; font-weight: 600;"><?= date('d/m/Y', strtotime($achat['date_achat'] ?? 'now')) ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Configuration Form -->
                            <form id="feeForm" method="POST" action="<?= Flight::get('flight.base_url') ?>/achat/<?= htmlspecialchars($achat['id']) ?>/frais-update">
                                <div style="margin-bottom: var(--spacing-6);">
                                    <label style="display: block; margin-bottom: var(--spacing-2);">
                                        <strong>Pourcentage de Frais (%)</strong>
                                    </label>
                                    <div style="display: flex; gap: var(--spacing-3); align-items: flex-end;">
                                        <div style="flex: 1;">
                                            <input 
                                                type="number" 
                                                name="frais_percent" 
                                                id="fraisPercent"
                                                value="<?= htmlspecialchars($achat['frais_percent'] ?? 0) ?>"
                                                min="0" 
                                                max="100" 
                                                step="0.01"
                                                class="form-control"
                                                placeholder="Entrez le pourcentage (ex: 5.50)"
                                                required
                                            />
                                            <small style="display: block; margin-top: var(--spacing-1); color: var(--text-secondary);">
                                                Entre 0 et 100
                                            </small>
                                        </div>
                                        <span style="font-weight: 600; font-size: 1.2rem;">%</span>
                                    </div>
                                </div>

                                <!-- Calculation Preview -->
                                <div style="background-color: var(--surface-200); padding: var(--spacing-4); border-radius: var(--radius); margin-bottom: var(--spacing-6);">
                                    <h3 style="margin: 0 0 var(--spacing-3) 0; font-size: var(--font-size-lg);">Aperçu du Calcul</h3>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-4);">
                                        <div>
                                            <p style="margin: 0; color: var(--text-secondary); font-size: var(--font-size-sm);">Montant de base</p>
                                            <p style="margin: var(--spacing-1) 0 0 0; font-weight: 600; font-size: var(--font-size-lg);">
                                                <span id="baseAmount"><?= number_format($achat['montant'] ?? 0, 2, ',', '.') ?></span> Ar
                                            </p>
                                        </div>
                                        <div>
                                            <p style="margin: 0; color: var(--text-secondary); font-size: var(--font-size-sm);">Frais calculés</p>
                                            <p style="margin: var(--spacing-1) 0 0 0; font-weight: 600; font-size: var(--font-size-lg); color: var(--warning);">
                                                <span id="feeAmount">0</span> Ar
                                            </p>
                                        </div>
                                        <div>
                                            <p style="margin: 0; color: var(--text-secondary); font-size: var(--font-size-sm);">Montant Total</p>
                                            <p style="margin: var(--spacing-1) 0 0 0; font-weight: 600; font-size: var(--font-size-lg); color: var(--primary);">
                                                <span id="totalAmount"><?= number_format($achat['montant_total'] ?? 0, 2, ',', '.') ?></span> Ar
                                            </p>
                                        </div>
                                        <div>
                                            <p style="margin: 0; color: var(--text-secondary); font-size: var(--font-size-sm);">Ancien Total</p>
                                            <p style="margin: var(--spacing-1) 0 0 0; font-weight: 600; font-size: var(--font-size-lg); color: var(--text-secondary);">
                                                <span id="oldTotal"><?= number_format($achat['montant_total'] ?? 0, 2, ',', '.') ?></span> Ar
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div style="display: flex; gap: var(--spacing-3); justify-content: flex-end;">
                                    <a href="<?= Flight::get('flight.base_url') ?>/achat" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Enregistrer
                                    </button>
                                </div>
                            </form>

                            <!-- Success/Error Messages -->
                            <?php if (!empty($success_message)): ?>
                                <div style="margin-top: var(--spacing-4); padding: var(--spacing-3); background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: var(--radius); color: #155724;">
                                    <i class="bi bi-check-circle"></i>
                                    <?= htmlspecialchars($success_message) ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($error_message)): ?>
                                <div style="margin-top: var(--spacing-4); padding: var(--spacing-3); background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: var(--radius); color: #721c24;">
                                    <i class="bi bi-exclamation-circle"></i>
                                    <?= htmlspecialchars($error_message) ?>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <div style="padding: var(--spacing-6); text-align: center; color: var(--text-secondary);">
                                <i class="bi bi-exclamation-triangle" style="font-size: 3rem; display: block; margin-bottom: var(--spacing-2);"></i>
                                <p>Achat introuvable</p>
                                <a href="<?= Flight::get('flight.base_url') ?>/achat" class="btn btn-sm btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Retour
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script nonce="<?= Flight::get('csp_nonce') ?>">
        // Toggle sidebar on mobile
        document.querySelector('.sidebar-toggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        // Real-time calculation
        const fraisInput = document.getElementById('fraisPercent');
        const baseAmount = parseFloat('<?= $achat['montant'] ?? 0 ?>');
        
        function updateCalculation() {
            const fraisPercent = parseFloat(fraisInput.value) || 0;
            const frais = (baseAmount * fraisPercent) / 100;
            const total = baseAmount + frais;
            
            document.getElementById('feeAmount').textContent = frais.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('totalAmount').textContent = total.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
        
        fraisInput?.addEventListener('input', updateCalculation);
        updateCalculation();

        // Form submission with validation
        document.getElementById('feeForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const frais = parseFloat(fraisInput.value);
            
            if (isNaN(frais) || frais < 0 || frais > 100) {
                alert('Le pourcentage de frais doit être entre 0 et 100');
                return;
            }
            
            this.submit();
        });
    </script>
</body>

</html>
