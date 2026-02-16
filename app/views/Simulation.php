<!DOCTYPE html>
<html class="light" lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Simulation de Dispatch - Don</title>
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet" />
    <style>
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
        }
        .simulation-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
        }
        .need-item {
            background: white;
            border: 1px solid #dee2e6;
            padding: 1rem;
            margin-bottom: 0.75rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        .need-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .badge-dispatch {
            background: #28a745;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            font-weight: 600;
        }
        .badge-remaining {
            background: #ffc107;
            color: #333;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            font-weight: 600;
        }
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .stat-box {
            background: white;
            padding: 1rem;
            border-radius: 0.5rem;
            border: 1px solid #dee2e6;
            text-align: center;
        }
        .stat-label {
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }
        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #667eea;
        }
        .button-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        .btn-lg-custom {
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 0.5rem;
            font-size: 1rem;
        }
        .loading {
            display: none;
        }
        .loading.active {
            display: inline-block;
        }
        .alert-simulation {
            margin-top: 1rem;
            display: none;
        }
        .alert-simulation.active {
            display: block;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .success-animation {
            animation: bounce 0.6s ease;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>

<body>
    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="fw-bold">
                <i class="bi bi-shuffle me-2"></i>
                Simulation de Dispatch
            </h2>
            <a href="<?= Flight::get('flight.base_url') ?>" class="btn btn-link">← Retour</a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-gift me-2"></i>
                    Don de <?= htmlspecialchars($don['donateur_nom'] ?? 'Anonyme') ?>
                </h5>
            </div>

            <div class="card-body">
                <!-- Donation Details -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="simulation-box">
                            <h6 class="fw-bold mb-3">📋 Détails du Don</h6>
                            <p class="mb-2">
                                <strong>Catégorie:</strong> 
                                <span class="badge bg-info"><?= htmlspecialchars($don['categorie_nom'] ?? '-') ?></span>
                            </p>
                            <p class="mb-2">
                                <strong>Ville:</strong> 
                                <span><?= htmlspecialchars($don['ville_nom'] ?? '-') ?></span>
                            </p>
                            <p class="mb-2">
                                <strong>Région:</strong> 
                                <span><?= htmlspecialchars($don['region_nom'] ?? '-') ?></span>
                            </p>
                            <p class="mb-2">
                                <strong>Quantité:</strong> 
                                <span class="badge bg-primary"><?= (int)$don['quantite'] ?> unités</span>
                            </p>
                            <p class="mb-0">
                                <strong>Date du don:</strong> 
                                <span><?= date('d/m/Y H:i', strtotime($don['date_don'] ?? 'now')) ?></span>
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="simulation-box">
                            <h6 class="fw-bold mb-3">👤 Informations Donateur</h6>
                            <p class="mb-2"><strong>Nom:</strong> <?= htmlspecialchars($don['donateur_nom'] ?? '-') ?></p>
                            <p class="mb-2"><strong>Email:</strong> <?= htmlspecialchars($don['donateur_email'] ?? '-') ?></p>
                            <p class="mb-0"><strong>Téléphone:</strong> <?= htmlspecialchars($don['donateur_telephone'] ?? '-') ?></p>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="stats-row">
                    <div class="stat-box">
                        <div class="stat-label">Total à dispatcher</div>
                        <div class="stat-value" id="stat-total"><?= (int)$don['quantite'] ?></div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-label">Besoins trouvés</div>
                        <div class="stat-value" id="stat-needs"><?= count($simulation['dispatch_results'] ?? []) ?></div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-label">Besoins satisfaits</div>
                        <div class="stat-value" id="stat-satisfied">0</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-label">Quantité restante</div>
                        <div class="stat-value" id="stat-remaining"><?= (int)$simulation['remaining_quantity'] ?></div>
                    </div>
                </div>

                <!-- Simulation Results -->
                <div id="simulation-container">
                    <?php if (!empty($simulation['dispatch_results'])): ?>
                        <div class="alert alert-simulation active alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Résultats de la simulation:</strong> 
                            La donation sera dispatcher sur <?= count($simulation['dispatch_results']) ?> besoin(s).
                        </div>

                        <h6 class="fw-bold mb-3 mt-4">
                            <i class="bi bi-arrow-left-right me-2"></i>
                            Dispatch vers les besoins
                        </h6>

                        <?php foreach ($simulation['dispatch_results'] as $index => $dispatch): ?>
                            <div class="need-item">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <p class="mb-2">
                                            <strong>Besoin #<?= htmlspecialchars($dispatch['besoin_id']) ?></strong>
                                            <span class="text-muted float-end">Ville: <?= htmlspecialchars($dispatch['besoin_ville_nom'] ?? '-') ?></span>
                                        </p>
                                        <p class="text-muted small mb-0">
                                            <?= htmlspecialchars($dispatch['besoin_description'] ?? 'Sans description') ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-end gap-2 align-items-center flex-wrap">
                                            <div>
                                                <div class="small text-muted">Besoin initial</div>
                                                <div class="badge bg-light text-dark"><?= (int)$dispatch['besoin_quantite_needed'] ?> unités</div>
                                            </div>
                                            <div class="text-center">
                                                <i class="bi bi-arrow-right text-success" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted">Dispatché</div>
                                                <div class="badge-dispatch"><?= (int)$dispatch['dispatched_quantity'] ?> unités</div>
                                            </div>
                                            <div>
                                                <div class="small text-muted">Après dispatch</div>
                                                <div class="badge bg-light text-dark"><?= (int)$dispatch['new_need_quantity'] ?> unités</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <?php if ($simulation['remaining_quantity'] > 0): ?>
                            <div class="alert alert-warning mt-3">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Quantité non utilisée:</strong> 
                                <?= (int)$simulation['remaining_quantity'] ?> unité(s) restante(s) 
                                après satisfaction de tous les besoins.
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            <strong>Aucun besoin trouvé</strong> 
                            pour cette catégorie et cette ville.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Action Buttons -->
                <div class="button-group">
                    <button class="btn btn-secondary btn-lg-custom" id="btn-simulate" onclick="handleSimulate()">
                        <i class="bi bi-play-circle me-2"></i>
                        Rafraîchir la Simulation
                    </button>
                    <button class="btn btn-success btn-lg-custom" id="btn-validate" onclick="handleValidate()" 
                            <?= empty($simulation['dispatch_results']) ? 'disabled' : '' ?>>
                        <i class="bi bi-check-circle me-2"></i>
                        Valider le Dispatch
                        <span class="loading" id="loading-validate">
                            <span class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>
                        </span>
                    </button>
                </div>

                <div id="alert-container" class="mt-3"></div>
            </div>
        </div>
    </div>

    <script src="<?php echo Flight::get('flight.base_url'); ?>/js/bootstrap.bundle.min.js"></script>
    <script>
        const BASE_URL = '<?= Flight::get("flight.base_url") ?>';
        const DON_ID = <?= (int)$don_id ?>;

        async function handleSimulate() {
            const btn = document.getElementById('btn-simulate');
            const loader = btn.querySelector('.loading');
            
            btn.disabled = true;
            loader.classList.add('active');

            try {
                const response = await fetch(`${BASE_URL}/api/simulation/simulate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ don_id: DON_ID })
                });

                const data = await response.json();

                if (data.success) {
                    showAlert('success', 'Simulation rafraîchie avec succès');
                    updateSimulationDisplay(data);
                    document.getElementById('btn-validate').disabled = 
                        !data.dispatch_results || data.dispatch_results.length === 0;
                } else {
                    showAlert('danger', 'Erreur: ' + (data.error || 'Simulation échouée'));
                }
            } catch (error) {
                showAlert('danger', 'Erreur réseau: ' + error.message);
            } finally {
                btn.disabled = false;
                loader.classList.remove('active');
            }
        }

        async function handleValidate() {
            if (!confirm('Êtes-vous sûr de vouloir valider et dispatcher le don ? Cette action ne peut pas être annulée.')) {
                return;
            }

            const btn = document.getElementById('btn-validate');
            const loader = btn.querySelector('.loading');
            
            btn.disabled = true;
            loader.classList.add('active');

            try {
                const response = await fetch(`${BASE_URL}/api/simulation/validate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ don_id: DON_ID })
                });

                const data = await response.json();

                if (data.success) {
                    showAlert('success', 
                        `<i class="bi bi-check-circle me-2"></i>
                        Dispatch validé! ${data.total_dispatched} unité(s) dispatché(es) vers ${data.dispatch_results.length} besoin(s).`
                    );
                    
                    // Disable buttons and mark as completed
                    document.getElementById('btn-simulate').disabled = true;
                    btn.disabled = true;
                    
                    // Show completion message
                    setTimeout(() => {
                        const confirmMsg = confirm('Dispatch termin avec succès! Retourner à la page d\'accueil ?');
                        if (confirmMsg) {
                            window.location.href = BASE_URL + '/';
                        }
                    }, 2000);
                } else {
                    showAlert('danger', 'Erreur de validation: ' + (data.error || 'Validation échouée'));
                }
            } catch (error) {
                showAlert('danger', 'Erreur réseau: ' + error.message);
            } finally {
                btn.disabled = false;
                loader.classList.remove('active');
            }
        }

        function updateSimulationDisplay(data) {
            if (data.dispatch_results && data.dispatch_results.length > 0) {
                // Update statistics
                document.getElementById('stat-satisfied').textContent = data.dispatch_results.length;
                document.getElementById('stat-remaining').textContent = data.remaining_quantity;

                // Build dispatch results HTML
                let html = `
                    <div class="alert alert-simulation active alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Résultats de la simulation:</strong> 
                        La donation sera dispatcher sur ${data.dispatch_results.length} besoin(s).
                    </div>
                    <h6 class="fw-bold mb-3 mt-4">
                        <i class="bi bi-arrow-left-right me-2"></i>
                        Dispatch vers les besoins
                    </h6>`;

                data.dispatch_results.forEach(dispatch => {
                    html += `
                        <div class="need-item">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>Besoin #${dispatch.besoin_id}</strong>
                                        <span class="text-muted float-end">Ville: ${dispatch.besoin_ville_nom || '-'}</span>
                                    </p>
                                    <p class="text-muted small mb-0">
                                        ${dispatch.besoin_description || 'Sans description'}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-end gap-2 align-items-center flex-wrap">
                                        <div>
                                            <div class="small text-muted">Besoin initial</div>
                                            <div class="badge bg-light text-dark">${dispatch.besoin_quantite_needed} unités</div>
                                        </div>
                                        <div class="text-center">
                                            <i class="bi bi-arrow-right text-success" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="small text-muted">Dispatché</div>
                                            <div class="badge-dispatch">${dispatch.dispatched_quantity} unités</div>
                                        </div>
                                        <div>
                                            <div class="small text-muted">Après dispatch</div>
                                            <div class="badge bg-light text-dark">${dispatch.new_need_quantity} unités</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                });

                if (data.remaining_quantity > 0) {
                    html += `
                        <div class="alert alert-warning mt-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Quantité non utilisée:</strong> 
                            ${data.remaining_quantity} unité(s) restante(s) après satisfaction de tous les besoins.
                        </div>`;
                }

                document.getElementById('simulation-container').innerHTML = html;
            } else {
                document.getElementById('simulation-container').innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <strong>Aucun besoin trouvé</strong> pour cette catégorie et cette ville.
                    </div>`;
            }
        }

        function showAlert(type, message) {
            const container = document.getElementById('alert-container');
            const alertId = 'alert-' + Date.now();
            const alertHtml = `
                <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
            
            container.innerHTML = alertHtml;
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                const alert = document.getElementById(alertId);
                if (alert) {
                    alert.remove();
                }
            }, 5000);
        }
    </script>
</body>

</html>
