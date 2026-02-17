<!DOCTYPE html>
<html class="light" lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Don - BNGRC</title>
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
                    <a href="<?= Flight::get('flight.base_url') ?>/don/ajouter" class="sidebar-menu-link active">
                        <i class="bi bi-gift"></i>
                        <span>Ajouter don</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?= Flight::get('flight.base_url') ?>/achat/non-argent" class="sidebar-menu-link">
                        <i class="bi bi-bag"></i>
                        <span>Achat</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?= Flight::get('flight.base_url') ?>/achat/non-argent" class="sidebar-menu-link">
                        <i class="bi bi-bag"></i>
                        <span>Achat</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?= Flight::get('flight.base_url') ?>/Simulation" class="sidebar-menu-link">
                        <i class="bi bi-diagram-3"></i>
                        <span>Simulation</span>
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
                    <h1 style="margin: 0; font-size: 1.25rem; font-weight: 700;">📊 BNGRC - Gestion des risques</h1>
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
                        <li>Ajouter un don</li>
                    </ol>
                </div>

                <!-- PAGE HEADER -->
                <div class="page-header">
                    <div class="page-title">
                        <h1><i class="bi bi-gift-fill"></i> Ajouter un Don</h1>
                        <p>Remplissez les informations du don pour l'enregistrer dans le système</p>
                    </div>
                    <div class="page-actions">
                        <a href="<?= Flight::get('flight.base_url') ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i>
                            Retour
                        </a>
                    </div>
                </div>

                <!-- FORM CARD -->
                <div class="card" style="max-width: 850px; margin: 0 auto;">
                    <form method="post" action="<?= Flight::get('flight.base_url') ?>/don/ajouter" id="donForm">
                        <!-- SECTION 1: INFORMATIONS DU DON -->
                        <div class="card-header bg-primary">
                            <h5 style="margin: 0; color: white; display: flex; align-items: center; gap: var(--spacing-2);">
                                <i class="bi bi-box-seam"></i>
                                Informations du Don
                            </h5>
                        </div>

                        <div class="card-body">
                            <input type="hidden" name="ville_id" value="<?= htmlspecialchars($ville_id ?? '') ?>" />

                            <div class="form-group">
                                <label class="form-label required">Ville</label>
                                <?php if (!empty($ville_id)) { ?>
                                    <input type="text" class="form-control" value="Ville ID: <?= htmlspecialchars($ville_id) ?>" disabled />
                                <?php } else { ?>
                                    <input type="text" name="ville_libre" class="form-control" placeholder="Entrez l'ID ou le nom de la ville" />
                                <?php } ?>
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Catégorie</label>
                                <select name="categorie_id" class="form-select" required>
                                    <option value="">-- Sélectionnez une catégorie --</option>
                                    <?php foreach ($categories as $c) { ?>
                                        <option value="<?= htmlspecialchars($c['id']) ?>"><?= htmlspecialchars($c['libelle']) ?></option>
                                    <?php } ?>
                                </select>
                                <span class="form-helper">Sélectionnez le type de bien donné</span>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label required">Quantité</label>
                                    <input type="number" name="quantite" class="form-control" min="1" placeholder="0" required />
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Date du don</label>
                                    <input type="datetime-local" name="date_don" class="form-control" />
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: INFORMATIONS DU DONATEUR -->
                        <div class="card-header bg-primary" style="margin-top: var(--spacing-6);">
                            <h5 style="margin: 0; color: white; display: flex; align-items: center; gap: var(--spacing-2);">
                                <i class="bi bi-person-check"></i>
                                Informations du Donateur
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">Sélectionner un donateur existant</label>
                                <select name="donateur_id" class="form-select">
                                    <option value="">-- Créer un nouveau donateur --</option>
                                    <?php foreach ($donateurs as $d) { ?>
                                        <option value="<?= htmlspecialchars($d['id']) ?>">
                                            <?= htmlspecialchars($d['nom']) ?>
                                            <?= !empty($d['telephone']) ? '(' . htmlspecialchars($d['telephone']) . ')' : '' ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <span class="form-helper">Ou remplissez les informations ci-dessous pour créer un nouveau donateur</span>
                            </div>

                            <div style="background: linear-gradient(135deg, var(--primary-50) 0%, rgba(89, 104, 255, 0.05) 100%); padding: var(--spacing-4); border-radius: var(--radius-lg); margin: var(--spacing-4) 0; border-left: 4px solid var(--primary);">
                                <p class="text-muted" style="margin: 0; font-size: var(--font-size-sm); color: var(--text-secondary);">
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Note:</strong> Ces informations ne seront utilisées que si vous n'avez pas sélectionné de donateur existant
                                </p>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Nom complet</label>
                                    <input type="text" name="donateur_nom" class="form-control" placeholder="Ex: Jean Dupont" />
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Téléphone</label>
                                    <input type="tel" name="donateur_telephone" class="form-control" placeholder="Ex: +261 34 40 40 40" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" name="donateur_email" class="form-control" placeholder="Ex: email@exemple.com" />
                            </div>
                        </div>

                        <!-- ACTIONS -->
                        <div class="card-footer" style="display: flex; gap: var(--spacing-3); justify-content: flex-end;">
                            <a href="<?= Flight::get('flight.base_url') ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i>
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i>
                                Enregistrer le don
                            </button>
                        </div>
                    </form>
                </div>

            </div>

            <!-- FOOTER -->
            <footer class="layout-footer">
                <p>&copy; <?= date('Y') ?> Bureau National de Gestion des Risques et Catastrophes (BNGRC). Tous droits réservés.</p>
            </footer>
        </div>
    </div>

    <script nonce="<?= Flight::get('csp_nonce') ?>">
        // Responsive sidebar toggle
        document.querySelector('.sidebar-toggle')?.addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('show');
        });

        // Simple form validation
        document.getElementById('donForm')?.addEventListener('submit', function(e) {
            const categorie = document.querySelector('select[name="categorie_id"]').value;
            const quantite = document.querySelector('input[name="quantite"]').value;
            
            if (!categorie) {
                e.preventDefault();
                alert('Veuillez sélectionner une catégorie');
                return false;
            }
            
            if (!quantite || quantite < 1) {
                e.preventDefault();
                alert('Veuillez entrer une quantité valide');
                return false;
            }
        });
    </script>
</body>

</html>
