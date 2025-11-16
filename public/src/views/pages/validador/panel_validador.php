<?php
session_start();
require_once __DIR__ . '/../../../../../config.php';

// --- Bloque de seguridad para Validador o Admin ---
if (!isset($_SESSION['user_data']) || !in_array($_SESSION['user_data']['role'], ['validador', 'admin'])) {
    header('Location: ' . BASE_URL . 'src/views/pages/auth/login.php');
    exit();
}

// --- Variables para el header ---
$page_title = "Panel de Validador";
$specific_css_files = ['dashboard.css', 'abm_usuarios.css', 'dashboard-adm.css'];

// --- Incluir la cabecera ---
include(__DIR__ . '/../../../../../components/header.php');
?>
<body class="dashboard-body">

    <?php include(__DIR__ . '/../../../../../components/navbar.php'); ?>

    <main class="container my-5">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="mb-0">Panel de Validador</h1>
                        <p class="lead">Revisa las solicitudes pendientes y gestiona el estado de los artistas.</p>
                    </div>
                </div>

                <!-- Fila de Tarjetas de Estadísticas -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="stat-card h-100">
                            <div class="stat-card-icon icon-requests"><i class="bi bi-hourglass-split"></i></div>
                            <div class="stat-card-info">
                                <p class="stat-card-title">Artistas Pendientes</p>
                                <h3 class="stat-card-number" id="stat-pendientes">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="stat-card h-100">
                            <div class="stat-card-icon icon-artists"><i class="bi bi-patch-check-fill"></i></div>
                            <div class="stat-card-info">
                                <p class="stat-card-title">Artistas Validados</p>
                                <h3 class="stat-card-number" id="stat-validados">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="stat-card h-100">
                            <div class="stat-card-icon icon-users"><i class="bi bi-x-circle-fill"></i></div>
                            <div class="stat-card-info">
                                <p class="stat-card-title">Artistas Rechazados</p>
                                <h3 class="stat-card-number" id="stat-rechazados">0</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menú de Acciones del Validador -->
                <div class="card">
                    <div class="card-header">
                        <h4>Listas de Gestión</h4>
                        <p class="mb-0 text-muted small">Accede a las listas de artistas según su estado de validación.</p>
                    </div>
                    <div class="card-body">
                        <div class="dashboard-menu">
                            <a href="<?php echo BASE_URL; ?>src/views/pages/shared/gestion_perfiles.php" class="dashboard-item" data-bs-toggle="tooltip" title="Revisar y validar los perfiles de artistas para asegurar información apropiada.">
                                <i class="bi bi-person-check dashboard-icon"></i> Validar Perfiles de Artistas
                            </a>
                            <a href="<?php echo BASE_URL; ?>src/views/pages/validador/gestion_pendientes.php" class="dashboard-item" data-bs-toggle="tooltip" title="Revisar y procesar los artistas que esperan validación.">
                                <i class="bi bi-person-exclamation dashboard-icon"></i> Ver Obras Pendientes
                            </a>
                            <a href="<?php echo BASE_URL; ?>src/views/pages/validador/log_validaciones.php" class="dashboard-item" data-bs-toggle="tooltip" title="Consultar el historial de artistas ya validados o rechazados.">
                                <i class="bi bi-file-earmark-text-fill dashboard-icon"></i> Historial de Validaciones
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include(__DIR__ . '/../../../../../components/footer.php'); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';
    </script>
    <script src="<?php echo BASE_URL; ?>static/js/panel_validador.js"></script>
</body>
</html>
