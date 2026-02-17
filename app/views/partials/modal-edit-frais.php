<?php
/**
 * Modal pour éditer les frais d'un don
 * À inclure dans les vues avec: <?php include_once 'app/views/partials/modal-edit-frais.php'; ?>
 */
?>

<!-- Modal Édition Frais -->
<div class="modal fade" id="modalEditFrais" tabindex="-1" aria-labelledby="modalEditFraisLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditFraisLabel">
                    <i class="bi bi-percent"></i> Configuration des Frais
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle"></i>
                    <small>Configurez le pourcentage de frais pour ce don (Nature ou Matériaux seulement)</small>
                </div>

                <div class="mb-3">
                    <label for="fraisPercentageInput" class="form-label">Pourcentage de Frais</label>
                    <div class="input-group">
                        <input 
                            type="number" 
                            step="0.01" 
                            min="0" 
                            max="100" 
                            class="form-control form-control-lg" 
                            id="fraisPercentageInput" 
                            placeholder="Ex: 5.50"
                            required
                        />
                        <span class="input-group-text">%</span>
                    </div>
                    <div class="form-text">Entrez une valeur entre 0 et 100</div>
                </div>

                <div id="fraisErrorAlert" class="alert alert-danger d-none" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <span id="fraisErrorMessage"></span>
                </div>

                <div id="fraisSuccessAlert" class="alert alert-success d-none" role="alert">
                    <i class="bi bi-check-circle"></i>
                    Frais mis à jour avec succès!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Annuler
                </button>
                <button type="button" class="btn btn-primary" id="saveFraisBtn">
                    <i class="bi bi-check-circle"></i> Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentDonId = null;
    const modal = document.getElementById('modalEditFrais');

    /**
     * Ouvrir la modale et charger les données
     * @param {number} donId - ID du don
     * @param {number} currentFrais - Frais actuels (optionnel)
     */
    function openEditFraisModal(donId, currentFrais = 0) {
        currentDonId = donId;
        document.getElementById('fraisPercentageInput').value = currentFrais || '';
        document.getElementById('fraisErrorAlert').classList.add('d-none');
        document.getElementById('fraisSuccessAlert').classList.add('d-none');
        
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }

    // Enregistrer les frais
    document.getElementById('saveFraisBtn').addEventListener('click', async function() {
        const fraisValue = parseFloat(document.getElementById('fraisPercentageInput').value);
        const errorAlert = document.getElementById('fraisErrorAlert');
        const successAlert = document.getElementById('fraisSuccessAlert');

        // Validation
        if (isNaN(fraisValue) || fraisValue < 0 || fraisValue > 100) {
            document.getElementById('fraisErrorMessage').textContent = 'Veuillez entrer un pourcentage valide entre 0 et 100';
            errorAlert.classList.remove('d-none');
            return;
        }

        if (!currentDonId) {
            document.getElementById('fraisErrorMessage').textContent = 'Erreur: Don non identifié';
            errorAlert.classList.remove('d-none');
            return;
        }

        try {
            const response = await fetch(`/api/don/${currentDonId}/frais`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    frais_percent: fraisValue
                })
            });

            const data = await response.json();

            if (data.success) {
                errorAlert.classList.add('d-none');
                successAlert.classList.remove('d-none');

                // Mettre à jour le bouton/affichage du don
                const fraisBtn = document.querySelector(`button[data-don-id="${currentDonId}"]`);
                if (fraisBtn) {
                    fraisBtn.innerHTML = `<i class="bi bi-percent"></i> ${fraisValue.toFixed(2)}%`;
                }

                // Fermer après un délai
                setTimeout(() => {
                    bootstrap.Modal.getInstance(modal).hide();
                }, 1500);
            } else {
                document.getElementById('fraisErrorMessage').textContent = data.error || 'Erreur lors de la mise à jour';
                errorAlert.classList.remove('d-none');
            }
        } catch (error) {
            document.getElementById('fraisErrorMessage').textContent = 'Erreur réseau: ' + error.message;
            errorAlert.classList.remove('d-none');
        }
    });

    // Fermer les alerts quand on tape dans l'input
    document.getElementById('fraisPercentageInput').addEventListener('input', function() {
        document.getElementById('fraisErrorAlert').classList.add('d-none');
        document.getElementById('fraisSuccessAlert').classList.add('d-none');
    });
</script>
