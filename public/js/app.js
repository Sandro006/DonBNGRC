/**
 * BNGRC App - Common JavaScript Utilities
 * Gestion des interactions UI/UX communes
 */

// ========================================
// SIDEBAR MANAGEMENT
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const layoutContent = document.querySelector('.layout-content');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('show');
        });
    }

    // Close sidebar on mobile when clicking content
    if (layoutContent && sidebar) {
        layoutContent.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('show');
            }
        });
    }

    // ========================================
    // COLLAPSIBLE SECTIONS
    // ========================================
    const collapsibleHeaders = document.querySelectorAll('.collapsible-header');
    collapsibleHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const content = this.nextElementSibling;
            if (content && content.classList.contains('collapsible-content')) {
                const isActive = this.classList.contains('active');
                
                // Close all other collapsibles
                document.querySelectorAll('.collapsible-header.active').forEach(h => {
                    if (h !== this) {
                        h.classList.remove('active');
                        const c = h.nextElementSibling;
                        if (c) c.classList.remove('show');
                    }
                });

                // Toggle current
                this.classList.toggle('active');
                content.classList.toggle('show');
            }
        });
    });

    // ========================================
    // ALERT DISMISSAL
    // ========================================
    const alertCloseButtons = document.querySelectorAll('.alert-close');
    alertCloseButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const alert = this.closest('.alert');
            if (alert) {
                alert.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }
        });
    });

    // ========================================
    // FORM VALIDATION
    // ========================================
    const forms = document.querySelectorAll('form[data-validate="true"]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });

    // ========================================
    // TOOLTIPS
    // ========================================
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            showTooltip(this);
        });
        element.addEventListener('mouseleave', function() {
            hideTooltip(this);
        });
    });

    // ========================================
    // NEED ITEMS SELECTION (For Simulation)
    // ========================================
    const needItems = document.querySelectorAll('.need-item[data-selectable="true"]');
    needItems.forEach(item => {
        item.addEventListener('click', function() {
            this.classList.toggle('selected');
            updateSimulationTotal();
        });
    });

    // ========================================
    // MODAL MANAGEMENT
    // ========================================
    const modalBackdrops = document.querySelectorAll('.modal-backdrop');
    modalBackdrops.forEach(backdrop => {
        backdrop.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this);
            }
        });
    });

    // ========================================
    // TABLE SORTING
    // ========================================
    const sortableHeaders = document.querySelectorAll('[data-sortable="true"]');
    sortableHeaders.forEach(header => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            sortTable(this);
        });
    });

    // ========================================
    // FILTER FUNCTIONALITY
    // ========================================
    const filterInputs = document.querySelectorAll('[data-filter-target]');
    filterInputs.forEach(input => {
        input.addEventListener('input', function() {
            filterTable(this);
        });
    });
});

// ========================================
// UTILITY FUNCTIONS
// ========================================

/**
 * Form validation
 */
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('[required]');
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            showFormError(input, 'Ce champ est requis');
            isValid = false;
        } else {
            clearFormError(input);
        }
    });

    return isValid;
}

function showFormError(input, message) {
    input.classList.add('is-invalid');
    let errorElement = input.nextElementSibling;
    
    if (!errorElement || !errorElement.classList.contains('form-error')) {
        errorElement = document.createElement('span');
        errorElement.classList.add('form-error');
        input.parentNode.insertBefore(errorElement, input.nextSibling);
    }
    
    errorElement.textContent = message;
}

function clearFormError(input) {
    input.classList.remove('is-invalid');
    const errorElement = input.nextElementSibling;
    if (errorElement && errorElement.classList.contains('form-error')) {
        errorElement.remove();
    }
}

/**
 * Tooltip management
 */
function showTooltip(element) {
    const tooltipText = element.getAttribute('data-tooltip');
    let tooltip = element.querySelector('.tooltip-text');
    
    if (!tooltip) {
        tooltip = document.createElement('div');
        tooltip.classList.add('tooltip-text');
        tooltip.textContent = tooltipText;
        element.appendChild(tooltip);
    }
    
    tooltip.style.visibility = 'visible';
    tooltip.style.opacity = '1';
}

function hideTooltip(element) {
    const tooltip = element.querySelector('.tooltip-text');
    if (tooltip) {
        tooltip.style.visibility = 'hidden';
        tooltip.style.opacity = '0';
    }
}

/**
 * Modal management
 */
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalElement) {
    const modal = modalElement.closest('.modal');
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}

/**
 * Table sorting
 */
function sortTable(header) {
    const table = header.closest('table');
    const index = Array.from(header.parentElement.children).indexOf(header);
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));

    const isAsc = !header.classList.contains('sort-asc');
    
    rows.sort((a, b) => {
        const aValue = a.children[index].textContent.trim();
        const bValue = b.children[index].textContent.trim();
        
        // Try numeric sort if possible
        const aNum = parseFloat(aValue);
        const bNum = parseFloat(bValue);
        
        if (!isNaN(aNum) && !isNaN(bNum)) {
            return isAsc ? aNum - bNum : bNum - aNum;
        }
        
        return isAsc ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
    });

    // Update header classes
    table.querySelectorAll('[data-sortable="true"]').forEach(h => {
        h.classList.remove('sort-asc', 'sort-desc');
    });
    header.classList.add(isAsc ? 'sort-asc' : 'sort-desc');

    // Append sorted rows
    rows.forEach(row => tbody.appendChild(row));
}

/**
 * Table filtering
 */
function filterTable(input) {
    const filterValue = input.value.toLowerCase();
    const targetSelector = input.getAttribute('data-filter-target');
    const targetRows = document.querySelectorAll(targetSelector);

    targetRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filterValue) ? '' : 'none';
    });
}

/**
 * Simulation total update
 */
function updateSimulationTotal() {
    const selectedItems = document.querySelectorAll('.need-item.selected');
    let total = 0;

    selectedItems.forEach(item => {
        const quantityElement = item.querySelector('[data-quantity]');
        if (quantityElement) {
            total += parseInt(quantityElement.getAttribute('data-quantity'), 10) || 0;
        }
    });

    const totalElement = document.getElementById('simulation-total');
    if (totalElement) {
        totalElement.textContent = total;
    }
}

/**
 * Format currency
 */
function formatCurrency(value, currency = 'USD') {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: currency
    }).format(value);
}

/**
 * Format date
 */
function formatDate(date, format = 'short') {
    const options = {
        short: { year: 'numeric', month: 'short', day: 'numeric' },
        long: { year: 'numeric', month: 'long', day: 'numeric' },
        full: { year: 'numeric', month: 'long', day: 'numeric', weekday: 'long' }
    };

    return new Date(date).toLocaleDateString('fr-FR', options[format] || options.short);
}

/**
 * Copy to clipboard
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Copié dans le presse-papiers', 'success');
    }).catch(() => {
        showNotification('Erreur lors de la copie', 'error');
    });
}

/**
 * Show notification toast
 */
function showNotification(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type}`;
    toast.style.position = 'fixed';
    toast.style.top = 'var(--spacing-4)';
    toast.style.right = 'var(--spacing-4)';
    toast.style.zIndex = 'var(--z-tooltip)';
    toast.innerHTML = `
        <div class="alert-icon">
            <i class="bi bi-${type === 'success' ? 'check-circle-fill' : type === 'error' ? 'exclamation-circle-fill' : 'info-circle-fill'}"></i>
        </div>
        <div class="alert-content">
            <p>${message}</p>
        </div>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

/**
 * Debounce function for performance
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Export data to CSV
 */
function exportTableToCSV(tableId, filename = 'export.csv') {
    const table = document.getElementById(tableId);
    let csv = [];
    
    // Get headers
    const headers = table.querySelectorAll('thead th');
    csv.push(Array.from(headers).map(h => `"${h.textContent.trim()}"`).join(','));
    
    // Get rows
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        csv.push(Array.from(cells).map(c => `"${c.textContent.trim()}"`).join(','));
    });

    // Download
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    link.click();
}

// Export functions for global use
window.BNGRCUtils = {
    validateForm,
    showFormError,
    clearFormError,
    openModal,
    closeModal,
    formatCurrency,
    formatDate,
    copyToClipboard,
    showNotification,
    debounce,
    exportTableToCSV
};
