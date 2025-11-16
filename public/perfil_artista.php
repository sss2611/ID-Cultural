<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../backend/config/connection.php';

// Obtener ID del artista desde la URL
$artista_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Si no hay ID, mostrar error
if (!$artista_id) {
    header('Location: /index.php');
    exit;
}

// Consultar artista
try {
    // Obtener datos del artista
    $stmt = $pdo->prepare("
        SELECT a.*, 
               (SELECT COUNT(*) FROM publicaciones WHERE usuario_id = a.id AND estado = 'validado') as obras_validadas,
               (SELECT COUNT(*) FROM publicaciones WHERE usuario_id = a.id) as total_obras
        FROM artistas a 
        WHERE a.id = ?
    ");
    $stmt->execute([$artista_id]);
    $artista = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Si no existe, mostrar error
    if (!$artista) {
        header('Location: /index.php');
        exit;
    }
    
    // Verificar permisos de acceso
    $es_propietario = isset($_SESSION['user_data']) && $_SESSION['user_data']['role'] === 'artista' && $_SESSION['user_data']['id'] === $artista_id;
    $es_validado = $artista['status'] === 'validado';
    
    // Si no es propietario y el perfil no está validado, redirigir
    if (!$es_propietario && !$es_validado) {
        header('Location: /index.php');
        exit;
    }
    
} catch (PDOException $e) {
    error_log("Error al obtener artista: " . $e->getMessage());
    header('Location: /index.php');
    exit;
}

// Variables para el header
$page_title = "{$artista['nombre']} {$artista['apellido']} - ID Cultural";
$specific_css_files = ['perfil-artista.css'];

include(__DIR__ . '/../components/header.php');
?>

<body class="profile-page">
    <?php include __DIR__ . '/../components/navbar.php'; ?>
    
    <!-- Hero Header with Background Image -->
    <div class="hero-header" style="background-image: url('../assets/img/sgo.jpg'); background-position: center; background-size: cover; background-attachment: fixed; min-height: 500px; position: relative; display: flex; align-items: flex-start; justify-content: center; padding-top: 60px;">
        <div style="position: absolute; inset: 0; background: linear-gradient(135deg, rgba(54, 119, 137, 0.6), rgba(195, 1, 53, 0.4)), linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.5));"></div>
        <div style="position: relative; z-index: 2; text-align: center; color: white;">
            <h1 class="display-3 fw-bold mb-2">Perfil de Artista</h1>
            <p class="lead fs-5">Explora la trayectoria y obras de nuestros talentos locales</p>
        </div>
    </div>

    <main>
        <!-- Profile Section -->
        <section class="profile-section" style="margin-top: -180px; position: relative; z-index: 10;">
            <div class="container">
                <!-- Profile Card -->
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8">
                        <div class="card shadow-lg border-0 rounded-4 overflow-visible">
                            <div class="card-body text-center p-5">
                                <!-- Avatar -->
                                <div class="mb-4 avatar-container">
                                    <img src="<?php echo htmlspecialchars($artista['foto_perfil'] ?? '/static/img/default-avatar.png'); ?>" alt="<?php echo htmlspecialchars($artista['nombre'] . ' ' . $artista['apellido']); ?>"
                                        class="rounded-circle border border-4 border-white shadow"
                                        style="width: 220px; height: 220px; object-fit: cover; margin-top: -110px; background-color: white;">
                                </div>
                                
                                <!-- Name and Title -->
                                <h2 class="h1 fw-bold mb-2"><?php echo htmlspecialchars($artista['nombre'] . ' ' . $artista['apellido']); ?></h2>
                                <p class="text-muted fs-5 mb-5"><?php echo htmlspecialchars($artista['disciplina'] ?? 'Artista'); ?></p>
                                
                                <!-- Social Links -->
                                <div class="social-links mb-4">
                                    <?php if (!empty($artista['instagram'])): ?>
                                        <a href="<?php echo htmlspecialchars($artista['instagram']); ?>" class="btn btn-outline-primary btn-sm rounded-circle social-btn" title="Instagram" target="_blank">
                                            <i class="bi bi-instagram"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($artista['twitter'])): ?>
                                        <a href="<?php echo htmlspecialchars($artista['twitter']); ?>" class="btn btn-outline-primary btn-sm rounded-circle social-btn" title="Twitter" target="_blank">
                                            <i class="bi bi-twitter-x"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($artista['facebook'])): ?>
                                        <a href="<?php echo htmlspecialchars($artista['facebook']); ?>" class="btn btn-outline-primary btn-sm rounded-circle social-btn" title="Facebook" target="_blank">
                                            <i class="bi bi-facebook"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($artista['sitio_web'])): ?>
                                        <a href="<?php echo htmlspecialchars($artista['sitio_web']); ?>" class="btn btn-outline-primary btn-sm rounded-circle social-btn" title="Sitio Web" target="_blank">
                                            <i class="bi bi-globe"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <!-- Edit Profile Button (solo si es el artista logueado) -->
                                <?php if (isset($_SESSION['user_data']) && $_SESSION['user_data']['role'] === 'artista' && $_SESSION['user_data']['id'] === $artista['id']): ?>
                                    <a href="/src/views/pages/editar-perfil.php" class="btn btn-primary btn-lg rounded-pill px-5 mb-4">
                                        <i class="bi bi-pencil me-2"></i>Editar Perfil
                                    </a>
                                <?php endif; ?>

                                <!-- Description -->
                                <div class="alert alert-light border border-2 border-secondary rounded-3 p-4 text-start">
                                    <?php if (!empty($artista['biografia'])): ?>
                                        <p class="mb-0">
                                            <?php echo nl2br(htmlspecialchars($artista['biografia'])); ?>
                                        </p>
                                    <?php else: ?>
                                        <p class="mb-0 text-muted">
                                            <em>Este artista aún no ha añadido una biografía.</em>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs Section -->
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8">
                        <ul class="nav nav-tabs nav-fill border-bottom-2 mb-4" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-bold" id="obras-tab" data-bs-toggle="tab" data-bs-target="#obras" type="button" role="tab" aria-controls="obras" aria-selected="true">
                                    <i class="bi bi-camera me-2"></i>Obras
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold" id="colaboraciones-tab" data-bs-toggle="tab" data-bs-target="#colaboraciones" type="button" role="tab" aria-controls="colaboraciones" aria-selected="false">
                                    <i class="bi bi-music-note me-2"></i>Colaboraciones
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold" id="favoritos-tab" data-bs-toggle="tab" data-bs-target="#favoritos" type="button" role="tab" aria-controls="favoritos" aria-selected="false">
                                    <i class="bi bi-heart me-2"></i>Favoritos
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content">
                            <!-- Obras Tab -->
                            <div class="tab-pane fade show active" id="obras" role="tabpanel" aria-labelledby="obras-tab">
                                <div class="row g-4">
                                    <?php
                                    // Traer obras validadas del artista
                                    try {
                                        $stmt_obras = $pdo->prepare("
                                            SELECT id, titulo, descripcion, multimedia, fecha_validacion 
                                            FROM publicaciones 
                                            WHERE usuario_id = ? AND estado = 'validado'
                                            ORDER BY fecha_validacion DESC
                                        ");
                                        $stmt_obras->execute([$artista_id]);
                                        $obras = $stmt_obras->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        if (empty($obras)):
                                    ?>
                                        <div class="col-12">
                                            <div class="alert alert-info" role="alert">
                                                <i class="bi bi-info-circle me-2"></i>
                                                Este artista aún no tiene obras validadas.
                                            </div>
                                        </div>
                                    <?php
                                        else:
                                            foreach ($obras as $obra):
                                                $thumbnail = $obra['multimedia'] ? BASE_URL . ltrim($obra['multimedia'], '/') : BASE_URL . 'static/img/paleta-de-pintura.png';
                                    ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="card h-100 shadow-sm border-0 overflow-hidden" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#obraModal" onclick="mostrarObra(<?php echo htmlspecialchars(json_encode($obra)); ?>)">
                                                <img src="<?php echo htmlspecialchars($thumbnail); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($obra['titulo']); ?>" style="height: 250px; object-fit: cover;">
                                                <div class="card-body">
                                                    <h5 class="card-title"><?php echo htmlspecialchars($obra['titulo']); ?></h5>
                                                    <p class="card-text text-muted small"><?php echo htmlspecialchars(substr($obra['descripcion'], 0, 80) . (strlen($obra['descripcion']) > 80 ? '...' : '')); ?></p>
                                                </div>
                                                <div class="card-footer bg-light border-top-0">
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar-check"></i>
                                                        <?php echo date('d/m/Y', strtotime($obra['fecha_validacion'])); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                            endforeach;
                                        endif;
                                    } catch (PDOException $e) {
                                        echo '<div class="col-12"><div class="alert alert-danger">Error al cargar obras</div></div>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <!-- Colaboraciones Tab -->
                            <div class="tab-pane fade" id="colaboraciones" role="tabpanel" aria-labelledby="colaboraciones-tab">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="overflow-hidden rounded-3">
                                            <img src="<?php echo BASE_URL; ?>static/img/paleta-de-pintura.png" class="img-fluid w-100" alt="Colaboración 1" style="aspect-ratio: 1; object-fit: cover;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="overflow-hidden rounded-3">
                                            <img src="<?php echo BASE_URL; ?>static/img/paleta-de-pintura.png" class="img-fluid w-100" alt="Colaboración 2" style="aspect-ratio: 1; object-fit: cover;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="overflow-hidden rounded-3">
                                            <img src="<?php echo BASE_URL; ?>static/img/paleta-de-pintura.png" class="img-fluid w-100" alt="Colaboración 3" style="aspect-ratio: 1; object-fit: cover;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="overflow-hidden rounded-3">
                                            <img src="<?php echo BASE_URL; ?>static/img/paleta-de-pintura.png" class="img-fluid w-100" alt="Colaboración 4" style="aspect-ratio: 1; object-fit: cover;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Favoritos Tab -->
                            <div class="tab-pane fade" id="favoritos" role="tabpanel" aria-labelledby="favoritos-tab">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="overflow-hidden rounded-3">
                                            <img src="<?php echo BASE_URL; ?>static/img/paleta-de-pintura.png" class="img-fluid w-100" alt="Favorito 1" style="aspect-ratio: 1; object-fit: cover;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="overflow-hidden rounded-3">
                                            <img src="<?php echo BASE_URL; ?>static/img/paleta-de-pintura.png" class="img-fluid w-100" alt="Favorito 2" style="aspect-ratio: 1; object-fit: cover;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="overflow-hidden rounded-3">
                                            <img src="<?php echo BASE_URL; ?>static/img/paleta-de-pintura.png" class="img-fluid w-100" alt="Favorito 3" style="aspect-ratio: 1; object-fit: cover;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="overflow-hidden rounded-3">
                                            <img src="<?php echo BASE_URL; ?>static/img/paleta-de-pintura.png" class="img-fluid w-100" alt="Favorito 4" style="aspect-ratio: 1; object-fit: cover;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="overflow-hidden rounded-3">
                                            <img src="<?php echo BASE_URL; ?>static/img/paleta-de-pintura.png" class="img-fluid w-100" alt="Favorito 5" style="aspect-ratio: 1; object-fit: cover;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Modal para ver obra completa -->
    <div class="modal fade" id="obraModal" tabindex="-1" aria-labelledby="obraModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="obraModalLabel">Detalle de Obra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img id="obraImagen" src="" alt="" class="img-fluid mb-3" style="max-height: 400px; object-fit: cover; width: 100%;">
                    <h4 id="obraTitulo"></h4>
                    <p id="obraDescripcion" class="text-muted"></p>
                    <small id="obraFecha" class="text-muted d-block"></small>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';
    </script>
    <script src="<?php echo BASE_URL; ?>static/js/perfil-artista.js"></script>
</body>

</html>