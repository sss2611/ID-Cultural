/**
 * JavaScript para Panel del Validador
 * Archivo: /static/js/panel_validador.js
 */

document.addEventListener('DOMContentLoaded', function() {
    cargarEstadisticas();
    
    // Actualizar estadísticas cada 30 segundos
    setInterval(cargarEstadisticas, 30000);
});

async function cargarEstadisticas() {
    try {
        const response = await fetch(`${BASE_URL}api/get_estadisticas_validador.php`);
        const data = await response.json();
        
        if (data.status === 'ok') {
            actualizarEstadisticas(data.data);
        } else if (data.pendientes !== undefined) {
            // Formato antiguo de respuesta
            actualizarEstadisticas(data);
        } else {
            console.error('Error al cargar estadísticas:', data.message);
        }
    } catch (error) {
        console.error('Error de conexión:', error);
    }
}

function actualizarEstadisticas(stats) {
    // Actualizar números con animación
    animarNumero('stat-pendientes', stats.pendientes, 'text-warning');
    animarNumero('stat-validados', stats.validados, 'text-success');
    animarNumero('stat-rechazados', stats.rechazados, 'text-danger');
}

function animarNumero(elementId, valorFinal, colorClass = '') {
    const elemento = document.getElementById(elementId);
    if (!elemento) return;
    
    const valorActual = parseInt(elemento.textContent) || 0;
    const diferencia = valorFinal - valorActual;
    const duracion = 500; // ms
    const pasos = 20;
    const incremento = diferencia / pasos;
    const intervalo = duracion / pasos;
    
    let paso = 0;
    
    const timer = setInterval(() => {
        paso++;
        const nuevoValor = Math.round(valorActual + (incremento * paso));
        elemento.textContent = nuevoValor;
        
        // Aplicar clase de color si hay cambios
        if (colorClass && diferencia !== 0) {
            elemento.classList.add(colorClass);
            setTimeout(() => elemento.classList.remove(colorClass), duracion);
        }
        
        if (paso >= pasos) {
            elemento.textContent = valorFinal;
            clearInterval(timer);
        }
    }, intervalo);
}

// Inicializar tooltips de Bootstrap
const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});