document.addEventListener('DOMContentLoaded', () => {

    const tbody = document.getElementById('tabla-solicitudes-body');

    async function cargarSolicitudes() {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center">Cargando tus solicitudes...</td></tr>';
        try {
            const response = await fetch(`${BASE_URL}api/solicitudes.php?action=get_my`);
            if (!response.ok) throw new Error('Error al obtener los datos.');
            const solicitudes = await response.json();

            tbody.innerHTML = '';
            if (solicitudes.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center">No has enviado ninguna publicación a validación.</td></tr>';
                return;
            }

            solicitudes.forEach(solicitud => {
                let badgeClass = 'bg-secondary';
                if (solicitud.estado === 'validado') badgeClass = 'bg-success';
                if (solicitud.estado === 'pendiente') badgeClass = 'bg-warning text-dark';
                if (solicitud.estado === 'rechazado') badgeClass = 'bg-danger';

                const fechaEnvio = new Date(solicitud.fecha_envio_validacion).toLocaleDateString('es-AR');
                
                const fila = `
                    <tr>
                        <td class="ps-3"><strong>${solicitud.titulo}</strong></td>
                        <td>${fechaEnvio}</td>
                        <td class="text-center">
                            <span class="badge ${badgeClass}">${solicitud.estado}</span>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += fila;
            });

        } catch (error) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Error al cargar tus solicitudes.</td></tr>';
        }
    }

    cargarSolicitudes();
});
