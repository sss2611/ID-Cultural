/**
 * Gestión de Obras Pendientes de Validación
 * Archivo: /public/static/js/gestion_pendientes.js
 */

document.addEventListener('DOMContentLoaded', () => {
    // Buscar tbody de manera flexible (puede ser tabla-obras-pendientes-body o tabla-artistas-body con clase tabla-obras-body)
    let tbody = document.getElementById('tabla-obras-pendientes-body');
    console.log('Buscando tbody con ID tabla-obras-pendientes-body:', tbody);
    
    if (!tbody) {
        tbody = document.querySelector('tbody.tabla-obras-body');
        console.log('Buscando tbody con selector .tabla-obras-body:', tbody);
    }
    
    if (!tbody) {
        console.error('No se encontró el elemento tbody para cargar las obras');
        console.log('Elementos tbody disponibles:', document.querySelectorAll('tbody'));
        return;
    }
    console.log('Tbody encontrado:', tbody.id, tbody.className);
    
    let obrasPendientes = [];
    let obrasFiltradas = [];

    // Cargar obras pendientes al iniciar
    cargarObrasPendientes();

    // Event listeners para filtros
    document.getElementById('filtro-busqueda')?.addEventListener('input', aplicarFiltros);
    document.getElementById('filtro-categoria')?.addEventListener('change', aplicarFiltros);
    document.getElementById('filtro-municipio')?.addEventListener('change', aplicarFiltros);

    async function cargarObrasPendientes() {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Cargando obras pendientes...</p></td></tr>';
        
        try {
            console.log('Fetching:', `${BASE_URL}api/get_publicaciones.php?estado=pendiente`);
            const response = await fetch(`${BASE_URL}api/get_publicaciones.php?estado=pendiente`);
            console.log('Response status:', response.status, response.ok);
            
            if (!response.ok) throw new Error('Error al obtener los datos.');
            
            obrasPendientes = await response.json();
            console.log('Obras recibidas:', obrasPendientes);
            console.log('Total de obras:', obrasPendientes.length);
            
            obrasFiltradas = [...obrasPendientes];
            
            // Llenar select de municipios
            llenarSelectMunicipios();
            
            // Mostrar obras
            console.log('Llamando mostrarObras con:', obrasFiltradas);
            mostrarObras(obrasFiltradas);

        } catch (error) {
            console.error('Error completo:', error);
            console.error('Stack:', error.stack);
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4"><i class="bi bi-exclamation-triangle fs-1"></i><p class="mt-2">Error al cargar las obras pendientes.</p></td></tr>';
        }
    }

    function mostrarObras(obras) {
        console.log('mostrarObras llamada con obras:', obras);
        console.log('Longitud:', obras ? obras.length : 'undefined');
        
        if (!obras || obras.length === 0) {
            console.log('No hay obras, mostrando mensaje vacío');
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-5"><i class="bi bi-inbox fs-1 text-muted"></i><p class="mt-3 text-muted">No hay obras pendientes de validación</p></td></tr>';
            return;
        }
        
        console.log('Renderizando', obras.length, 'obras');

        tbody.innerHTML = obras.map(obra => `
            <tr id="obra-${obra.id}" class="obra-row">
                <td class="ps-3">
                    <div>
                        <strong class="d-block">${escapeHtml(obra.titulo)}</strong>
                        <small class="text-muted">
                            <i class="bi bi-person-circle"></i> ${escapeHtml(obra.artista_nombre)}
                            ${obra.es_artista_validado ? '<span class="badge bg-success ms-1">Artista Validado</span>' : ''}
                        </small>
                    </div>
                </td>
                <td>
                    <span class="badge bg-info">${formatearCategoria(obra.categoria)}</span>
                </td>
                <td>
                    <small>${escapeHtml(obra.municipio || 'No especificado')}</small><br>
                    <small class="text-muted">${escapeHtml(obra.provincia || '')}</small>
                </td>
                <td>
                    <small>${formatearFecha(obra.fecha_envio_validacion)}</small>
                </td>
                <td class="text-center">
                    <div class="btn-group-vertical btn-group-sm" role="group">
                        <button class="btn btn-sm btn-outline-primary btn-ver" 
                                data-id="${obra.id}"
                                title="Ver detalles de la obra">
                            <i class="bi bi-eye"></i> Ver Obra
                        </button>
                        <button class="btn btn-sm btn-success btn-aprobar" 
                                data-id="${obra.id}"
                                title="Aprobar esta obra">
                            <i class="bi bi-check-circle"></i> Aprobar
                        </button>
                        <button class="btn btn-sm btn-danger btn-rechazar" 
                                data-id="${obra.id}"
                                title="Rechazar esta obra">
                            <i class="bi bi-x-circle"></i> Rechazar
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        // Agregar event listeners a los botones
        agregarEventListeners();
    }

    function agregarEventListeners() {
        // Botón Ver
        document.querySelectorAll('.btn-ver').forEach(btn => {
            btn.addEventListener('click', () => verDetalleObra(btn.dataset.id));
        });

        // Botón Aprobar
        document.querySelectorAll('.btn-aprobar').forEach(btn => {
            btn.addEventListener('click', () => aprobarObra(btn.dataset.id));
        });

        // Botón Rechazar
        document.querySelectorAll('.btn-rechazar').forEach(btn => {
            btn.addEventListener('click', () => mostrarModalRechazo(btn.dataset.id));
        });
    }

    async function verDetalleObra(obraId) {
        try {
            const response = await fetch(`${BASE_URL}api/get_publicacion_detalle.php?id=${obraId}`);
            if (!response.ok) throw new Error('Error al obtener detalles');
            
            const obra = await response.json();
            mostrarModalDetalle(obra);

        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudo cargar los detalles de la obra', 'error');
        }
    }

    function mostrarModalDetalle(obra) {
        // Construir contenido multimedia
        let multimediaHTML = '';
        if (obra.multimedia && obra.multimedia.length > 0) {
            const multimedia = typeof obra.multimedia === 'string' ? JSON.parse(obra.multimedia) : obra.multimedia;
            multimediaHTML = `
                <div class="mt-3">
                    <h6><i class="bi bi-images"></i> Archivos Multimedia:</h6>
                    <div class="row g-2">
                        ${multimedia.map(file => {
                            if (file.type === 'image' || /\.(jpg|jpeg|png|gif|webp)$/i.test(file.url)) {
                                return `
                                    <div class="col-md-4">
                                        <img src="${escapeHtml(file.url)}" 
                                             class="img-fluid rounded shadow-sm" 
                                             alt="Imagen"
                                             style="cursor: pointer; max-height: 200px; object-fit: cover;"
                                             onclick="window.open('${escapeHtml(file.url)}', '_blank')">
                                    </div>
                                `;
                            } else if (file.type === 'video' || /\.(mp4|webm|ogg)$/i.test(file.url)) {
                                return `
                                    <div class="col-md-6">
                                        <video controls class="w-100 rounded">
                                            <source src="${escapeHtml(file.url)}">
                                        </video>
                                    </div>
                                `;
                            } else if (file.type === 'audio' || /\.(mp3|wav|ogg)$/i.test(file.url)) {
                                return `
                                    <div class="col-12">
                                        <audio controls class="w-100">
                                            <source src="${escapeHtml(file.url)}">
                                        </audio>
                                    </div>
                                `;
                            } else {
                                return `
                                    <div class="col-12">
                                        <a href="${escapeHtml(file.url)}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download"></i> ${file.nombre || 'Descargar archivo'}
                                        </a>
                                    </div>
                                `;
                            }
                        }).join('')}
                    </div>
                </div>
            `;
        }

        // Construir campos extra
        let camposExtraHTML = '';
        if (obra.campos_extra) {
            const campos = typeof obra.campos_extra === 'string' ? JSON.parse(obra.campos_extra) : obra.campos_extra;
            camposExtraHTML = '<div class="mt-3"><h6><i class="bi bi-info-circle"></i> Información Adicional:</h6><dl class="row">';
            for (const [key, value] of Object.entries(campos)) {
                camposExtraHTML += `
                    <dt class="col-sm-4">${formatearClave(key)}:</dt>
                    <dd class="col-sm-8">${escapeHtml(value)}</dd>
                `;
            }
            camposExtraHTML += '</dl></div>';
        }

        Swal.fire({
            title: escapeHtml(obra.titulo),
            html: `
                <div class="text-start">
                    <div class="mb-3">
                        <span class="badge bg-info">${formatearCategoria(obra.categoria)}</span>
                        ${obra.es_artista_validado ? '<span class="badge bg-success">Artista Validado</span>' : '<span class="badge bg-secondary">Usuario</span>'}
                    </div>
                    <p><strong><i class="bi bi-person"></i> Artista:</strong> ${escapeHtml(obra.artista_nombre)}</p>
                    <p><strong><i class="bi bi-geo-alt"></i> Ubicación:</strong> ${escapeHtml(obra.municipio)}, ${escapeHtml(obra.provincia)}</p>
                    <p><strong><i class="bi bi-envelope"></i> Email:</strong> ${escapeHtml(obra.artista_email)}</p>
                    <hr>
                    <h6><i class="bi bi-file-text"></i> Descripción:</h6>
                    <p>${escapeHtml(obra.descripcion)}</p>
                    ${camposExtraHTML}
                    ${multimediaHTML}
                    <hr>
                    <p class="text-muted small mb-0">
                        <i class="bi bi-clock"></i> Enviado el: ${formatearFecha(obra.fecha_envio_validacion)}
                    </p>
                </div>
            `,
            width: '800px',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-check-circle"></i> Aprobar',
            denyButtonText: '<i class="bi bi-x-circle"></i> Rechazar',
            cancelButtonText: 'Cerrar',
            confirmButtonColor: '#28a745',
            denyButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                aprobarObra(obra.id);
            } else if (result.isDenied) {
                mostrarModalRechazo(obra.id);
            }
        });
    }

    async function aprobarObra(obraId) {
        const confirmacion = await Swal.fire({
            title: '¿Aprobar esta obra?',
            text: 'La obra será publicada en la Wiki de Artistas',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-check-circle"></i> Sí, aprobar',
            cancelButtonText: 'Cancelar'
        });

        if (!confirmacion.isConfirmed) return;

        try {
            const formData = new FormData();
            formData.append('id', obraId);
            formData.append('accion', 'validar');

            const response = await fetch(`${BASE_URL}api/validar_publicacion.php`, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.status === 'ok') {
                await Swal.fire({
                    title: '¡Aprobada!',
                    text: result.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
                cargarObrasPendientes();
            } else {
                Swal.fire('Error', result.message || 'No se pudo aprobar la obra', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Error de conexión al servidor', 'error');
        }
    }

    function mostrarModalRechazo(obraId) {
        Swal.fire({
            title: '<i class="bi bi-exclamation-triangle text-danger"></i> Rechazar Obra',
            html: `
                <p class="text-start">Por favor, indica el motivo del rechazo. Esta información será enviada al artista.</p>
                <textarea id="swal-motivo" class="form-control" rows="4" placeholder="Ejemplo: La obra no cumple con los criterios de autenticidad regional..."></textarea>
            `,
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-send"></i> Confirmar Rechazo',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                const motivo = document.getElementById('swal-motivo').value.trim();
                if (!motivo) {
                    Swal.showValidationMessage('Debes proporcionar un motivo de rechazo');
                    return false;
                }
                return motivo;
            }
        }).then(async (result) => {
            if (result.isConfirmed) {
                await rechazarObra(obraId, result.value);
            }
        });
    }

    async function rechazarObra(obraId, motivo) {
        try {
            const formData = new FormData();
            formData.append('id', obraId);
            formData.append('accion', 'rechazar');
            formData.append('motivo', motivo);

            const response = await fetch(`${BASE_URL}api/validar_publicacion.php`, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.status === 'ok') {
                await Swal.fire({
                    title: 'Rechazada',
                    text: result.message,
                    icon: 'info',
                    timer: 2000,
                    showConfirmButton: false
                });
                cargarObrasPendientes();
            } else {
                Swal.fire('Error', result.message || 'No se pudo rechazar la obra', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Error de conexión al servidor', 'error');
        }
    }

    function aplicarFiltros() {
        const busqueda = document.getElementById('filtro-busqueda')?.value.toLowerCase() || '';
        const categoria = document.getElementById('filtro-categoria')?.value || '';
        const municipio = document.getElementById('filtro-municipio')?.value || '';

        obrasFiltradas = obrasPendientes.filter(obra => {
            const coincideBusqueda = obra.titulo.toLowerCase().includes(busqueda) || 
                                     obra.artista_nombre.toLowerCase().includes(busqueda);
            const coincideCategoria = !categoria || obra.categoria === categoria;
            const coincideMunicipio = !municipio || obra.municipio === municipio;

            return coincideBusqueda && coincideCategoria && coincideMunicipio;
        });

        mostrarObras(obrasFiltradas);
    }

    function llenarSelectMunicipios() {
        const select = document.getElementById('filtro-municipio');
        if (!select) return;

        const municipios = [...new Set(obrasPendientes.map(o => o.municipio).filter(Boolean))].sort();
        
        municipios.forEach(mun => {
            const option = document.createElement('option');
            option.value = mun;
            option.textContent = mun;
            select.appendChild(option);
        });
    }

    // ============================================
    // FUNCIONES AUXILIARES
    // ============================================

    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
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
        if (!fecha) return 'No disponible';
        const date = new Date(fecha);
        return date.toLocaleDateString('es-AR', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric'
        });
    }

    function formatearCategoria(categoria) {
        const categorias = {
            'musica': 'Música',
            'artes_visuales': 'Artes Visuales',
            'literatura': 'Literatura',
            'teatro': 'Teatro',
            'danza': 'Danza',
            'fotografia': 'Fotografía',
            'artesania': 'Artesanía',
            'otros': 'Otros'
        };
        return categorias[categoria] || categoria;
    }

    function formatearClave(clave) {
        return clave.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }
});