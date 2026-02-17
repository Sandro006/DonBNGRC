<!DOCTYPE html>
<html class="light" lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Méthodes de Distribution - BNGRC</title>
    <link href="<?= Flight::get('flight.base_url') ?>/css/design-system.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/components.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/layout.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/utilities.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/pages.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/css/custom.css" rel="stylesheet" />
    <link href="<?= Flight::get('flight.base_url') ?>/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet" />
</head>

<body>
    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <!-- Main Layout -->
    <div class="layout">
        <!-- Main Content Area -->
        <div class="layout-main">
            <!-- HEADER -->
            <header class="header">
                <div class="header-container">
                    <button class="sidebar-toggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 style="margin: 0; font-size: 1.25rem; font-weight: 700;">⚙️ BNGRC - Méthodes de Distribution</h1>
                    <div class="header-actions" style="margin-left: auto;">
                        <div class="header-user">
                            <div class="header-user-avatar">AD</div>
                            <span style="font-size: 0.875rem;">Admin</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- CONTENT -->
            <main class="layout-content">
                <!-- BREADCRUMB -->
                <div class="breadcrumb-nav">
                    <ol>
                        <li><a href="<?= Flight::get('flight.base_url') ?>">Accueil</a></li>
                        <li><a href="<?= Flight::get('flight.base_url') ?>/don-global">Dons Globaux</a></li>
                        <li>Méthodes de Distribution</li>
                    </ol>
                </div>

                <!-- PAGE HEADER -->
                <div class="page-header">
                    <div class="page-title">
                        <h1><i class="bi bi-gear-fill"></i> Méthodes de Distribution</h1>
                        <p>Choisissez et configurez la méthode de distribution des dons globaux</p>
                    </div>
                </div>

                <!-- FORM DE SELECTION -->
                <form id="distributionForm" method="post" action="<?= Flight::get('flight.base_url') ?>/don-global/simulation">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- METHODES DISPONIBLES -->
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="bi bi-list-ul"></i> Méthodes Disponibles</h5>
                                    <small class="text-muted">Sélectionnez la méthode de distribution qui convient le mieux à votre stratégie</small>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($methodes as $key => $methode): ?>
                                        <div class="method-option mb-3">
                                            <div class="form-check method-radio">
                                                <input class="form-check-input" type="radio" name="methode" 
                                                       id="methode_<?= $key ?>" value="<?= $key ?>" 
                                                       <?= $key === 'date' ? 'checked' : '' ?>>
                                                <label class="form-check-label method-label" for="methode_<?= $key ?>">
                                                    <div class="method-card">
                                                        <div class="method-header">
                                                            <i class="bi bi-<?= $methode['icone'] ?> method-icon"></i>
                                                            <div>
                                                                <h6 class="method-title"><?= htmlspecialchars($methode['nom']) ?></h6>
                                                                <p class="method-description"><?= htmlspecialchars($methode['description']) ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- PARAMETRES DE LA METHODE -->
                            <div class="card" id="parametres-card">
                                <div class="card-header">
                                    <h6><i class="bi bi-sliders"></i> Paramètres</h6>
                                </div>
                                <div class="card-body">
                                    <div id="parametres-content">
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle"></i>
                                            <small>Sélectionnez une méthode pour voir ses paramètres</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ACTIONS -->
                            <div class="card mt-3">
                                <div class="card-body text-center">
                                    <button type="submit" class="btn btn-primary btn-lg mb-2" name="action" value="simuler">
                                        <i class="bi bi-play-circle-fill"></i>
                                        Simuler la Distribution
                                    </button>
                                    <br>
                                    <button type="submit" class="btn btn-success" name="action" value="executer">
                                        <i class="bi bi-lightning-fill"></i>
                                        Exécuter Directement
                                    </button>
                                    <small class="form-text text-muted d-block mt-2">
                                        <strong>Simuler:</strong> Voir les résultats sans modifier<br>
                                        <strong>Exécuter:</strong> Appliquer la distribution immédiatement
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- INFORMATION SUR LES METHODES -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6><i class="bi bi-question-circle"></i> À propos des Méthodes</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>🕒 Distribution par Date</h6>
                                <p><small>Privilégie les besoins avec les plus grosses quantités demandées. Les besoins les plus importants sont traités en premier, triés par date.</small></p>
                            </div>
                            <div class="col-md-6">
                                <h6>📊 Distribution par Plus Petit Nombre</h6>
                                <p><small>Distribue en minimisant le nombre de besoins restants. Priorité aux petites quantités pour satisfaire le maximum de besoins.</small></p>
                            </div>
                        </div>
                    </div>
                </div>

            </main>

            <!-- FOOTER -->
            <?php include __DIR__ . '/partials/footer.php'; ?>
        </div>
    </div>

    <script>
        // Responsive sidebar toggle
        document.querySelector('.sidebar-toggle')?.addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('show');
        });

        // Paramètres des méthodes
        const methodesParametres = <?= json_encode($methodes) ?>;

        // Gestionnaire de changement de méthode
        document.querySelectorAll('input[name="methode"]').forEach(radio => {
            radio.addEventListener('change', function() {
                updateParametres(this.value);
            });
        });

        // Initialiser avec la méthode par défaut
        updateParametres('date');

        function updateParametres(methode) {
            const parametresContent = document.getElementById('parametres-content');
            const methodeData = methodesParametres[methode];
            
            if (!methodeData || !methodeData.parametres || Object.keys(methodeData.parametres).length === 0) {
                parametresContent.innerHTML = `
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i>
                        <small>Aucun paramètre requis<br>Cette méthode est prête à utiliser</small>
                    </div>
                `;
                return;
            }

            let html = '<div class="parametres-form">';
            
            Object.entries(methodeData.parametres).forEach(([param, config]) => {
                html += `<div class="form-group mb-3">`;
                
                if (typeof config === 'object' && !Array.isArray(config)) {
                    // Select dropdown
                    html += `<label class="form-label">${param.replace('_', ' ')}</label>`;
                    html += `<select name="parametres[${param}]" class="form-select form-select-sm">`;
                    Object.entries(config).forEach(([value, label]) => {
                        html += `<option value="${value}">${label}</option>`;
                    });
                    html += `</select>`;
                } else {
                    // Input text
                    html += `<label class="form-label">${param.replace('_', ' ')}</label>`;
                    html += `<input type="text" name="parametres[${param}]" class="form-control form-control-sm" placeholder="${config}">`;
                }
                
                html += `</div>`;
            });
            
            html += '</div>';
            parametresContent.innerHTML = html;
        }

        // Validation du formulaire
        document.getElementById('distributionForm')?.addEventListener('submit', function(e) {
            const action = document.activeElement.value;
            
            if (action === 'executer') {
                if (!confirm('Êtes-vous sûr de vouloir exécuter la distribution immédiatement ?\n\nCette action modifiera réellement les données et ne peut pas être annulée.')) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    </script>

    <style>
        .method-option {
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            transition: all 0.2s ease;
        }
        
        .method-option:hover {
            border-color: var(--primary);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .method-radio input:checked + .method-label .method-card {
            border-color: var(--primary);
            background-color: var(--primary-50);
        }
        
        .method-card {
            padding: 1rem;
            border: 2px solid transparent;
            border-radius: var(--radius-md);
            transition: all 0.2s ease;
        }
        
        .method-header {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }
        
        .method-icon {
            font-size: 1.5rem;
            color: var(--primary);
            margin-top: 0.25rem;
        }
        
        .method-title {
            margin: 0 0 0.5rem 0;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .method-description {
            margin: 0;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }
        
        .form-check-input {
            position: absolute;
            opacity: 0;
        }
        
        .parametres-form .form-group {
            margin-bottom: 1rem;
        }
        
        .parametres-form .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: capitalize;
        }
    </style>
</body>

</html>