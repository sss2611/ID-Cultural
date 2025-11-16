<?php
session_start();
require_once __DIR__ . '/../../../../../config.php';

// --- Bloque de seguridad para Admin ---
if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'src/views/pages/auth/login.php');
    exit();
}

// --- Variables para el header ---
$page_title = "Log del Sistema";
$specific_css_files = ['dashboard.css', 'abm_usuarios.css']; // Reutilizamos estilos

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
                        <h1 class="mb-0">Log del Sistema</h1>
                        <p class="lead">Registro de todas las acciones importantes realizadas en la plataforma.</p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>src/views/pages/admin/dashboard-adm.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al Panel
                    </a>
                </div>

                <!-- Sección de Filtros -->
                <div class="row g-3 mb-4 p-3 bg-light border rounded">
                    <div class="col-md-4">
                        <label for="filter-action" class="form-label">Filtrar por Acción</label>
                        <select id="filter-action" class="form-select">
                            <option value="">Todas las acciones</option>
                            <option value="INICIO DE SESIÓN">Inicio de Sesión</option>
                            <option value="VALIDACIÓN DE ARTISTA">Validaciones</option>
                            <option value="RECHAZO DE ARTISTA">Rechazos</option>
                            <option value="CREACIÓN DE NOTICIA">Creación de Noticias</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filter-user" class="form-label">Filtrar por Usuario</label>
                        <input type="text" id="filter-user" class="form-control" placeholder="Nombre del usuario...">
                    </div>
                    <div class="col-md-4">
                        <label for="filter-date" class="form-label">Filtrar por Fecha</label>
                        <input type="date" id="filter-date" class="form-control">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="ps-3">Fecha y Hora</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Detalles</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-logs-body">
                            <!-- Las filas se cargarán aquí dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php include(__DIR__ . '/../../../../../components/footer.php'); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tbody = document.getElementById('tabla-logs-body');
            let allLogs = []; // Guardamos todos los logs para filtrar

            // Elementos de filtro
            const filterAction = document.getElementById('filter-action');
            const filterUser = document.getElementById('filter-user');
            const filterDate = document.getElementById('filter-date');

            function renderLogs(logs) {
                tbody.innerHTML = '';
                if (logs.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center">No se encontraron registros con los filtros aplicados.</td></tr>';
                    return;
                }

                logs.forEach(log => {
                    const fecha = new Date(log.timestamp).toLocaleString('es-AR');
                    
                    // Lógica para asignar colores a las acciones
                    let badgeClass = 'bg-secondary'; // Color por defecto
                    if (log.action.includes('VALIDACIÓN')) badgeClass = 'bg-success';
                    if (log.action.includes('RECHAZO')) badgeClass = 'bg-danger';
                    if (log.action.includes('INICIO DE SESIÓN')) badgeClass = 'bg-primary';
                    if (log.action.includes('CREACIÓN')) badgeClass = 'bg-warning text-dark';

                    const fila = `
                        <tr>
                            <td class="ps-3">${fecha}</td>
                            <td><strong>${log.user_name}</strong></td>
                            <td><span class="badge ${badgeClass}">${log.action}</span></td>
                            <td>${log.details}</td>
                        </tr>
                    `;
                    tbody.innerHTML += fila;
                });
            }

            function applyFilters() {
                let filteredLogs = allLogs;

                const actionValue = filterAction.value;
                const userValue = filterUser.value.toLowerCase();
                const dateValue = filterDate.value;

                if (actionValue) {
                    filteredLogs = filteredLogs.filter(log => log.action === actionValue);
                }
                if (userValue) {
                    filteredLogs = filteredLogs.filter(log => log.user_name.toLowerCase().includes(userValue));
                }
                if (dateValue) {
                    filteredLogs = filteredLogs.filter(log => {
                        const logDate = new Date(log.timestamp).toISOString().split('T')[0];
                        return logDate === dateValue;
                    });
                }
                
                renderLogs(filteredLogs);
            }

            async function cargarLogs() {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">Cargando registros...</td></tr>';
                try {
                    const response = await fetch('<?php echo BASE_URL; ?>api/get_logs.php');
                    if (!response.ok) throw new Error('Error al obtener los datos.');
                    allLogs = await response.json();
                    renderLogs(allLogs);
                } catch (error) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error al cargar los registros.</td></tr>';
                }
            }

            // Event listeners para los filtros
            filterAction.addEventListener('change', applyFilters);
            filterUser.addEventListener('keyup', applyFilters);
            filterDate.addEventListener('change', applyFilters);

            cargarLogs();
        });
    </script>
</body>
</html>