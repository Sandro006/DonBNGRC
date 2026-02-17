<?php
/**
 * Configuration Frais View
 * Manage frais_percent for bngrc_achat table
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration des Frais</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .configuration-container {
            max-width: 600px;
            margin: 40px auto;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid #dee2e6;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .btn-save {
            min-width: 120px;
        }
        .alert-success {
            margin-top: 1rem;
        }
        .frais-info {
            background-color: #f0f7ff;
            border-left: 4px solid #0d6efd;
            padding: 1rem;
            border-radius: 0.25rem;
            margin-bottom: 1.5rem;
        }
        .frais-info p {
            margin: 0.25rem 0;
            font-size: 0.95rem;
        }
        .current-value {
            font-weight: 600;
            color: #0d6efd;
            font-size: 1.2rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="configuration-container">
            <!-- Header -->
            <div class="mb-4">
                <h1 class="h3 mb-2">
                    <i class="bi bi-gear"></i> Configuration des Frais
                </h1>
                <p class="text-muted">Gérez le pourcentage de frais pour les achats</p>
            </div>

            <!-- Current Info Box -->
            <div class="frais-info">
                <p><strong>Pourcentage de frais actuel :</strong></p>
                <p class="current-value" id="currentFraisValue">
                    <?php echo isset($current_frais_percent) ? number_format($current_frais_percent, 2, ',', ' ') . ' %' : 'N/A'; ?>
                </p>
                <p class="text-muted mt-2" style="font-size: 0.9rem;">
                    Ce pourcentage s'applique à tous les nouveaux achats enregistrés dans le système.
                </p>
            </div>

            <!-- Configuration Form -->
            <div class="card">
                <div class="card-body p-4">
                    <form id="configurationForm" method="POST" action="/api/configuration/frais">
                        <!-- Frais Percent Input -->
                        <div class="mb-3">
                            <label for="fraisPourcentage" class="form-label">
                                Pourcentage de Frais (%)
                            </label>
                            <div class="input-group">
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    min="0" 
                                    max="100" 
                                    class="form-control" 
                                    id="fraisPourcentage" 
                                    name="frais_percent"
                                    value="<?php echo isset($current_frais_percent) ? htmlspecialchars($current_frais_percent) : '0'; ?>"
                                    required
                                    placeholder="Ex: 5.50"
                                    title="Veuillez entrer un pourcentage entre 0 et 100"
                                >
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="form-text">
                                Entrez une valeur entre 0 et 100. Exemple: 5.50 pour 5.50%
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label">
                                Description (Optionnel)
                            </label>
                            <textarea 
                                class="form-control" 
                                id="description" 
                                name="description"
                                rows="3"
                                placeholder="Note de modification (raison, date d'application, etc.)"
                            ><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
                            <div class="form-text">
                                Ajoutez une description pour documenter le changement
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                            <a href="/" class="btn btn-secondary" role="button">
                                <i class="bi bi-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary btn-save">
                                <i class="bi bi-check-circle"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Success Message -->
            <?php if (isset($success_message) && $success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i>
                <strong>Succès!</strong> <?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
            <?php endif; ?>

            <!-- Error Message -->
            <?php if (isset($error_message) && $error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i>
                <strong>Erreur!</strong> <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
            <?php endif; ?>

            <!-- History Section (Optional) -->
            <?php if (isset($frais_history) && !empty($frais_history)): ?>
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="bi bi-clock-history"></i> Historique des modifications
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Pourcentage</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($frais_history as $entry): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo number_format($entry['frais_percent'], 2, ',', ' '); ?>%
                                        </span>
                                    </td>
                                    <td class="text-muted">
                                        <?php echo isset($entry['date']) ? date('d/m/Y H:i', strtotime($entry['date'])) : 'N/A'; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($entry['description']) ? htmlspecialchars($entry['description']) : '-'; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation and real-time preview
        document.getElementById('fraisPourcentage').addEventListener('input', function() {
            const value = parseFloat(this.value) || 0;
            if (value < 0 || value > 100) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });

        // Form submission with AJAX
        document.getElementById('configurationForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = this;
            const fraisInput = document.getElementById('fraisPourcentage');
            const value = parseFloat(fraisInput.value);
            
            // Validation
            if (isNaN(value) || value < 0 || value > 100) {
                fraisInput.classList.add('is-invalid');
                alert('Veuillez entrer un pourcentage valide entre 0 et 100');
                return false;
            }

            try {
                // Get form data
                const formData = new FormData(form);
                
                // Send request
                const response = await fetch('/api/configuration/frais', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        frais_percent: formData.get('frais_percent'),
                        description: formData.get('description')
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Update current value display
                    document.getElementById('currentFraisValue').textContent = 
                        parseFloat(data.frais_percent).toFixed(2).replace('.', ',') + ' %';
                    
                    // Show success message and reload page after delay
                    showAlert('Succès! Configuration des frais mise à jour avec succès', 'success');
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert('Erreur: ' + (data.error || 'Une erreur est survenue'), 'danger');
                }
            } catch (error) {
                showAlert('Erreur: ' + error.message, 'danger');
            }
        });

        // Helper function to show alerts
        function showAlert(message, type) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
                    <strong>${type === 'success' ? 'Succès!' : 'Erreur!'}</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            `;
            
            const alertContainer = document.querySelector('.configuration-container');
            const existingAlert = alertContainer.querySelector('.alert');
            
            if (existingAlert) {
                existingAlert.remove();
            }
            
            const alertElement = document.createElement('div');
            alertElement.innerHTML = alertHtml;
            alertContainer.appendChild(alertElement.firstElementChild);
        }
    </script>
</body>
</html>
