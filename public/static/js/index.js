/**
 * INDEX.JS - ID CULTURAL
 * JavaScript para la página de inicio
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar animaciones AOS
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-out',
            once: true,
            offset: 100
        });
    }

    // Cargar estadísticas
    cargarEstadisticas();

    // Cargar noticias
    cargarNoticias();

    // Animar números de estadísticas cuando entren en viewport
    observarEstadisticas();
});

/**
 * Cargar estadísticas del sitio
 */
async function cargarEstadisticas() {
    try {
        const response = await fetch(`${BASE_URL}api/get_estadisticas_inicio.php`);
        const data = await response.json();

        if (data.status === 'ok' || data.artistas !== undefined) {
            animarContador('stat-artistas', data.artistas || 0);
            animarContador('stat-obras', data.obras || 0);
            animarContador('stat-noticias', data.noticias || 0);
        }
    } catch (error) {
        console.error('Error al cargar estadísticas:', error);
        // Mostrar valores por defecto
        document.getElementById('stat-artistas').textContent = '0';
        document.getElementById('stat-obras').textContent = '0';
        document.getElementById('stat-noticias').textContent = '0';
    }
}

/**
 * Animar contador de números
 */
function animarContador(elementId, valorFinal) {
    const elemento = document.getElementById(elementId);
    if (!elemento) return;

    const duracion = 2000; // 2 segundos
    const pasos = 60;
    const incremento = valorFinal / pasos;
    const intervalo = duracion / pasos;
    
    let valorActual = 0;
    let paso = 0;

    const timer = setInterval(() => {
        paso++;
        valorActual = Math.min(Math.round(incremento * paso), valorFinal);
        elemento.textContent = valorActual.toLocaleString('es-AR');

        if (paso >= pasos) {
            clearInterval(timer);
            elemento.textContent = valorFinal.toLocaleString('es-AR');
        }
    }, intervalo);
}

/**
 * Observar cuándo las estadísticas entran en viewport
 */
function observarEstadisticas() {
    const statsSection = document.querySelector('.stats-section');
    if (!statsSection) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                cargarEstadisticas();
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    observer.observe(statsSection);
}

/**
 * Cargar noticias
 */
async function cargarNoticias() {
    const contenedor = document.getElementById('contenedor-noticias');
    
    try {
        const response = await fetch(`${BASE_URL}api/noticias.php?action=get`);
        const data = await response.json();

        // Verificar si hay error en la respuesta
        if (data.error) {
            console.error('Error en API:', data.error);
            contenedor.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="bi bi-exclamation-triangle fs-1 text-danger"></i>
                    <p class="mt-3 text-danger">Error al cargar las noticias</p>
                    <small class="text-muted">${data.error}</small>
                </div>
            `;
            return;
        }

        // Si data es un array vacío o no hay noticias
        const noticias = Array.isArray(data) ? data.slice(0, 6) : (data.noticias || []);
        
        if (noticias.length === 0) {
            contenedor.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">No hay noticias disponibles en este momento</p>
                </div>
            `;
            return;
        }

        contenedor.innerHTML = noticias.map((noticia, index) => `
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="${index * 100}">
                <article class="card noticia-card h-100" onclick="window.location.href='${BASE_URL}noticias.php'" style="cursor: pointer;">
                    ${noticia.imagen_url ? `
                        <img src="${escapeHtml(noticia.imagen_url)}" 
                             class="card-img-top" 
                             alt="${escapeHtml(noticia.titulo)}"
                             onerror="this.src='https://placehold.co/600x400/0d6efd/FFFFFF?text=Sin+Imagen'">
                    ` : `
                        <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center" style="height: 250px; background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                            <i class="bi bi-newspaper text-white" style="font-size: 4rem; opacity: 0.7;"></i>
                        </div>
                    `}
                    <div class="card-body">
                        <h3 class="noticia-title">${escapeHtml(noticia.titulo)}</h3>
                        <p class="noticia-excerpt">${escapeHtml(truncarTexto(noticia.contenido, 150))}</p>
                        <div class="noticia-meta">
                            <span class="noticia-fecha">
                                <i class="bi bi-calendar3"></i>
                                ${formatearFecha(noticia.fecha_creacion)}
                            </span>
                            <span class="noticia-leer-mas">
                                Leer más <i class="bi bi-arrow-right"></i>
                            </span>
                        </div>
                    </div>
                </article>
            </div>
        `).join('');

        // Re-inicializar AOS para las noticias nuevas
        if (typeof AOS !== 'undefined') {
            AOS.refresh();
        }

    } catch (error) {
        console.error('Error al cargar noticias:', error);
        contenedor.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="bi bi-exclamation-triangle fs-1 text-danger"></i>
                <p class="mt-3 text-danger">Error al cargar las noticias</p>
            </div>
        `;
    }
}

/**
 * Abrir modal con detalles de la noticia
 */
async function abrirModalNoticia(noticiaId) {
    try {
        const response = await fetch(`${BASE_URL}api/noticias.php?action=get&id=${noticiaId}`);
        const noticia = await response.json();

        if (noticia.error) {
            Swal.fire('Error', 'No se pudo cargar la noticia', 'error');
            return;
        }

        // Crear modal con SweetAlert2
        Swal.fire({
            html: `
                <div class="modal-noticia-content text-start">
                    ${noticia.imagen_url ? `
                        <img src="${escapeHtml(noticia.imagen_url)}" 
                             class="modal-noticia-imagen"
                             alt="${escapeHtml(noticia.titulo)}"
                             onerror="this.style.display='none'">
                    ` : ''}
                    <h2 class="mb-3">${escapeHtml(noticia.titulo)}</h2>
                    <div class="mb-3 text-muted">
                        <i class="bi bi-calendar3 me-2"></i>
                        <small>${formatearFechaCompleta(noticia.fecha_creacion)}</small>
                        ${noticia.editor_nombre ? `
                            <span class="ms-3">
                                <i class="bi bi-person me-2"></i>
                                <small>Por ${escapeHtml(noticia.editor_nombre)}</small>
                            </span>
                        ` : ''}
                    </div>
                    <div class="modal-noticia-contenido">
                        ${noticia.contenido}
                    </div>
                </div>
            `,
            showCloseButton: true,
            showConfirmButton: false,
            width: '800px',
            customClass: {
                popup: 'modal-noticia',
                htmlContainer: 'p-0'
            },
            showClass: {
                popup: 'animate__animated animate__fadeInUp animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutDown animate__faster'
            }
        });

    } catch (error) {
        console.error('Error al cargar detalle de noticia:', error);
        Swal.fire('Error', 'No se pudo cargar la noticia', 'error');
    }
}

/**
 * Función auxiliar: Truncar texto
 */
function truncarTexto(texto, maxLength) {
    if (!texto) return '';
    // Eliminar HTML tags
    const textoLimpio = texto.replace(/<[^>]*>/g, '');
    if (textoLimpio.length <= maxLength) return textoLimpio;
    return textoLimpio.substring(0, maxLength) + '...';
}

/**
 * Función auxiliar: Formatear fecha
 */
function formatearFecha(fecha) {
    if (!fecha) return '';
    const date = new Date(fecha);
    const opciones = { day: 'numeric', month: 'short', year: 'numeric' };
    return date.toLocaleDateString('es-AR', opciones);
}

/**
 * Función auxiliar: Formatear fecha completa
 */
function formatearFechaCompleta(fecha) {
    if (!fecha) return '';
    const date = new Date(fecha);
    const opciones = { 
        weekday: 'long',
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return date.toLocaleDateString('es-AR', opciones);
}

/**
 * Función auxiliar: Escapar HTML
 */
function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

/**
 * Mejora de accesibilidad: navegación con teclado en cards
 */
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' || e.key === ' ') {
        const target = e.target.closest('.noticia-card');
        if (target) {
            e.preventDefault();
            target.click();
        }
    }
});

/**
 * Pausar carrusel cuando el usuario interactúa
 */
const carousel = document.querySelector('#heroCarousel');
if (carousel) {
    carousel.addEventListener('mouseenter', function() {
        const bsCarousel = bootstrap.Carousel.getInstance(carousel);
        if (bsCarousel) bsCarousel.pause();
    });

    carousel.addEventListener('mouseleave', function() {
        const bsCarousel = bootstrap.Carousel.getInstance(carousel);
        if (bsCarousel) bsCarousel.cycle();
    });
}

/**
 * Optimización: Lazy loading para imágenes
 */
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                }
                observer.unobserve(img);
            }
        });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

/**
 * Smooth scroll para enlaces internos
 */
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

/**
 * Precargar próxima imagen del carrusel
 */
const preloadNextCarouselImage = () => {
    const activeItem = document.querySelector('.carousel-item.active');
    const nextItem = activeItem?.nextElementSibling || document.querySelector('.carousel-item:first-child');
    
    if (nextItem) {
        const img = nextItem.querySelector('.carousel-image');
        if (img) {
            const bgImage = img.style.backgroundImage;
            const url = bgImage.match(/url\(['"]?(.*?)['"]?\)/)?.[1];
            if (url) {
                const preloader = new Image();
                preloader.src = url;
            }
        }
    }
};

// Ejecutar precarga después de que la página cargue
window.addEventListener('load', preloadNextCarouselImage);