document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('tabla-logs-body');

    async function cargarLogs() {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">Cargando registros...</td></tr>';
        try {
            // CORRECCIÓN: Usamos la variable global BASE_URL que definimos en el archivo PHP.
            const response = await fetch(`${BASE_URL}api/get_logs.php`);
            if (!response.ok) throw new Error('Error al obtener los datos.');
            const logs = await response.json();

            tbody.innerHTML = '';
            if (logs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">No hay registros en el sistema.</td></tr>';
                return;
            }

            logs.forEach(log => {
                const fecha = new Date(log.timestamp).toLocaleString('es-AR');
                const fila = `
                    <tr>
                        <td class="ps-3">${fecha}</td>
                        <td><strong>${log.user_name}</strong></td>
                        <td><span class="badge bg-secondary">${log.action}</span></td>
                        <td>${log.details}</td>
                    </tr>
                `;
                tbody.innerHTML += fila;
            });

        } catch (error) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error al cargar los registros.</td></tr>';
        }
    }
    cargarLogs();
});