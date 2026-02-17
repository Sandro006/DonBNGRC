<?php
/**
 * Helper pour afficher le bouton de configuration des frais
 * Utilisation: <?php echo renderFraisButton($don); ?>
 */

function isMoneyCategory($categorieName) {
    return strtolower($categorieName) === 'argent';
}

function renderFraisButton($don) {
    // Ne pas afficher le bouton si la catégorie est "Argent"
    if (isMoneyCategory($don['categorie_nom'] ?? '')) {
        return '';
    }

    $donId = $don['id'] ?? '';
    $fraisPercent = $don['frais_percent'] ?? 0;
    $fraisDisplay = $fraisPercent > 0 ? number_format($fraisPercent, 2, ',', ' ') : '0';

    return '
        <button 
            type="button" 
            class="btn btn-sm btn-outline-warning" 
            onclick="openEditFraisModal(' . htmlspecialchars($donId) . ', ' . htmlspecialchars($fraisPercent) . ')"
            data-don-id="' . htmlspecialchars($donId) . '"
            title="Configurer les frais de ce don"
        >
            <i class="bi bi-percent"></i> ' . htmlspecialchars($fraisDisplay) . '%
        </button>
    ';
}

function renderFraisButtonSmall($don) {
    // Ne pas afficher le bouton si la catégorie est "Argent"
    if (isMoneyCategory($don['categorie_nom'] ?? '')) {
        return '';
    }

    $donId = $don['id'] ?? '';
    $fraisPercent = $don['frais_percent'] ?? 0;

    return '
        <button 
            type="button" 
            class="btn btn-xs btn-outline-warning" 
            onclick="openEditFraisModal(' . htmlspecialchars($donId) . ', ' . htmlspecialchars($fraisPercent) . ')"
            data-don-id="' . htmlspecialchars($donId) . '"
            title="Frais: ' . htmlspecialchars($fraisPercent) . '%"
        >
            <i class="bi bi-percent"></i>
        </button>
    ';
}
