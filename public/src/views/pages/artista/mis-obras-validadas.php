<?php
session_start();
require_once __DIR__ . '/../../../../../config.php';
require_once __DIR__ . '/../../../../../backend/config/connection.php';

// --- Bloque de seguridad ---
if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['role'] !== 'artista') {
    header('Location: ' . BASE_URL . 'src/views/pages/auth/login.php');
    exit();
}

$usuario_id = $_SESSION['user_data']['id'];

// Obtener obras publicadas del artista
try {
    $stmt = $pdo->prepare("
        SELECT * FROM publicaciones 
        WHERE usuario_id = ? AND estado = 'validado'
        ORDER BY fecha_validacion DESC
    ");
    $stmt->execute([$usuario_id]);
    $obras = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error al obtener obras: " . $e->getMessage());
    $obras = [];
}

// --- Variables para el header ---
$page_title = "Mis Obras Publicadas - ID Cultural";
$specific_css_files = ['dashboard.css'];

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
                        <h1 class="mb-0">✅ Mis Obras Publicadas</h1>
                        <p class="lead">Edita tus obras ya publicadas. Los cambios serán enviados a validación.</p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>src/views/pages/artista/dashboard-artista.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>

                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle"></i> <strong>Importante:</strong> Cualquier edición en tus obras publicadas requiere validación nuevamente.
                </div>

                <?php if (empty($obras)): ?>
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> No tienes obras publicadas aún. <a href="<?php echo BASE_URL; ?>src/views/pages/artista/crear-borrador.php">Crea tu primera obra</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Título</th>
                                    <th>Categoría</th>
                                    <th>Fecha Publicación</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($obras as $obra): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($obra['titulo']); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo htmlspecialchars(substr($obra['descripcion'], 0, 60)) . '...'; ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo htmlspecialchars($obra['categoria']); ?></span>
                                        </td>
                                        <td>
                                            <?php echo date('d/m/Y H:i', strtotime($obra['fecha_validacion'])); ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Publicada</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo BASE_URL; ?>src/views/pages/artista/editar_obra.php?id=<?php echo $obra['id']; ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                                    <i class="bi bi-pencil"></i> Editar
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar" data-id="<?php echo $obra['id']; ?>" title="Eliminar">
                                                    <i class="bi bi-trash"></i> Eliminar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include(__DIR__ . '/../../../../../components/footer.php'); ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Botones eliminar
            const btnsEliminar = document.querySelectorAll('.btn-eliminar');

            btnsEliminar.forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    const id = btn.dataset.id;

                    const result = await Swal.fire({
                        title: '¿Eliminar obra?',
                        text: 'Esta acción no se puede deshacer.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#dc3545'
                    });

                    if (result.isConfirmed) {
                        try {
                            const res = await fetch('/api/eliminar_obra.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ id })
                            });

                            const data = await res.json();

                            if (res.ok && data.success) {
                                Swal.fire('Eliminada', 'La obra ha sido eliminada.', 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', data.error || 'Error al eliminar', 'error');
                            }
                        } catch (err) {
                            console.error(err);
                            Swal.fire('Error', 'Error de conexión', 'error');
                        }
                    }
                });
            });
        });
    </script>

</body>
</html>
