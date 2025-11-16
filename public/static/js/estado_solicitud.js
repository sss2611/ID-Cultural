document.addEventListener('DOMContentLoaded', () => {

    const tbody = document.getElementById('tabla-artistas-body');

    // Función para inicializar los tooltips de Bootstrap
    function inicializarTooltips() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        // Limpiar tooltips viejos para evitar duplicados
        const existingTooltips = bootstrap.Tooltip.getInstance(document.body);
        if (existingTooltips) {
            existingTooltips.dispose();
        }
        [...tooltipTriggerList].forEach(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    }

    async function cargarArtistas() {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">Cargando artistas...</td></tr>';
        try {
            const response = await fetch(`${BASE_URL}api/artistas.php?action=get`);
            if (!response.ok) throw new Error('Error al obtener los datos.');
            const artistas = await response.json();

            tbody.innerHTML = '';
            if (artistas.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">No hay artistas registrados.</td></tr>';
                return;
            }

            artistas.forEach(artista => {
                let badgeClass = 'bg-secondary';
                if (artista.status === 'validado') badgeClass = 'bg-success';
                if (artista.status === 'pendiente') badgeClass = 'bg-warning text-dark';
                if (artista.status === 'rechazado') badgeClass = 'bg-danger';

                // Se añaden los atributos data-bs-toggle y title para los tooltips
                let acciones = `
                    <button class="btn btn-sm btn-outline-secondary btn-edit" data-id="${artista.id}" data-bs-toggle="tooltip" title="Editar Artista"><i class="bi bi-pencil-fill"></i></button>
                    <button class="btn btn-sm btn-outline-danger btn-delete" data-id="${artista.id}" data-bs-toggle="tooltip" title="Eliminar Artista"><i class="bi bi-trash-fill"></i></button>
                `;

                if (artista.status === 'pendiente') {
                    acciones = `
                        <button class="btn btn-sm btn-success btn-aprobar" data-id="${artista.id}">Aprobar</button>
                        <button class="btn btn-sm btn-danger btn-rechazar" data-id="${artista.id}">Rechazar</button>
                    ` + acciones;
                }

                const fila = `
                    <tr>
                        <td class="ps-3"><strong>${artista.nombre} ${artista.apellido}</strong></td>
                        <td>${artista.email}</td>
                        <td class="text-center"><span class="badge ${badgeClass}">${artista.status}</span></td>
                        <td class="text-end pe-3">${acciones}</td>
                    </tr>
                `;
                tbody.innerHTML += fila;
            });
            
            // Se inicializan los tooltips después de crear los botones
            inicializarTooltips();

        } catch (error) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error al cargar los artistas.</td></tr>';
        }
    }

    async function procesarValidacion(id, estado, motivo) {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('status', estado);
        formData.append('motivo', motivo);

        try {
            const response = await fetch(`${BASE_URL}api/update_artista_status.php`, { method: 'POST', body: formData });
            const result = await response.json();
            if (response.ok && result.status === 'ok') {
                Swal.fire('¡Actualizado!', result.message, 'success');
                cargarArtistas();
            } else {
                Swal.fire('Error', result.message, 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
        }
    }

    tbody.addEventListener('click', (e) => {
        const aprobarBtn = e.target.closest('.btn-aprobar');
        const rechazarBtn = e.target.closest('.btn-rechazar');
        const editarBtn = e.target.closest('.btn-edit');
        const eliminarBtn = e.target.closest('.btn-delete');

        if (aprobarBtn) {
            const id = aprobarBtn.dataset.id;
            Swal.fire({
                title: 'Aprobar Artista',
                input: 'textarea',
                inputLabel: 'Comentario (opcional)',
                inputPlaceholder: 'Añade un comentario para el registro interno...',
                showCancelButton: true,
                confirmButtonText: 'Aprobar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    procesarValidacion(id, 'validado', result.value || '');
                }
            });
        }

        if (rechazarBtn) {
            const id = rechazarBtn.dataset.id;
            Swal.fire({
                title: 'Rechazar Artista',
                input: 'textarea',
                inputLabel: 'Motivo del rechazo',
                inputPlaceholder: 'Explica por qué se rechaza la cuenta...',
                showCancelButton: true,
                confirmButtonText: 'Rechazar',
                cancelButtonText: 'Cancelar',
                inputValidator: (value) => {
                    if (!value) {
                        return '¡Necesitas escribir un motivo para el rechazo!'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    procesarValidacion(id, 'rechazado', result.value);
                }
            });
        }

        if (editarBtn) {
            Swal.fire('En desarrollo', 'La funcionalidad para editar artistas se implementará próximamente.', 'info');
        }

        if (eliminarBtn) {
            const id = eliminarBtn.dataset.id;
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Esta acción eliminará permanentemente la cuenta del artista!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, ¡eliminar!',
                cancelButtonText: 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('id', id);
                    try {
                        const response = await fetch(`${BASE_URL}api/delete_artista.php`, {
                            method: 'POST',
                            body: formData
                        });
                        const res = await response.json();
                        if (response.ok && res.status === 'ok') {
                            Swal.fire('¡Eliminado!', res.message, 'success');
                            cargarArtistas();
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                    }
                }
            });
        }
    });

    cargarArtistas();
});
