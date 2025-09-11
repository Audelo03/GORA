/**
 * ARCHIVO PRINCIPAL DE JAVASCRIPT - GORA
 * 
 * Este archivo contiene las funciones principales de JavaScript
 * para la funcionalidad del sistema.
 */

// ========================================
// FUNCIONES DE NAVEGACIÓN Y UI
// ========================================

/**
 * Inicializa la aplicación cuando el DOM está listo
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Sistema GORA cargado correctamente');
    
    // Inicializar funcionalidades del sidebar
    initializeSidebar();
    
    // Inicializar tooltips de Bootstrap
    initializeTooltips();
    
    // Inicializar modales
    initializeModals();
    
    // Limpiar tooltips antes de salir de la página
    window.addEventListener('beforeunload', function() {
        cleanupAllTooltips();
    });
    
    // Limpiar tooltips cuando se detecta navegación
    window.addEventListener('pagehide', function() {
        cleanupAllTooltips();
    });
    
    // Limpiar tooltips cuando se detecta cambio de visibilidad
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden') {
            cleanupAllTooltips();
        }
    });
    
    // Limpiar tooltips cuando se detecta navegación del navegador
    window.addEventListener('popstate', function() {
        cleanupAllTooltips();
    });
    
    // Limpiar tooltips periódicamente para casos edge
    setInterval(function() {
        const visibleTooltips = document.querySelectorAll('.tooltip.show');
        if (visibleTooltips.length > 0) {
            cleanupAllTooltips();
        }
    }, 5000);
});

/**
 * Inicializa la funcionalidad del sidebar
 */
function initializeSidebar() {
    const toggleBtn = document.getElementById('btn-toggle-sidebar');
    const sidebar = document.getElementById('app-sidebar');
    const content = document.getElementById('app-content');
    
    if (toggleBtn && sidebar && content) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('collapsed');
        });
    }
    
    // Limpiar tooltips cuando se hace clic en enlaces del sidebar
    const sidebarLinks = sidebar.querySelectorAll('a[data-bs-toggle="tooltip"]');
    sidebarLinks.forEach(function(link) {
        // Limpiar al hacer clic
        link.addEventListener('click', function() {
            cleanupAllTooltips();
            // Limpieza adicional con delay para casos edge
            setTimeout(function() {
                cleanupAllTooltips();
            }, 100);
        });
        
        // Limpiar al salir del elemento con el mouse
        link.addEventListener('mouseleave', function() {
            const tooltip = bootstrap.Tooltip.getInstance(link);
            if (tooltip) {
                tooltip.hide();
            }
        });
        
        // Limpiar al perder el foco
        link.addEventListener('blur', function() {
            const tooltip = bootstrap.Tooltip.getInstance(link);
            if (tooltip) {
                tooltip.hide();
            }
        });
    });
}

/**
 * Limpia todos los tooltips existentes
 */
function cleanupAllTooltips() {
    const allTooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    allTooltips.forEach(function(element) {
        const tooltip = bootstrap.Tooltip.getInstance(element);
        if (tooltip) {
            tooltip.hide();
            tooltip.dispose();
        }
    });
    
    // Limpiar también cualquier tooltip que pueda estar en el DOM
    const existingTooltipElements = document.querySelectorAll('.tooltip');
    existingTooltipElements.forEach(function(element) {
        element.remove();
    });
}

/**
 * Inicializa los tooltips de Bootstrap
 */
function initializeTooltips() {
    // Primero, eliminar todos los tooltips existentes para evitar bugs
    cleanupAllTooltips();
    
    // Luego, inicializar todos los tooltips nuevamente
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Inicializa los modales de Bootstrap
 */
function initializeModals() {
    // Cerrar modales automáticamente después de operaciones exitosas
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            // Limpiar formularios cuando se cierra el modal
            const forms = modal.querySelectorAll('form');
            forms.forEach(form => form.reset());
        });
    });
}

// ========================================
// FUNCIONES DE UTILIDAD
// ========================================

/**
 * Muestra una notificación de éxito
 * @param {string} message - Mensaje a mostrar
 */
function showSuccess(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: message,
            timer: 3000,
            showConfirmButton: false
        });
    } else {
        alert('Éxito: ' + message);
    }
}

/**
 * Muestra una notificación de error
 * @param {string} message - Mensaje a mostrar
 */
function showError(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    } else {
        alert('Error: ' + message);
    }
}

/**
 * Muestra una notificación de confirmación
 * @param {string} message - Mensaje a mostrar
 * @param {function} callback - Función a ejecutar si se confirma
 */
function showConfirm(message, callback) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: '¿Estás seguro?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed && callback) {
                callback();
            }
        });
    } else {
        if (confirm(message)) {
            callback();
        }
    }
}

/**
 * Formatea una fecha para mostrar
 * @param {string} dateString - Fecha en formato string
 * @returns {string} - Fecha formateada
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
}

/**
 * Valida un email
 * @param {string} email - Email a validar
 * @returns {boolean} - True si es válido, false si no
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Valida que un campo no esté vacío
 * @param {string} value - Valor a validar
 * @returns {boolean} - True si no está vacío, false si está vacío
 */
function isNotEmpty(value) {
    return value && value.trim().length > 0;
}

// ========================================
// FUNCIONES DE PAGINACIÓN
// ========================================

/**
 * Crea los controles de paginación
 * @param {number} currentPage - Página actual
 * @param {number} totalPages - Total de páginas
 * @param {function} onPageChange - Función a ejecutar al cambiar página
 */
function createPagination(currentPage, totalPages, onPageChange) {
    const paginationContainer = document.getElementById('pagination');
    if (!paginationContainer) return;
    
    let html = '<nav><ul class="pagination justify-content-center">';
    
    // Botón anterior
    html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="${currentPage - 1}">Anterior</a>
    </li>`;
    
    // Números de página
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
            <a class="page-link" href="#" data-page="${i}">${i}</a>
        </li>`;
    }
    
    // Botón siguiente
    html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="${currentPage + 1}">Siguiente</a>
    </li>`;
    
    html += '</ul></nav>';
    
    paginationContainer.innerHTML = html;
    
    // Agregar event listeners
    paginationContainer.addEventListener('click', function(e) {
        e.preventDefault();
        if (e.target.classList.contains('page-link')) {
            const page = parseInt(e.target.dataset.page);
            if (page >= 1 && page <= totalPages && page !== currentPage) {
                onPageChange(page);
            }
        }
    });
}

// ========================================
// FUNCIONES DE BÚSQUEDA
// ========================================

/**
 * Implementa búsqueda con debounce
 * @param {function} searchFunction - Función a ejecutar para buscar
 * @param {number} delay - Delay en milisegundos (por defecto 300)
 */
function setupSearchWithDebounce(searchFunction, delay = 300) {
    let timeoutId;
    
    return function(event) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            searchFunction(event.target.value);
        }, delay);
    };
}

// ========================================
// FUNCIONES DE EXPORTACIÓN
// ========================================

/**
 * Exporta datos a CSV
 * @param {Array} data - Datos a exportar
 * @param {string} filename - Nombre del archivo
 */
function exportToCSV(data, filename) {
    if (!data || data.length === 0) {
        showError('No hay datos para exportar');
        return;
    }
    
    const headers = Object.keys(data[0]);
    const csvContent = [
        headers.join(','),
        ...data.map(row => headers.map(header => `"${row[header] || ''}"`).join(','))
    ].join('\n');
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Exporta un gráfico a PNG
 * @param {string} canvasId - ID del canvas del gráfico
 * @param {string} filename - Nombre del archivo
 */
function exportChartToPNG(canvasId, filename) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) {
        showError('No se encontró el gráfico para exportar');
        return;
    }
    
    const link = document.createElement('a');
    link.download = filename;
    link.href = canvas.toDataURL();
    link.click();
}
