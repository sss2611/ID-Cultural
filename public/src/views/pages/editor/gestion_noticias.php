<?php
session_start();
require_once __DIR__ . '/../../../../../config.php';

// --- Bloque de seguridad para Editor ---
if (!isset($_SESSION['user_data']) || !in_array($_SESSION['user_data']['role'], ['editor', 'admin'])) {
    header('Location: ' . BASE_URL . 'src/views/pages/auth/login.php');
    exit();
}

// --- Variables para el header ---
$page_title = "Gestión de Noticias";
$specific_css_files = ['dashboard.css', 'abm_usuarios.css'];

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
                        <h1 class="mb-0">Gestión de Noticias</h1>
                        <p class="lead">Crea, edita y elimina las noticias que aparecen en la página principal.</p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>src/views/pages/editor/panel_editor.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al Panel
                    </a>
                </div>

                <!-- Acordeón para "Añadir Noticia" -->
                <div class="accordion mb-4" id="accordionAnadirNoticia">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdd" aria-expanded="false" aria-controls="collapseAdd">
                                <i class="bi bi-plus-circle-fill me-2"></i> Añadir Nueva Noticia
                            </button>
                        </h2>
                        <div id="collapseAdd" class="accordion-collapse collapse" data-bs-parent="#accordionAnadirNoticia">
                            <div class="accordion-body">
                                <form id="form-add-noticia" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="titulo" class="form-label">Título</label>
                                        <input type="text" class="form-control" id="titulo" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="contenido" class="form-label">Contenido</label>
                                        <textarea class="form-control" id="contenido" rows="5" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="imagen" class="form-label">Imagen de Portada (opcional)</label>
                                        <input class="form-control" type="file" id="imagen" accept="image/*">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Guardar Noticia</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="ps-3">Título</th>
                                <th>Fecha de Creación</th>
                                <th class="text-end pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-noticias-body">
                            <!-- Filas cargadas por JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para Editar Noticia -->
    <div class="modal fade" id="editNoticiaModal" tabindex="-1" aria-labelledby="editNoticiaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editNoticiaModalLabel">Editar Noticia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-edit-noticia" enctype="multipart/form-data">
                        <input type="hidden" id="edit-id">
                        <div class="mb-3">
                            <label for="edit-titulo" class="form-label">Título</label>
                            <input type="text" class="form-control" id="edit-titulo" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-contenido" class="form-label">Contenido</label>
                            <textarea class="form-control" id="edit-contenido" rows="5" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit-imagen" class="form-label">Cambiar Imagen (opcional)</label>
                            <input class="form-control" type="file" id="edit-imagen" accept="image/*">
                            <div class="form-text">Si no seleccionas una nueva imagen, se conservará la actual.</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="save-edit-button">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <?php include(__DIR__ . '/../../../../../components/footer.php'); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';
    </script>
    <script src="<?php echo BASE_URL; ?>static/js/panel_editor.js"></script>
</body>
</html>
