<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../backend/config/connection.php';

$page_title = "Wiki - ID Cultural";
$specific_css_files = ['wiki.css'];

// Obtener categorías para el selector
try {
    $stmt = $pdo->prepare("SELECT DISTINCT categoria FROM publicaciones WHERE estado = 'validado' ORDER BY categoria");
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categorias = [];
}

// Obtener municipios para el selector
try {
    $stmt = $pdo->prepare("SELECT DISTINCT municipio FROM artistas WHERE status = 'validado' ORDER BY municipio");
    $stmt->execute();
    $municipios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $municipios = [];
}

include(__DIR__ . '/../components/header.php');
?>

<body>

    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <main class="container-fluid my-4">
        <div class="wiki-pattern"></div>
        
        <!-- Header de la Wiki -->
        <div class="wiki-header text-center mb-5">
            <h1 class="display-4 fw-bold text-gradient">Wiki Cultural de Santiago del Estero</h1>
            <p class="lead">Descubre el talento artístico y cultural de nuestra provincia</p>
            <div class="stats-row">
                <div class="stat-item">
                    <div class="stat-number" id="total-artistas">0</div>
                    <div class="stat-label">Artistas Registrados</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" id="total-obras">0</div>
                    <div class="stat-label">Obras Validadas</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" id="total-categorias">7</div>
                    <div class="stat-label">Categorías Artísticas</div>
                </div>
            </div>
        </div>

        <!-- Barra de Búsqueda Mejorada -->
        <div class="search-hero mb-5">
            <div class="search-container">
                <form id="form-busqueda" action="#" method="get">
                    <div class="search-input-group">
                        <input type="text" class="search-input" placeholder="Buscar artistas, obras, categorías..." name="search" id="search">
                        <select name="categoria" id="categoria" class="search-select">
                            <option value="">Todas las categorías</option>
                            <option value="Artesania">Artesanía</option>
                            <option value="Audiovisual">Audiovisual</option>
                            <option value="Danza">Danza</option>
                            <option value="Teatro">Teatro</option>
                            <option value="Musica">Música</option>
                            <option value="Literatura">Literatura</option>
                            <option value="Escultura">Escultura</option>
                        </select>
                        <button type="submit" class="search-btn">
                            <i class="bi bi-search"></i>
                            Buscar
                        </button>
                        <button type="button" class="clear-btn" onclick="clearFilters()">
                            <i class="bi bi-x-circle"></i>
                            Limpiar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Layout Principal: Sidebar + Content -->
        <div class="row g-4">
            
            <!-- Sidebar -->
            <div class="col-lg-3 col-md-4">
                <div class="wiki-sidebar">
                    
                    <!-- Artista Más Buscado -->
                    <div class="sidebar-card featured-artist">
                        <h4 class="sidebar-title">
                            <i class="bi bi-star-fill"></i>
                            Artista Destacado
                        </h4>
                        <div class="featured-content" id="featured-artist">
                            <div class="featured-image">
                                <img src="/static/img/merce.jpg" alt="Mercedes Sosa" class="img-fluid">
                            </div>
                            <div class="featured-info">
                                <h5>Mercedes Sosa</h5>
                                <p class="featured-category">Música - Folklore</p>
                                <p class="featured-description">La Negra, voz del pueblo argentino y embajadora cultural de Santiago del Estero.</p>
                                <a href="#" class="btn-featured">Ver Perfil</a>
                            </div>
                        </div>
                    </div>

                    <!-- Categorías -->
                    <div class="sidebar-card categories">
                        <h4 class="sidebar-title">
                            <i class="bi bi-grid-3x3-gap"></i>
                            Explorar por Categoría
                        </h4>
                        <div class="category-list">
                            <a href="#musica" class="category-item" data-category="Musica">
                                <i class="bi bi-music-note"></i>
                                <span>Música</span>
                                <span class="category-count" id="count-musica">0</span>
                                <i class="bi bi-x-circle-fill category-close-icon"></i>
                            </a>
                            <a href="#literatura" class="category-item" data-category="Literatura">
                                <i class="bi bi-book"></i>
                                <span>Literatura</span>
                                <span class="category-count" id="count-literatura">0</span>
                                <i class="bi bi-x-circle-fill category-close-icon"></i>
                            </a>
                            <a href="#danza" class="category-item" data-category="Danza">
                                <i class="bi bi-person-arms-up"></i>
                                <span>Danza</span>
                                <span class="category-count" id="count-danza">0</span>
                                <i class="bi bi-x-circle-fill category-close-icon"></i>
                            </a>
                            <a href="#teatro" class="category-item" data-category="Teatro">
                                <i class="bi bi-mask-happy"></i>
                                <span>Teatro</span>
                                <span class="category-count" id="count-teatro">0</span>
                                <i class="bi bi-x-circle-fill category-close-icon"></i>
                            </a>
                            <a href="#artesania" class="category-item" data-category="Artesania">
                                <i class="bi bi-palette"></i>
                                <span>Artesanía</span>
                                <span class="category-count" id="count-artesania">0</span>
                                <i class="bi bi-x-circle-fill category-close-icon"></i>
                            </a>
                            <a href="#audiovisual" class="category-item" data-category="Audiovisual">
                                <i class="bi bi-camera-video"></i>
                                <span>Audiovisual</span>
                                <span class="category-count" id="count-audiovisual">0</span>
                                <i class="bi bi-x-circle-fill category-close-icon"></i>
                            </a>
                            <a href="#escultura" class="category-item" data-category="Escultura">
                                <i class="bi bi-trophy"></i>
                                <span>Escultura</span>
                                <span class="category-count" id="count-escultura">0</span>
                                <i class="bi bi-x-circle-fill category-close-icon"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Filtros Rápidos -->
                    <div class="sidebar-card quick-filters">
                        <h4 class="sidebar-title">
                            <i class="bi bi-funnel"></i>
                            Filtros Rápidos
                        </h4>
                        <div class="filter-buttons">
                            <button class="filter-btn active" data-filter="todos">Todos</button>
                            <button class="filter-btn" data-filter="validados">Solo Validados</button>
                            <button class="filter-btn" data-filter="famosos">Artistas Famosos</button>
                            <button class="filter-btn" data-filter="recientes">Más Recientes</button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="col-lg-9 col-md-8">
                
                <!-- Navegación por Pestañas -->
                <div class="content-tabs">
                    <nav class="tab-navigation">
                        <button class="tab-btn active" data-tab="artistas-validados">
                            <i class="bi bi-people-fill"></i>
                            Artistas Validados
                        </button>
                        <button class="tab-btn" data-tab="obras-validadas">
                            <i class="bi bi-collection"></i>
                            Obras Validadas
                        </button>
                        <button class="tab-btn" data-tab="artistas-famosos">
                            <i class="bi bi-star-fill"></i>
                            Artistas Famosos
                        </button>
                    </nav>
                </div>

                <!-- Contenido de las Pestañas -->
                <div class="tab-content">
                    
                    <!-- Artistas Validados -->
                    <div class="tab-pane active" id="artistas-validados">
                        <div class="content-header">
                            <h3>Artistas Validados de Santiago del Estero</h3>
                            <p>Artistas locales que han pasado por nuestro proceso de validación</p>
                        </div>
                        <div class="artists-grid" id="validated-artists">
                            <!-- Contenido dinámico cargado por JS -->
                            <div class="loading-placeholder">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p>Cargando artistas...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Obras Validadas -->
                    <div class="tab-pane" id="obras-validadas">
                        <div class="content-header">
                            <h3>Obras Culturales Validadas</h3>
                            <p>Creaciones artísticas registradas y validadas en nuestra plataforma</p>
                        </div>
                        <div class="works-grid" id="validated-works">
                            <!-- Contenido dinámico cargado por JS -->
                            <div class="loading-placeholder">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p>Cargando obras...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Artistas Famosos -->
                    <div class="tab-pane" id="artistas-famosos">
                        <div class="content-header">
                            <h3>Artistas Famosos de Santiago del Estero</h3>
                            <p>Referentes culturales y artistas reconocidos de nuestra provincia</p>
                        </div>
                        <div class="famous-artists-grid" id="famous-artists-grid">
                            <div class="row g-4" id="famous-artists-container">
                                <div class="col-lg-6 col-md-6 famous-artist-item" data-category="Musica">
                                    <div class="famous-artist-card">
                                        <div class="famous-image">
                                            <img src="/static/img/merce.jpg" alt="Mercedes Sosa">
                                            <div class="famous-overlay">
                                                <span class="famous-badge">Leyenda</span>
                                            </div>
                                        </div>
                                        <div class="famous-content">
                                            <h4>Mercedes Sosa</h4>
                                            <p class="famous-category">Música - Folklore</p>
                                            <p class="famous-bio">Cantante argentina conocida como "La Negra", embajadora mundial del folklore argentino.</p>
                                            <div class="famous-achievements">
                                                <span class="achievement">🏆 Grammy Latino</span>
                                                <span class="achievement">🌎 Embajadora UNESCO</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-6 col-md-6 famous-artist-item" data-category="Musica">
                                    <div class="famous-artist-card">
                                        <div class="famous-image">
                                            <img src="/static/img/nocheros.jpg" alt="Los Nocheros">
                                            <div class="famous-overlay">
                                                <span class="famous-badge">Nacional</span>
                                            </div>
                                        </div>
                                        <div class="famous-content">
                                            <h4>Los Nocheros</h4>
                                            <p class="famous-category">Música - Folklore</p>
                                            <p class="famous-bio">Grupo musical argentino formado en Salta, con fuerte presencia en Santiago del Estero.</p>
                                            <div class="famous-achievements">
                                                <span class="achievement">🎵 40+ años de carrera</span>
                                                <span class="achievement">💿 20+ discos</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 famous-artist-item" data-category="Musica">
                                    <div class="famous-artist-card">
                                        <div class="famous-image">
                                            <img src="/static/img/chaqueno.jpg" alt="El Chaqueño Palavecino">
                                            <div class="famous-overlay">
                                                <span class="famous-badge">Regional</span>
                                            </div>
                                        </div>
                                        <div class="famous-content">
                                            <h4>El Chaqueño Palavecino</h4>
                                            <p class="famous-category">Música - Folklore</p>
                                            <p class="famous-bio">Cantante de folklore argentino, uno de los máximos exponentes de la música santiagueña.</p>
                                            <div class="famous-achievements">
                                                <span class="achievement">🎤 Voz emblemática</span>
                                                <span class="achievement">🏛️ Declarado de interés cultural</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 famous-artist-item" data-category="Musica">
                                    <div class="famous-artist-card">
                                        <div class="famous-image">
                                            <img src="/static/img/abel.jpg" alt="Abel Pintos">
                                            <div class="famous-overlay">
                                                <span class="famous-badge">Contemporáneo</span>
                                            </div>
                                        </div>
                                        <div class="famous-content">
                                            <h4>Abel Pintos</h4>
                                            <p class="famous-category">Música - Folk/Pop</p>
                                            <p class="famous-bio">Cantautor argentino con raíces folclóricas, reconocido nacionalmente.</p>
                                            <div class="famous-achievements">
                                                <span class="achievement">🎼 Múltiples premios</span>
                                                <span class="achievement">🎪 Estadios completos</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Agregar algunos artistas de otras categorías para demostrar filtros -->
                                <div class="col-lg-6 col-md-6 famous-artist-item" data-category="Literatura">
                                    <div class="famous-artist-card">
                                        <div class="famous-image">
                                            <img src="/static/img/perfil-del-usuario.png" alt="Artista Literario">
                                            <div class="famous-overlay">
                                                <span class="famous-badge">Regional</span>
                                            </div>
                                        </div>
                                        <div class="famous-content">
                                            <h4>Escritores Santiagueños</h4>
                                            <p class="famous-category">Literatura - Narrativa</p>
                                            <p class="famous-bio">Representantes de la literatura regional con obras que reflejan la identidad santiagueña.</p>
                                            <div class="famous-achievements">
                                                <span class="achievement">📚 Tradición oral</span>
                                                <span class="achievement">✍️ Cultura regional</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 famous-artist-item" data-category="Danza">
                                    <div class="famous-artist-card">
                                        <div class="famous-image">
                                            <img src="/static/img/perfil-del-usuario.png" alt="Artista de Danza">
                                            <div class="famous-overlay">
                                                <span class="famous-badge">Tradicional</span>
                                            </div>
                                        </div>
                                        <div class="famous-content">
                                            <h4>Bailarines Folclóricos</h4>
                                            <p class="famous-category">Danza - Folklore</p>
                                            <p class="famous-bio">Cultores de las danzas tradicionales santiagueñas, preservadores de la cultura ancestral.</p>
                                            <div class="famous-achievements">
                                                <span class="achievement">💃 Chacarera</span>
                                                <span class="achievement">🕺 Gato</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                
                <!-- Paginación -->
                <div class="pagination-container">
                    <nav aria-label="Navegación de páginas">
                        <ul class="pagination-custom" id="pagination">
                            <!-- Generado dinámicamente por JS -->
                        </ul>
                    </nav>
                </div>

            </div>
        </div>

    </main>

    <?php include("../components/footer.php"); ?>

    <!-- Meta tag para el base URL en JavaScript -->
    <meta name="base-url" content="<?php echo BASE_URL; ?>">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
    <script src="<?php echo BASE_URL; ?>static/js/wiki.js"></script>
</body>

</html>