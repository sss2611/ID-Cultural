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

// Obtener datos actuales del perfil
try {
    $stmt = $pdo->prepare("SELECT * FROM artistas WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $artista = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$artista) {
        header('Location: ' . BASE_URL . 'src/views/pages/auth/login.php');
        exit();
    }
} catch (Exception $e) {
    error_log("Error al obtener datos del artista: " . $e->getMessage());
    $artista = [];
}

// --- Variables para el header ---
$page_title = "Editar Perfil - ID Cultural";
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
                        <h1 class="mb-0">Editar Perfil</h1>
                        <p class="lead">Actualiza tu información personal y datos de contacto.</p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>src/views/pages/artista/dashboard-artista.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al Dashboard
                    </a>
                </div>

                <form id="form-editar-perfil" method="POST" class="needs-validation" novalidate>
                    <!-- Sección: Datos Personales -->
                    <h4 class="mb-3 mt-4">Datos Personales</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="nombre" 
                                name="nombre" 
                                value="<?php echo htmlspecialchars($artista['nombre']); ?>"
                                required
                            >
                            <div class="invalid-feedback">Por favor ingresa tu nombre.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="apellido" 
                                name="apellido" 
                                value="<?php echo htmlspecialchars($artista['apellido']); ?>"
                                required
                            >
                            <div class="invalid-feedback">Por favor ingresa tu apellido.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                            <input 
                                type="date" 
                                class="form-control" 
                                id="fecha_nacimiento" 
                                name="fecha_nacimiento" 
                                value="<?php echo htmlspecialchars($artista['fecha_nacimiento']); ?>"
                                required
                            >
                            <div class="invalid-feedback">Por favor selecciona tu fecha de nacimiento.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="genero" class="form-label">Género</label>
                            <select class="form-select" id="genero" name="genero" required>
                                <option value="">Selecciona un género</option>
                                <option value="Masculino" <?php echo $artista['genero'] === 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                                <option value="Femenino" <?php echo $artista['genero'] === 'Femenino' ? 'selected' : ''; ?>>Femenino</option>
                                <option value="Otro" <?php echo $artista['genero'] === 'Otro' ? 'selected' : ''; ?>>Otro</option>
                                <option value="Prefiero no especificar" <?php echo $artista['genero'] === 'Prefiero no especificar' ? 'selected' : ''; ?>>Prefiero no especificar</option>
                            </select>
                            <div class="invalid-feedback">Por favor selecciona un género.</div>
                        </div>
                    </div>

                    <!-- Sección: Ubicación -->
                    <h4 class="mb-3 mt-4">Ubicación</h4>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="pais" class="form-label">País</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="pais" 
                                name="pais" 
                                value="<?php echo htmlspecialchars($artista['pais'] ?? ''); ?>"
                                required
                            >
                            <div class="invalid-feedback">Por favor ingresa tu país.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="provincia" class="form-label">Provincia</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="provincia" 
                                name="provincia" 
                                value="<?php echo htmlspecialchars($artista['provincia'] ?? ''); ?>"
                                required
                            >
                            <div class="invalid-feedback">Por favor ingresa tu provincia.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="municipio" class="form-label">Municipio</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="municipio" 
                                name="municipio" 
                                value="<?php echo htmlspecialchars($artista['municipio'] ?? ''); ?>"
                                required
                            >
                            <div class="invalid-feedback">Por favor ingresa tu municipio.</div>
                        </div>
                    </div>

                    <!-- Sección: Contacto -->
                    <h4 class="mb-3 mt-4">Información de Contacto</h4>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input 
                                type="email" 
                                class="form-control" 
                                id="email" 
                                name="email" 
                                value="<?php echo htmlspecialchars($artista['email']); ?>"
                                disabled
                            >
                            <small class="text-muted">El email no puede ser modificado. Contacta con soporte si necesitas cambiar tu email.</small>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex gap-3 mt-5">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Guardar Cambios
                        </button>
                        <a href="<?php echo BASE_URL; ?>src/views/pages/artista/dashboard-artista.php" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include(__DIR__ . '/../../../../../components/footer.php'); ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('form-editar-perfil');

            if (form) {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    // Validación del formulario
                    if (!form.checkValidity()) {
                        e.stopPropagation();
                        form.classList.add('was-validated');
                        return;
                    }

                    // Recopilar datos
                    const datos = {
                        nombre: document.getElementById('nombre').value.trim(),
                        apellido: document.getElementById('apellido').value.trim(),
                        fecha_nacimiento: document.getElementById('fecha_nacimiento').value,
                        genero: document.getElementById('genero').value,
                        pais: document.getElementById('pais').value.trim(),
                        provincia: document.getElementById('provincia').value.trim(),
                        municipio: document.getElementById('municipio').value.trim()
                    };

                    try {
                        const res = await fetch('/api/actualizar_perfil_artista.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(datos)
                        });

                        const data = await res.json();

                        if (res.ok && data.success) {
                            Swal.fire({
                                title: '✓ Éxito',
                                text: 'Tu perfil ha sido actualizado correctamente.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href = '/src/views/pages/artista/dashboard-artista.php';
                            });
                        } else {
                            Swal.fire('Error', data.error || 'Error al actualizar el perfil', 'error');
                        }
                    } catch (err) {
                        console.error(err);
                        Swal.fire('Error', 'Error de conexión', 'error');
                    }
                });
            }
        });
    </script>

</body>
</html>
