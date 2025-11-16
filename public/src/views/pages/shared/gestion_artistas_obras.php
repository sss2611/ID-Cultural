<?php
session_start();
require_once __DIR__ . '/../../../../../config.php';

// --- Bloque de seguridad ---
// Solo Admin y Validador pueden acceder
if (!isset($_SESSION['user_data']) || !in_array($_SESSION['user_data']['role'], ['admin', 'validador'])) {
    header('Location: ' . BASE_URL . 'src/views/pages/auth/login.php');
    exit();
}

// Obtener el rol del usuario
$userRole = $_SESSION['user_data']['role'];

// Determinar configuración según el rol
$config = [
    'admin' => [
        'title' => 'Gestión de Artistas',
        'subtitle' => 'Valida, edita o elimina las obras pendientes de validación.',
        'page_title' => 'Gestión de Artistas',
        'back_url' => BASE_URL . 'src/views/pages/admin/dashboard-adm.php',
        'css_files' => ['dashboard.css', 'abm_usuarios.css'],
        'js_file' => 'gestion_pendientes.js',
        'view_mode' => 'admin_view' // Vista con obras
    ],
    'validador' => [
        'title' => 'Obras Pendientes de Validación',
        'subtitle' => 'Revisa y valida las obras enviadas por los artistas',
        'page_title' => 'Gestión de Obras Pendientes',
        'back_url' => BASE_URL . 'src/views/pages/validador/panel_validador.php',
        'css_files' => ['dashboard.css', 'abm_usuarios.css'],
        'js_file' => 'gestion_pendientes.js',
        'view_mode' => 'validator_works' // Vista detallada de obras
    ]
];

$current_config = $config[$userRole];

// --- Variables para el header ---
$page_title = $current_config['page_title'];
$specific_css_files = $current_config['css_files'];

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
                        <h1 class="mb-0"><?php echo htmlspecialchars($current_config['title']); ?></h1>
                        <p class="lead mb-0"><?php echo htmlspecialchars($current_config['subtitle']); ?></p>
                    </div>
                    <a href="<?php echo $current_config['back_url']; ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al Panel
                    </a>
                </div>

                <?php if (in_array($userRole, ['admin', 'validador'])): ?>
                    <!-- Filtros (para Admin y Validador) -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" id="filtro-busqueda" class="form-control" placeholder="Buscar por artista o título...">
                        </div>
                        <div class="col-md-3">
                            <select id="filtro-categoria" class="form-select">
                                <option value="">Todas las categorías</option>
                                <option value="musica">Música</option>
                                <option value="artes_visuales">Artes Visuales</option>
                                <option value="literatura">Literatura</option>
                                <option value="teatro">Teatro</option>
                                <option value="danza">Danza</option>
                                <option value="fotografia">Fotografía</option>
                                <option value="artesania">Artesanía</option>
                                <option value="otros">Otros</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="filtro-municipio" class="form-select">
                                <option value="">Todos los municipios</option>
                                <!-- Se llenan dinámicamente -->
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary w-100" onclick="aplicarFiltros()">
                                <i class="bi bi-funnel"></i> Filtrar
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Tabla -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width: 35%;">Obra y Artista</th>
                                <th style="width: 15%;">Categoría</th>
                                <th style="width: 20%;">Ubicación</th>
                                <th style="width: 15%;">Fecha de Envío</th>
                                <th class="text-center" style="width: 15%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-artistas-body" class="tabla-obras-body">
                            <!-- Las filas se cargarán aquí dinámicamente -->
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p class="mt-3 text-muted">Cargando obras...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <nav aria-label="Paginación" class="mt-3">
                    <ul class="pagination justify-content-center" id="paginacion">
                        <!-- Se genera dinámicamente -->
                    </ul>
                </nav>
            </div>
        </div>
    </main>

    <?php include(__DIR__ . '/../../../../../components/footer.php'); ?>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';
        const USER_ROLE = '<?php echo $userRole; ?>';
        const VIEW_MODE = '<?php echo $current_config['view_mode']; ?>';
    </script>
    <script src="<?php echo BASE_URL; ?>static/js/<?php echo $current_config['js_file']; ?>"></script>
</body>
</html>
