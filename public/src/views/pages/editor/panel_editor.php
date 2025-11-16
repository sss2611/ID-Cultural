<?php
session_start();
require_once __DIR__ . '/../../../../../config.php';

// --- Bloque de seguridad para Editor (y Admin) ---
if (!isset($_SESSION['user_data']) || !in_array($_SESSION['user_data']['role'], ['editor', 'admin'])) {
    // Permitimos que el admin también vea este panel
    header('Location: ' . BASE_URL . 'src/views/pages/auth/login.php');
    exit();
}

// --- Variables para el header ---
$page_title = "Panel de Editor";
// ===== LA CORRECCIÓN ESTÁ AQUÍ =====
$specific_css_files = ['dashboard.css', 'dashboard-adm.css'];
$nombre_editor = $_SESSION['user_data']['nombre'] ?? 'Editor';

// --- Incluir la cabecera ---
include(__DIR__ . '/../../../../../components/header.php');
?>
<body class="dashboard-body">

    <?php include(__DIR__ . '/../../../../../components/navbar.php'); ?>

    <main class="container my-5">
        <div class="card">
            <div class="card-body p-4">
                <div class="panel-gestion-header mb-4">
                    <h1>Panel de Editor</h1>
                    <p class="lead">Bienvenido, <strong><?php echo htmlspecialchars($nombre_editor); ?></strong>. Desde aquí puedes gestionar el contenido del sitio.</p>
                </div>

                <!-- Menú de Acciones del Editor -->
                <div class="card">
                    <div class="card-header">
                        <h4>Tareas del Editor</h4>
                        <p class="mb-0 text-muted small">Gestiona las noticias y el contenido de la página principal.</p>
                    </div>
                    <div class="card-body">
                        <div class="dashboard-menu">
                            <a href="<?php echo BASE_URL; ?>src/views/pages/editor/gestion_noticias.php" class="dashboard-item" data-bs-toggle="tooltip" title="Añadir, editar o eliminar noticias y comunicados.">
                                <i class="bi bi-newspaper dashboard-icon"></i> Gestionar Noticias
                            </a>
                            <a href="<?php echo BASE_URL; ?>src/views/pages/editor/gestion_inicio.php" class="dashboard-item" data-bs-toggle="tooltip" title="Modificar los textos y la imagen de portada de la página de inicio. (Próximamente)">
                                <i class="bi bi-house-gear-fill dashboard-icon"></i> Editar Página Principal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include(__DIR__ . '/../../../../../components/footer.php'); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // Script para inicializar los tooltips en esta página
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
      [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    </script>
</body>
</html>
