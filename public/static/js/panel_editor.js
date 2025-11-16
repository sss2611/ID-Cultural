/**
 * Panel Editor - Gestión de Noticias
 * Archivo: /static/js/panel_editor.js
 */

document.addEventListener('DOMContentLoaded', function() {
    cargarNoticias();

    // Formulario de agregar noticia
    document.getElementById('form-add-noticia').addEventListener('submit', async function(e) {
        e.preventDefault();
        await agregarNoticia();
    });

    // Botón de guardar edición
    document.getElementById('save-edit-button').addEventListener('click', async function() {
        await guardarEdicion();
    });
});

/**
 * Cargar todas las noticias
 */
async function cargarNoticias() {
    const tbody = document.getElementById('tabla-noticias-body');
    tbody.innerHTML = '<tr><td colspan="3" class="text-center">Cargando noticias...</td></tr>';

    try {
        const response = await fetch(`${BASE_URL}api/noticias.php?action=get`);
        const data = await response.json();

        const noticias = Array.isArray(data) ? data : (data.noticias || []);

        if (noticias.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No hay noticias creadas</td></tr>';
            return;
        }

        tbody.innerHTML = noticias.map(noticia => `
            <tr>
                <td class="ps-3">
                    <strong>${escapeHtml(noticia.titulo)}</strong>
                    ${noticia.imagen_url ? '<i class="bi bi-image text-primary ms-2" title="Con imagen"></i>' : ''}
                </td>
                <td>${formatearFecha(noticia.fecha_creacion)}</td>
                <td class="text-end pe-3">
                    <button class="btn btn-sm btn-outline-primary" onclick="editarNoticia(${noticia.id})" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarNoticia(${noticia.id})" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');

    } catch (error) {
        console.error('Error al cargar noticias:', error);
        tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Error al cargar noticias</td></tr>';
    }
}

/**
 * Agregar nueva noticia
 */
async function agregarNoticia() {
    const titulo = document.getElementById('titulo').value.trim();
    const contenido = document.getElementById('contenido').value.trim();
    const imagenInput = document.getElementById('imagen');

    if (!titulo || !contenido) {
        Swal.fire('Error', 'El título y contenido son obligatorios', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('titulo', titulo);
    formData.append('contenido', contenido);
    
    if (imagenInput.files.length > 0) {
        formData.append('imagen', imagenInput.files[0]);
    }

    try {
        Swal.fire({
            title: 'Guardando...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const response = await fetch(`${BASE_URL}api/noticias.php`, {
            method: 'POST',
            body: formData
        });

        // Leer la respuesta como texto primero para debug
        const responseText = await response.text();
        console.log('Respuesta del servidor:', responseText);

        // Intentar parsear como JSON
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (jsonError) {
            console.error('Error al parsear JSON:', jsonError);
            console.error('Respuesta recibida:', responseText);
            Swal.fire({
                title: 'Error',
                html: `El servidor devolvió una respuesta inválida.<br><small>Revisa la consola para más detalles.</small>`,
                icon: 'error'
            });
            return;
        }

        if (result.status === 'ok') {
            await Swal.fire({
                title: '¡Éxito!',
                text: 'Noticia creada correctamente',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });

            // Limpiar formulario
            document.getElementById('form-add-noticia').reset();
            
            // Cerrar acordeón
            const collapseElement = document.getElementById('collapseAdd');
            const bsCollapse = bootstrap.Collapse.getInstance(collapseElement);
            if (bsCollapse) {
                bsCollapse.hide();
            }

            // Recargar tabla
            cargarNoticias();
        } else {
            Swal.fire('Error', result.message || 'No se pudo guardar la noticia', 'error');
        }

    } catch (error) {
        console.error('Error completo:', error);
        Swal.fire('Error', 'No se pudo conectar con el servidor: ' + error.message, 'error');
    }
}

/**
 * Editar noticia
 */
async function editarNoticia(id) {
    try {
        // Obtener detalles de la noticia
        const response = await fetch(`${BASE_URL}api/noticias.php?action=get&id=${id}`);
        const noticia = await response.json();

        if (noticia.error) {
            Swal.fire('Error', 'No se pudo cargar la noticia', 'error');
            return;
        }

        // Llenar el formulario del modal
        document.getElementById('edit-id').value = noticia.id;
        document.getElementById('edit-titulo').value = noticia.titulo;
        document.getElementById('edit-contenido').value = noticia.contenido;

        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('editNoticiaModal'));
        modal.show();

    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error', 'No se pudo cargar la noticia', 'error');
    }
}

/**
 * Guardar edición
 */
async function guardarEdicion() {
    const id = document.getElementById('edit-id').value;
    const titulo = document.getElementById('edit-titulo').value.trim();
    const contenido = document.getElementById('edit-contenido').value.trim();
    const imagenInput = document.getElementById('edit-imagen');

    if (!titulo || !contenido) {
        Swal.fire('Error', 'El título y contenido son obligatorios', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('id', id);
    formData.append('titulo', titulo);
    formData.append('contenido', contenido);
    
    if (imagenInput.files.length > 0) {
        formData.append('imagen', imagenInput.files[0]);
    }

    try {
        Swal.fire({
            title: 'Guardando...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const response = await fetch(`${BASE_URL}api/noticias.php`, {
            method: 'POST',
            body: formData
        });

        // Leer la respuesta como texto primero para debug
        const responseText = await response.text();
        console.log('Respuesta del servidor:', responseText);

        // Intentar parsear como JSON
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (jsonError) {
            console.error('Error al parsear JSON:', jsonError);
            console.error('Respuesta recibida:', responseText);
            Swal.fire('Error', 'El servidor devolvió una respuesta inválida. Revisa la consola para más detalles.', 'error');
            return;
        }

        if (result.status === 'ok') {
            await Swal.fire({
                title: '¡Actualizada!',
                text: 'Noticia actualizada correctamente',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });

            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editNoticiaModal'));
            modal.hide();

            // Recargar tabla
            cargarNoticias();
        } else {
            Swal.fire('Error', result.message || 'No se pudo actualizar la noticia', 'error');
        }

    } catch (error) {
        console.error('Error completo:', error);
        Swal.fire('Error', 'No se pudo conectar con el servidor: ' + error.message, 'error');
    }
}

/**
 * Eliminar noticia
 */
async function eliminarNoticia(id) {
    const confirmacion = await Swal.fire({
        title: '¿Eliminar noticia?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (!confirmacion.isConfirmed) return;

    try {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);

        const response = await fetch(`${BASE_URL}api/noticias.php`, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status === 'ok') {
            await Swal.fire({
                title: '¡Eliminada!',
                text: 'Noticia eliminada correctamente',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });

            cargarNoticias();
        } else {
            Swal.fire('Error', result.message || 'No se pudo eliminar la noticia', 'error');
        }

    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
    }
}

/**
 * Funciones auxiliares
 */
function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

function formatearFecha(fecha) {
    if (!fecha) return '';
    const date = new Date(fecha);
    return date.toLocaleDateString('es-AR', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
}