/**
 * Simulation page JavaScript
 * Handles dispatch simulation and validation
 */

const BASE_URL = document.currentScript?.dataset.baseUrl || '/Sarobidy/DonBNGRC';
const DON_ID = parseInt(document.currentScript?.dataset.donId || 0);

// Setup event listeners when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const btnSimulate = document.getElementById('btn-simulate');
    const btnValidate = document.getElementById('btn-validate');
    
    if (btnSimulate) {
        btnSimulate.addEventListener('click', handleSimulate);
    }
    
    if (btnValidate) {
        btnValidate.addEventListener('click', handleValidate);
    }
});

/**
 * Simulate dispatch
 */
async function handleSimulate() {
    const btn = document.getElementById('btn-simulate');
    const loader = document.getElementById('loading-simulate');

    btn.disabled = true;
    loader.style.display = 'inline-block';

    try {
        console.log('Submitting simulation request for don_id:', DON_ID);
        const response = await fetch(`${BASE_URL}/api/simulation/simulate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ don_id: DON_ID })
        });

        console.log('Response status:', response.status);

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Response error:', response.status, errorText);
            showNotification('Erreur serveur: ' + response.status + ' - ' + errorText, 'error');
            return;
        }

        const data = await response.json();
        console.log('Response data:', data);

        if (data.success) {
            showNotification('Simulation rafraîchie avec succès', 'success');
            updateSimulationDisplay(data);
            document.getElementById('btn-validate').disabled =
                !data.dispatch_results || data.dispatch_results.length === 0;
        } else {
            showNotification('Erreur: ' + (data.error || 'Simulation échouée'), 'error');
        }
    } catch (error) {
        console.error('Fetch error:', error);
        showNotification('Erreur réseau: ' + error.message, 'error');
    } finally {
        btn.disabled = false;
        loader.style.display = 'none';
    }
}

/**
 * Validate and execute dispatch
 */
async function handleValidate() {
    if (!confirm('Êtes-vous sûr de vouloir valider et dispatcher le don ?\n\nCette action ne peut pas être annulée.')) {
        return;
    }

    const btn = document.getElementById('btn-validate');
    const loader = document.getElementById('loading-validate');

    btn.disabled = true;
    loader.style.display = 'inline-block';

    try {
        console.log('Submitting validation request for don_id:', DON_ID);
        const response = await fetch(`${BASE_URL}/api/simulation/validate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ don_id: DON_ID })
        });

        console.log('Response status:', response.status);

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Response error:', response.status, errorText);
            showNotification('Erreur serveur: ' + response.status + ' - ' + errorText, 'error');
            return;
        }

        const data = await response.json();
        console.log('Response data:', data);

        if (data.success) {
            const message = `Dispatch validé! ${data.total_dispatched} unité(s) dispatchée(s) vers ${data.dispatch_results.length} besoin(s).`;
            showNotification(message, 'success');

            // Disable buttons
            document.getElementById('btn-simulate').disabled = true;
            btn.disabled = true;

            // Redirect after 3 seconds
            setTimeout(() => {
                window.location.href = BASE_URL + '/simulation';
            }, 3000);
        } else {
            showNotification('Erreur de validation: ' + (data.error || 'Validation échouée'), 'error');
        }
    } catch (error) {
        console.error('Fetch error:', error);
        showNotification('Erreur réseau: ' + error.message, 'error');
    } finally {
        btn.disabled = false;
        loader.style.display = 'none';
    }
}

/**
 * Show notification toast
 */
function showNotification(message, type = 'info') {
    document.querySelectorAll('.toast-notification').forEach(el => el.remove());

    const toast = document.createElement('div');
    toast.className = 'toast-notification';

    const bgColors = {
        success: 'var(--success)',
        error: 'var(--danger)',
        info: 'var(--info)'
    };

    const icons = {
        success: 'bi-check-circle-fill',
        error: 'bi-exclamation-circle-fill',
        info: 'bi-info-circle-fill'
    };

    const titles = {
        success: 'Succès',
        error: 'Erreur',
        info: 'Info'
    };

    toast.style.cssText = `
        position: fixed; top: 20px; right: 20px; z-index: 9999;
        min-width: 320px; max-width: 450px;
        padding: var(--spacing-4) var(--spacing-6);
        border-radius: var(--radius-lg);
        background: ${bgColors[type] || bgColors.info};
        color: white;
        box-shadow: var(--shadow-xl);
        animation: slideInRight 0.3s ease;
    `;

    toast.innerHTML = `
        <div style="display: flex; align-items: center; gap: var(--spacing-2); margin-bottom: var(--spacing-2); font-weight: 700;">
            <i class="bi ${icons[type] || icons.info}"></i>
            ${titles[type] || titles.info}
        </div>
        <p style="margin: 0; font-size: var(--font-size-sm); opacity: 0.95;">${message}</p>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideInRight 0.3s ease reverse';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

/**
 * Update simulation display after AJAX refresh
 */
function updateSimulationDisplay(data) {
    // Update stat cards
    const needsCount = data.dispatch_results ? data.dispatch_results.length : 0;
    document.getElementById('stat-needs').textContent = needsCount;
    document.getElementById('stat-remaining').textContent = data.remaining_quantity || 0;

    let satisfiedCount = 0;
    if (data.dispatch_results) {
        data.dispatch_results.forEach(d => {
            if (d.new_need_quantity === 0) satisfiedCount++;
        });
    }
    document.getElementById('stat-satisfied').textContent = satisfiedCount;

    const container = document.getElementById('simulation-container');

    if (data.dispatch_results && data.dispatch_results.length > 0) {
        let html = `
            <div class="alert alert-info" style="margin-bottom: var(--spacing-6);">
                <i class="bi bi-info-circle" style="font-size: var(--font-size-xl); flex-shrink: 0;"></i>
                <div>
                    <strong>Résultats de la simulation</strong>
                    <p style="margin: var(--spacing-1) 0 0 0;">
                        La donation sera dispatchée sur <strong>${data.dispatch_results.length} besoin(s)</strong>.
                    </p>
                </div>
            </div>

            <div class="sim-section-title">
                <i class="bi bi-arrow-left-right"></i>
                <h3>Distribution des besoins</h3>
            </div>

            <div class="dispatch-list">`;

        data.dispatch_results.forEach(dispatch => {
            const restBadge = dispatch.new_need_quantity === 0 ? 'badge-success' : 'badge-danger';
            html += `
                <div class="dispatch-item">
                    <div class="dispatch-item-header">
                        <span class="badge badge-primary">Besoin #${dispatch.besoin_id}</span>
                        <span class="badge badge-gray">
                            <i class="bi bi-geo-alt"></i>&nbsp;${dispatch.besoin_ville_nom || '-'}
                        </span>
                    </div>
                    <p class="dispatch-item-desc">${dispatch.besoin_description || 'Sans description'}</p>
                    <div class="dispatch-flow">
                        <div class="dispatch-flow-step">
                            <span class="sim-info-label">Besoin</span>
                            <span class="badge badge-warning" style="font-size: var(--font-size-sm); padding: var(--spacing-2) var(--spacing-3);">
                                ${dispatch.besoin_quantite_needed}
                            </span>
                        </div>
                        <i class="bi bi-arrow-right dispatch-flow-arrow"></i>
                        <div class="dispatch-flow-step">
                            <span class="sim-info-label">Dispatché</span>
                            <span class="badge badge-success" style="font-size: var(--font-size-sm); padding: var(--spacing-2) var(--spacing-3);">
                                ${dispatch.dispatched_quantity}
                            </span>
                        </div>
                        <i class="bi bi-arrow-right dispatch-flow-arrow"></i>
                        <div class="dispatch-flow-step">
                            <span class="sim-info-label">Reste</span>
                            <span class="badge ${restBadge}" style="font-size: var(--font-size-sm); padding: var(--spacing-2) var(--spacing-3);">
                                ${dispatch.new_need_quantity}
                            </span>
                        </div>
                    </div>
                </div>`;
        });

        html += `</div>`;

        if (data.remaining_quantity > 0) {
            html += `
            <div class="alert alert-warning" style="margin-top: var(--spacing-6);">
                <i class="bi bi-exclamation-triangle" style="font-size: var(--font-size-xl); flex-shrink: 0;"></i>
                <div>
                    <strong>Quantité non utilisée</strong>
                    <p style="margin: var(--spacing-1) 0 0 0;">
                        ${data.remaining_quantity} unité(s) restante(s) après satisfaction de tous les besoins.
                        Cette quantité peut être conservée pour d'autres distributions.
                    </p>
                </div>
            </div>`;
        }

        container.innerHTML = html;
    } else {
        container.innerHTML = `
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-circle" style="font-size: var(--font-size-xl); flex-shrink: 0;"></i>
                <div>
                    <strong>Aucun besoin trouvé</strong>
                    <p style="margin: var(--spacing-1) 0 0 0;">
                        Aucun besoin correspondant à cette catégorie et cette ville n'a été trouvé.
                    </p>
                </div>
            </div>`;
    }
}
