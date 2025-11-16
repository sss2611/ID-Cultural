<?php
session_start();
require_once __DIR__ . '/../../../../../config.php';
require_once __DIR__ . '/../../../../../backend/config/connection.php';

// --- Bloque de seguridad ---
if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'src/views/pages/auth/login.php');
    exit();
}

// --- Variables para el header ---
$page_title = "Panel de Administrador";
$specific_css_files = ['dashboard.css', 'dashboard-adm.css'];

$nombre_admin = $_SESSION['user_data']['nombre'] ?? 'Admin';

// --- CONSULTAS REALES A LA BASE DE DATOS ---
try {
    // Total de usuarios (todos los roles)
    $stmt_users = $pdo->query("SELECT COUNT(*) as total FROM users");
    $total_usuarios = $stmt_users->fetch(PDO::FETCH_ASSOC)['total'];

    // Artistas validados (perfiles aprobados)
    $stmt_validados = $pdo->query("SELECT COUNT(*) as total FROM artistas WHERE status_perfil = 'validado'");
    $total_artistas_validados = $stmt_validados->fetch(PDO::FETCH_ASSOC)['total'];

    // Perfiles pendientes de validación
    $stmt_pendientes = $pdo->query("SELECT COUNT(*) as total FROM artistas WHERE status_perfil = 'pendiente'");
    $perfiles_pendientes_validacion = $stmt_pendientes->fetch(PDO::FETCH_ASSOC)['total'];

    // Obras pendientes de validación
    $stmt_obras_pendientes = $pdo->query("SELECT COUNT(*) as total FROM publicaciones WHERE estado IN ('pendiente', 'pendiente_validacion')");
    $obras_pendientes = $stmt_obras_pendientes->fetch(PDO::FETCH_ASSOC)['total'];

} catch (Exception $e) {
    error_log("Error al obtener estadísticas: " . $e->getMessage());
    $total_usuarios = 0;
    $total_artistas_validados = 0;
    $perfiles_pendientes_validacion = 0;
    $obras_pendientes = 0;
}

// --- Incluir la cabecera ---
include(__DIR__ . '/../../../../../components/header.php');
?>

<body>

    <?php include(__DIR__ . '/../../../../../components/navbar.php'); ?>

    <div class="dashboard-wrapper">
        <main class="container my-5">
            <div class="panel-gestion-header mb-4">
                <h1>Panel de Administrador</h1>
                <p class="lead">Bienvenido de nuevo, <strong><?php echo htmlspecialchars($nombre_admin); ?></strong>. Desde aquí puedes supervisar la plataforma y gestionar las cuentas.</p>
            </div>

            <!-- Alerta con instrucciones -->
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle-fill flex-shrink-0 me-3" style="font-size: 1.5rem;"></i>
                <div>
                    <strong>Tareas del Administrador:</strong> Tu rol principal es la gestión del personal del sitio y la supervisión general del sistema.
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            <!-- Fila de Tarjetas de Estadísticas -->
            <div class="row g-4 mb-5">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card h-100">
                        <div class="stat-card-icon icon-users"><i class="bi bi-people-fill"></i></div>
                        <div class="stat-card-info">
                            <p class="stat-card-title">Total de Usuarios</p>
                            <h3 class="stat-card-number"><?php echo $total_usuarios; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card h-100">
                        <div class="stat-card-icon icon-artists"><i class="bi bi-patch-check-fill"></i></div>
                        <div class="stat-card-info">
                            <p class="stat-card-title">Artistas Validados</p>
                            <h3 class="stat-card-number"><?php echo $total_artistas_validados; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card h-100">
                        <div class="stat-card-icon icon-requests"><i class="bi bi-person-exclamation"></i></div>
                        <div class="stat-card-info">
                            <p class="stat-card-title">Perfiles por Validar</p>
                            <h3 class="stat-card-number"><?php echo $perfiles_pendientes_validacion; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card h-100">
                        <div class="stat-card-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);"><i class="bi bi-file-earmark-text"></i></div>
                        <div class="stat-card-info">
                            <p class="stat-card-title">Obras por Validar</p>
                            <h3 class="stat-card-number"><?php echo $obras_pendientes; ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Menú de Acciones del Administrador con enlaces y textos corregidos -->
            <div class="card">
                <div class="card-header">
                    <h4>Tareas del Administrador</h4>
                    <p class="mb-0 text-muted small">Gestiona las cuentas del equipo y supervisa la actividad del sitio.</p>
                </div>
                <div class="card-body">
                    <div class="dashboard-menu">
                        <a href="<?php echo BASE_URL; ?>src/views/pages/admin/abm_usuarios.php" class="dashboard-item" data-bs-toggle="tooltip" data-bs-placement="top" title="Añadir, editar o eliminar cuentas del personal del sitio (Administradores, Editores, Validadores).">
                            <i class="bi bi-person-badge dashboard-icon"></i> Gestionar Personal
                        </a>
                        <a href="<?php echo BASE_URL; ?>src/views/pages/shared/gestion_perfiles.php" class="dashboard-item" data-bs-toggle="tooltip" data-bs-placement="top" title="Revisar y aprobar o rechazar perfiles públicos de artistas.">
                            <i class="bi bi-person-check dashboard-icon"></i> Validar Perfiles de Artistas
                        </a>
                        <a href="<?php echo BASE_URL; ?>src/views/pages/shared/gestion_artistas_obras.php" class="dashboard-item" data-bs-toggle="tooltip" data-bs-placement="top" title="Revisar y aprobar o rechazar publicaciones y obras de artistas.">
                            <i class="bi bi-file-earmark-check dashboard-icon"></i> Validar Obras
                        </a>
                        <a href="<?php echo BASE_URL; ?>src/views/pages/admin/log_sistema.php" class="dashboard-item" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver un registro de todas las modificaciones importantes realizadas en el sistema.">
                            <i class="bi bi-file-earmark-text-fill dashboard-icon"></i> Ver Log del Sistema
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include(__DIR__ . '/../../../../../components/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
      const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    </script>
</body>
</html>
