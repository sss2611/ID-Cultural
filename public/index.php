<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../backend/config/connection.php';

// Obtener el contenido del sitio desde la BD
try {
    $stmt = $pdo->prepare("SELECT content_key, content_value FROM site_content");
    $stmt->execute();
    $site_content = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    $site_content = [];
    error_log("Error al cargar el contenido del sitio: " . $e->getMessage());
}

// Variables para el header
$page_title = "Inicio - ID Cultural";
$specific_css_files = ['index.css'];

include(__DIR__ . '/../components/header.php');
?>

<body>

  <?php include __DIR__ . '/../components/navbar.php'; ?>

  <main>
    <!-- Hero Section con Carrusel Mejorado -->
    <section class="hero-section">
      <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
          <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
          <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
          <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        
        <div class="carousel-inner">
          <!-- Slide 1 -->
          <div class="carousel-item active">
            <div class="carousel-image" style="background-image: url('<?php echo htmlspecialchars($site_content['carousel_image_1'] ?? 'https://placehold.co/1920x800/367789/FFFFFF?text=Cultura+Santiagueña'); ?>');">
              <div class="carousel-overlay"></div>
            </div>
            <div class="carousel-caption-custom">
              <div class="container">
                <h1 class="display-3 fw-bold mb-3" data-aos="fade-up">Visibilizar y Preservar</h1>
                <p class="lead mb-4" data-aos="fade-up" data-aos-delay="100">Un espacio para la identidad artística y cultural de Santiago del Estero</p>
                <a href="/wiki.php?tab=obras-validadas" class="btn btn-light btn-lg rounded-pill px-5" data-aos="fade-up" data-aos-delay="200">
                  <i class="bi bi-compass me-2"></i>Explorar Wiki
                </a>
              </div>
            </div>
          </div>

          <!-- Slide 2 -->
          <div class="carousel-item">
            <div class="carousel-image" style="background-image: url('<?php echo htmlspecialchars($site_content['carousel_image_2'] ?? 'https://placehold.co/1920x800/C30135/FFFFFF?text=Nuestros+Artistas'); ?>');">
              <div class="carousel-overlay"></div>
            </div>
            <div class="carousel-caption-custom">
              <div class="container">
                <h1 class="display-3 fw-bold mb-3" data-aos="fade-up">Nuestros Artistas</h1>
                <p class="lead mb-4" data-aos="fade-up" data-aos-delay="100">Explora la trayectoria de talentos locales, actuales e históricos</p>
                <a href="/wiki.php?tab=artistas-validados" class="btn btn-light btn-lg rounded-pill px-5" data-aos="fade-up" data-aos-delay="200">
                  <i class="bi bi-people me-2"></i>Ver Artistas
                </a>
              </div>
            </div>
          </div>

          <!-- Slide 3 -->
          <div class="carousel-item">
            <div class="carousel-image" style="background-image: url('<?php echo htmlspecialchars($site_content['carousel_image_3'] ?? 'https://placehold.co/1920x800/efc892/333333?text=Biblioteca+Digital'); ?>');">
              <div class="carousel-overlay"></div>
            </div>
            <div class="carousel-caption-custom">
              <div class="container">
                <h1 class="display-3 fw-bold mb-3" data-aos="fade-up">Biblioteca Digital</h1>
                <p class="lead mb-4" data-aos="fade-up" data-aos-delay="100">Accede a un archivo único con material exclusivo de nuestra región</p>
                <a href="/busqueda.php?categoria=Arte" class="btn btn-light btn-lg rounded-pill px-5" data-aos="fade-up" data-aos-delay="200">
                  <i class="bi bi-book me-2"></i>Explorar Biblioteca
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Controles del Carrusel -->
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Siguiente</span>
        </button>
      </div>
    </section>

    <!-- Sección de Bienvenida -->
    <section class="welcome-section py-5">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-4 text-center mb-4 mb-lg-0" data-aos="fade-right">
            <img src="<?php echo BASE_URL; ?>static/img/huella-idcultural.png" 
                 alt="Huella ID Cultural" 
                 class="huella-idcultural img-fluid" 
                 style="max-width: 250px;">
          </div>
          <div class="col-lg-8" data-aos="fade-left">
            <div class="display-4 mb-4">
              <?php 
                $title = $site_content['welcome_title'] ?? '<p>Bienvenidos a ID Cultural</p>';
                // Si contiene HTML de Quill, no escapar; si es texto plano, escapar
                echo (strpos($title, '<p>') !== false) ? $title : htmlspecialchars($title);
              ?>
            </div>
            <div class="lead mb-4">
              <?php echo $site_content['welcome_paragraph'] ?? ''; ?>
            </div>
            <div class="h5 text-primary fst-italic">
              <i class="bi bi-quote me-2"></i>
              <?php 
                $slogan = $site_content['welcome_slogan'] ?? '<p>La identidad de un pueblo, en un solo lugar.</p>';
                // Si contiene HTML de Quill, no escapar; si es texto plano, escapar
                echo (strpos($slogan, '<p>') !== false) ? $slogan : htmlspecialchars($slogan);
              ?>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Sección de Estadísticas Rápidas -->
    <section class="stats-section py-5 bg-light">
      <div class="container">
        <div class="row text-center g-4">
          <div class="col-md-4" data-aos="fade-up" data-aos-delay="0">
            <div class="stat-card">
              <i class="bi bi-people-fill stat-icon text-primary"></i>
              <h3 class="stat-number" id="stat-artistas">0</h3>
              <p class="stat-label">Artistas Validados</p>
            </div>
          </div>
          <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-card">
              <i class="bi bi-palette-fill stat-icon text-success"></i>
              <h3 class="stat-number" id="stat-obras">0</h3>
              <p class="stat-label">Obras Publicadas</p>
            </div>
          </div>
          <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-card">
              <i class="bi bi-newspaper stat-icon text-warning"></i>
              <h3 class="stat-number" id="stat-noticias">0</h3>
              <p class="stat-label">Noticias</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Sección de Noticias -->
    <section class="noticias-section py-5">
      <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
          <h2 class="display-5 mb-3">
            <i class="bi bi-newspaper me-2"></i>Últimas Noticias
          </h2>
          <p class="lead text-muted">Mantente al día con las novedades de la cultura santiagueña</p>
        </div>
        
        <div id="contenedor-noticias" class="row g-4">
          <!-- Loading skeleton -->
          <div class="col-12 text-center">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Cargando noticias...</span>
            </div>
            <p class="mt-3 text-muted">Cargando noticias...</p>
          </div>
        </div>

        <div class="text-center mt-5">
          <a href="/noticias.php" class="btn btn-outline-primary btn-lg rounded-pill px-5">
            <i class="bi bi-arrow-right-circle me-2"></i>Ver Todas las Noticias
          </a>
        </div>
      </div>
    </section>

    <!-- Sección de Call to Action -->
    <section class="cta-section py-5 bg-primary text-white">
      <div class="container text-center">
        <div data-aos="zoom-in">
          <h2 class="display-5 mb-4">¿Eres un artista local?</h2>
          <p class="lead mb-4">Únete a nuestra comunidad y comparte tu talento con el mundo</p>
          <?php if (!isset($_SESSION['user_data'])): ?>
            <a href="/src/views/pages/auth/registro.php" class="btn btn-light btn-lg rounded-pill px-5 me-3">
              <i class="bi bi-person-plus me-2"></i>Registrarse
            </a>
            <a href="/src/views/pages/auth/login.php" class="btn btn-outline-light btn-lg rounded-pill px-5">
              <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
            </a>
          <?php else: ?>
            <a href="/wiki.php" class="btn btn-light btn-lg rounded-pill px-5">
              <i class="bi bi-compass me-2"></i>Explorar la Wiki
            </a>
          <?php endif; ?>
        </div>
      </div>
    </section>

  </main>

  <?php include __DIR__ . '/../components/footer.php'; ?>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    const BASE_URL = '<?php echo BASE_URL; ?>';
  </script>
  <script src="<?php echo BASE_URL; ?>static/js/index.js"></script>

</body>
</html>