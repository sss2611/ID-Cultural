<?php
/**
 * noticias.php
 * Página principal de noticias culturales
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config.php';

// Variables para el header
$page_title = "Noticias Culturales - ID Cultural";
$specific_css_files = ['index.css'];

include(__DIR__ . '/../components/header.php');
?>

<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="noticias-hero">
        <div class="container text-center">
            <div data-aos="fade-down">
                <i class="bi bi-newspaper" style="font-size: 4rem; margin-bottom: 20px;"></i>
                <h1>Noticias Culturales</h1>
                <p>Mantente informado sobre las últimas novedades del mundo cultural santiagueño</p>
            </div>
        </div>
    </section>

    <!-- Contenedor de Noticias -->
    <main class="container pb-5">
        <div id="contenedor-noticias">
            <!-- Loading -->
            <div class="loading-container">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
                        <span class="visually-hidden">Cargando noticias...</span>
                    </div>
                    <p class="mt-3 text-muted fs-5">Cargando noticias...</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Botón Volver Arriba -->
    <button class="btn-volver" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" title="Volver arriba">
        <i class="bi bi-arrow-up"></i>
    </button>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';
        
        // Inicializar AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });

        // Función para escapar HTML
        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        // Función para formatear fecha
        function formatearFecha(fecha) {
            if (!fecha) return '';
            const opciones = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(fecha).toLocaleDateString('es-AR', opciones);
        }

        // Función para renderizar el contenido preservando saltos de línea y formato
        function renderizarContenido(contenido) {
            if (!contenido) return '';
            // Convertir saltos de línea en párrafos
            return contenido
                .split('\n\n')
                .filter(p => p.trim())
                .map(p => `<p>${escapeHtml(p.trim())}</p>`)
                .join('');
        }

        // Cargar todas las noticias
        async function cargarNoticias() {
            const contenedor = document.getElementById('contenedor-noticias');
            
            try {
                const response = await fetch(`${BASE_URL}api/noticias.php?action=get`);
                const data = await response.json();

                if (data.error) {
                    console.error('Error en API:', data.error);
                    contenedor.innerHTML = `
                        <div class="no-noticias">
                            <i class="bi bi-exclamation-triangle text-danger"></i>
                            <h3 class="text-danger">Error al cargar las noticias</h3>
                            <p class="text-muted">${escapeHtml(data.error)}</p>
                            <a href="${BASE_URL}index.php" class="btn btn-primary mt-3">
                                <i class="bi bi-house-door me-2"></i>Volver al inicio
                            </a>
                        </div>
                    `;
                    return;
                }

                const noticias = Array.isArray(data) ? data : (data.noticias || []);
                
                if (noticias.length === 0) {
                    contenedor.innerHTML = `
                        <div class="no-noticias">
                            <i class="bi bi-inbox"></i>
                            <h3>No hay noticias disponibles</h3>
                            <p class="text-muted">Por el momento no hay noticias publicadas. Vuelve pronto para ver las novedades.</p>
                            <a href="${BASE_URL}index.php" class="btn btn-primary mt-3">
                                <i class="bi bi-house-door me-2"></i>Volver al inicio
                            </a>
                        </div>
                    `;
                    return;
                }

                // Renderizar noticias en formato completo
                contenedor.innerHTML = noticias.map((noticia, index) => {
                    const imagenHtml = noticia.imagen_url 
                        ? `<img src="${escapeHtml(noticia.imagen_url)}" class="noticia-imagen" alt="${escapeHtml(noticia.titulo)}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">`
                        : '';
                    
                    return `
                        <article class="noticia-individual" data-aos="fade-up" data-aos-delay="${index * 100}">
                            ${imagenHtml}
                            <div class="noticia-placeholder" style="display: ${noticia.imagen_url ? 'none' : 'flex'};">
                                <i class="bi bi-newspaper text-white" style="font-size: 5rem; opacity: 0.7;"></i>
                            </div>
                            <div class="noticia-contenido">
                                <h2 class="noticia-titulo">${escapeHtml(noticia.titulo)}</h2>
                                <div class="noticia-meta">
                                    <span>
                                        <i class="bi bi-calendar3"></i>
                                        ${formatearFecha(noticia.fecha_creacion)}
                                    </span>
                                    ${noticia.autor ? `
                                        <span>
                                            <i class="bi bi-person"></i>
                                            ${escapeHtml(noticia.autor)}
                                        </span>
                                    ` : ''}
                                </div>
                                <div class="noticia-texto">
                                    ${renderizarContenido(noticia.contenido)}
                                </div>
                            </div>
                        </article>
                    `;
                }).join('');

                // Re-inicializar AOS
                AOS.refresh();

            } catch (error) {
                console.error('Error al cargar noticias:', error);
                contenedor.innerHTML = `
                    <div class="no-noticias">
                        <i class="bi bi-exclamation-triangle text-danger"></i>
                        <h3 class="text-danger">Error al cargar las noticias</h3>
                        <p class="text-muted">Hubo un problema al conectar con el servidor. Por favor, intenta nuevamente más tarde.</p>
                        <a href="${BASE_URL}index.php" class="btn btn-primary mt-3">
                            <i class="bi bi-house-door me-2"></i>Volver al inicio
                        </a>
                    </div>
                `;
            }
        }

        // Cargar noticias al cargar la página
        document.addEventListener('DOMContentLoaded', cargarNoticias);
    </script>

    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .noticias-hero {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 80px 0 60px;
            margin-bottom: 50px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .noticias-hero h1 {
            font-size: 3rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .noticias-hero p {
            font-size: 1.2rem;
            opacity: 0.95;
        }

        .noticia-individual {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .noticia-individual:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .noticia-imagen {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-bottom: 5px solid #0d6efd;
        }

        .noticia-placeholder {
            width: 100%;
            height: 400px;
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 5px solid #0a58ca;
        }

        .noticia-contenido {
            padding: 40px;
        }

        .noticia-titulo {
            font-size: 2rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 20px;
            line-height: 1.3;
        }

        .noticia-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
            color: #6c757d;
            font-size: 0.95rem;
        }

        .noticia-meta i {
            margin-right: 5px;
            color: #0d6efd;
        }

        .noticia-texto {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #495057;
            text-align: justify;
        }

        .noticia-texto p {
            margin-bottom: 20px;
        }

        .loading-container {
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .no-noticias {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .no-noticias i {
            font-size: 5rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .btn-volver {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #0d6efd;
            color: white;
            border: none;
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            z-index: 1000;
        }

        .btn-volver:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.6);
            background: #0a58ca;
        }

        @media (max-width: 768px) {
            .noticias-hero h1 {
                font-size: 2rem;
            }

            .noticia-imagen,
            .noticia-placeholder {
                height: 250px;
            }

            .noticia-contenido {
                padding: 25px;
            }

            .noticia-titulo {
                font-size: 1.5rem;
            }

            .noticia-texto {
                font-size: 1rem;
            }
        }
    </style>
</body>
</html>
