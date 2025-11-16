/**
 * gestionar_perfiles.js
 * Gestión de validación de perfiles de artistas
 */

document.addEventListener('DOMContentLoaded', () => {
    let tbody = document.getElementById('tabla-perfiles-body');
    
    if (!tbody) {
        console.error('No se encontró el elemento tbody para cargar los perfiles');
        return;
    }

    let perfilesPendientes = [];
    perfilesFiltrados = [];

    // Cargar perfiles al iniciar
    cargarPerfiles();

    // Event listeners para filtros
    document.getElementById('filtro-busqueda')?.addEventListener('input', aplicarFiltros);
    document.getElementById('filtro-estado')?.addEventListener('change', aplicarFiltros);
    document.getElementById('filtro-provincia')?.addEventListener('change', aplicarFiltros);

    async function cargarPerfiles() {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Cargando perfiles...</p></td></tr>';
        
        try {
            console.log('Fetching:', `${BASE_URL}api/get_perfiles.php?estado=pendiente`);
            const response = await fetch(`${BASE_URL}api/get_perfiles.php?estado=pendiente`);
            console.log('Response status:', response.status);
            
            if (!response.ok) throw new Error('Error al obtener los datos.');
            
            perfilesPendientes = await response.json();
            console.log('Perfiles recibidos:', perfilesPendientes.length);
            
            perfilesFiltrados = [...perfilesPendientes];
            
            // Llenar select de provincias
            llenarSelectProvincias();
            
            // Mostrar perfiles
            mostrarPerfiles(perfilesFiltrados);

        } catch (error) {
            console.error('Error:', error);
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4"><i class="bi bi-exclamation-triangle fs-1"></i><p class="mt-2">Error al cargar los perfiles pendientes.</p></td></tr>';
        }
    }

    function mostrarPerfiles(perfiles) {
        console.log('mostrarPerfiles llamada con:', perfiles.length, 'perfiles');
        
        if (!perfiles || perfiles.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-5"><i class="bi bi-inbox fs-1 text-muted"></i><p class="mt-3 text-muted">No hay perfiles pendientes de validación</p></td></tr>';
            return;
        }

        tbody.innerHTML = perfiles.map(perfil => {
            const estadoBadge = obtenerBadgeEstado(perfil.status_perfil);
            const bioPreview = perfil.biografia ? perfil.biografia.substring(0, 50) + '...' : 'Sin biografía';
            
            return `
                <tr id="perfil-${perfil.id}" class="perfil-row">
                    <td>
                        <div>
                            <strong class="d-block">${escapeHtml(perfil.nombre + ' ' + perfil.apellido)}</strong>
                            <small class="text-muted">
                                <i class="bi bi-briefcase"></i> 
                                ${escapeHtml(perfil.especialidades || 'Sin especialidad')}
                            </small>
                        </div>
                    </td>
                    <td>
                        <small>${escapeHtml(perfil.email)}</small>
                    </td>
                    <td>
                        <small>${escapeHtml(perfil.municipio || 'No especificado')}</small><br>
                        <small class="text-muted">${escapeHtml(perfil.provincia || '')}</small>
                    </td>
                    <td>
                        ${estadoBadge}
                    </td>
                    <td class="text-center">
                        <div class="btn-group-vertical btn-group-sm" role="group">
                            <button class="btn btn-sm btn-outline-primary btn-ver" 
                                    data-id="${perfil.id}"
                                    title="Ver detalles del perfil">
                                <i class="bi bi-eye"></i> Ver
                            </button>
                            ${perfil.status_perfil === 'pendiente' ? `
                                <button class="btn btn-sm btn-success btn-aprobar" 
                                        data-id="${perfil.id}"
                                        title="Aprobar este perfil">
                                    <i class="bi bi-check-circle"></i> Aprobar
                                </button>
                                <button class="btn btn-sm btn-danger btn-rechazar" 
                                        data-id="${perfil.id}"
                                        title="Rechazar este perfil">
                                    <i class="bi bi-x-circle"></i> Rechazar
                                </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        // Agregar event listeners a los botones
        agregarEventListeners();
    }

    function agregarEventListeners() {
        // Botón Ver
        document.querySelectorAll('.btn-ver').forEach(btn => {
            btn.addEventListener('click', () => verDetallesPerfil(btn.dataset.id));
        });

        // Botón Aprobar
        document.querySelectorAll('.btn-aprobar').forEach(btn => {
            btn.addEventListener('click', () => aprobarPerfil(btn.dataset.id));
        });

        // Botón Rechazar
        document.querySelectorAll('.btn-rechazar').forEach(btn => {
            btn.addEventListener('click', () => mostrarModalRechazo(btn.dataset.id));
        });
    }

    async function verDetallesPerfil(perfilId) {
        try {
            const perfil = perfilesPendientes.find(p => p.id == perfilId);
            if (!perfil) {
                throw new Error('Perfil no encontrado');
            }
            mostrarModalDetalle(perfil);
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudo cargar los detalles del perfil', 'error');
        }
    }

    function mostrarModalDetalle(perfil) {
        // Construir foto de perfil si existe
        let fotoHTML = '';
        if (perfil.foto_perfil) {
            fotoHTML = `
                <div class="mb-3 text-center">
                    <img src="${escapeHtml(perfil.foto_perfil)}" 
                         alt="Foto de perfil" 
                         style="max-width: 200px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                </div>
            `;
        }

        // Construir información de redes sociales
        let redesHTML = '';
        if (perfil.instagram || perfil.facebook || perfil.twitter || perfil.sitio_web) {
            redesHTML = '<div class="mt-3"><h6><i class="bi bi-share"></i> Redes Sociales:</h6>';
            if (perfil.instagram) redesHTML += `<p><i class="bi bi-instagram"></i> <a href="https://instagram.com/${escapeHtml(perfil.instagram)}" target="_blank">${escapeHtml(perfil.instagram)}</a></p>`;
            if (perfil.facebook) redesHTML += `<p><i class="bi bi-facebook"></i> ${escapeHtml(perfil.facebook)}</p>`;
            if (perfil.twitter) redesHTML += `<p><i class="bi bi-twitter"></i> ${escapeHtml(perfil.twitter)}</p>`;
            if (perfil.sitio_web) redesHTML += `<p><i class="bi bi-globe"></i> <a href="${escapeHtml(perfil.sitio_web)}" target="_blank">${escapeHtml(perfil.sitio_web)}</a></p>`;
            redesHTML += '</div>';
        }

        // Mostrar motivo de rechazo si existe
        let motivoHTML = '';
        if (perfil.status_perfil === 'rechazado' && perfil.motivo_rechazo) {
            motivoHTML = `
                <div class="alert alert-danger mt-3">
                    <strong>Motivo de rechazo:</strong>
                    <p>${escapeHtml(perfil.motivo_rechazo)}</p>
                </div>
            `;
        }

        const config = {
            title: escapeHtml(perfil.nombre + ' ' + perfil.apellido),
            html: `
                <div class="text-start">
                    ${fotoHTML}
                    <div class="mb-3">
                        ${obtenerBadgeEstado(perfil.status_perfil)}
                    </div>
                    <p><strong><i class="bi bi-envelope"></i> Email:</strong> ${escapeHtml(perfil.email)}</p>
                    <p><strong><i class="bi bi-geo-alt"></i> Ubicación:</strong> ${escapeHtml(perfil.municipio)}, ${escapeHtml(perfil.provincia)}</p>
                    <p><strong><i class="bi bi-briefcase"></i> Especialidades:</strong> ${escapeHtml(perfil.especialidades || 'No especificado')}</p>
                    <hr>
                    <h6><i class="bi bi-file-text"></i> Biografía:</h6>
                    <p>${escapeHtml(perfil.biografia || 'Sin biografía')}</p>
                    ${redesHTML}
                    ${motivoHTML}
                </div>
            `,
            width: '700px',
            showCancelButton: true,
            cancelButtonText: 'Cerrar'
        };

        // Agregar botones condicionales según estado
        if (perfil.status_perfil === 'pendiente') {
            config.showDenyButton = true;
            config.confirmButtonText = '<i class="bi bi-check-circle"></i> Aprobar';
            config.denyButtonText = '<i class="bi bi-x-circle"></i> Rechazar';
            config.confirmButtonColor = '#28a745';
            config.denyButtonColor = '#dc3545';
        }

        Swal.fire(config).then((result) => {
            if (result.isConfirmed) {
                aprobarPerfil(perfil.id);
            } else if (result.isDenied) {
                mostrarModalRechazo(perfil.id);
            }
        });
    }

    async function aprobarPerfil(perfilId) {
        const confirmacion = await Swal.fire({
            title: '¿Aprobar este perfil?',
            text: 'El artista recibirá una notificación de aprobación',
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
            formData.append('id', perfilId);
            formData.append('accion', 'validar');

            const response = await fetch(`${BASE_URL}api/validar_perfil.php`, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.status === 'ok') {
                await Swal.fire({
                    title: '¡Aprobado!',
                    text: result.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
                cargarPerfiles();
            } else {
                Swal.fire('Error', result.message || 'No se pudo aprobar el perfil', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Error de conexión al servidor', 'error');
        }
    }

    function mostrarModalRechazo(perfilId) {
        Swal.fire({
            title: '<i class="bi bi-exclamation-triangle text-danger"></i> Rechazar Perfil',
            html: `
                <p class="text-start">Por favor, indica el motivo del rechazo. Esta información será enviada al artista para que realice los ajustes necesarios.</p>
                <textarea id="swal-motivo" class="form-control" rows="4" placeholder="Ejemplo: La información en la biografía requiere mayor detalle. Las redes sociales deben estar verificadas..."></textarea>
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
                await rechazarPerfil(perfilId, result.value);
            }
        });
    }

    async function rechazarPerfil(perfilId, motivo) {
        try {
            const formData = new FormData();
            formData.append('id', perfilId);
            formData.append('accion', 'rechazar');
            formData.append('motivo', motivo);

            const response = await fetch(`${BASE_URL}api/validar_perfil.php`, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.status === 'ok') {
                await Swal.fire({
                    title: 'Rechazado',
                    text: result.message,
                    icon: 'info',
                    timer: 2000,
                    showConfirmButton: false
                });
                cargarPerfiles();
            } else {
                Swal.fire('Error', result.message || 'No se pudo rechazar el perfil', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Error de conexión al servidor', 'error');
        }
    }

    function aplicarFiltros() {
        const busqueda = document.getElementById('filtro-busqueda')?.value.toLowerCase() || '';
        const estado = document.getElementById('filtro-estado')?.value || '';
        const provincia = document.getElementById('filtro-provincia')?.value || '';

        perfilesFiltrados = perfilesPendientes.filter(perfil => {
            const coincideBusqueda = perfil.nombre.toLowerCase().includes(busqueda) || 
                                     perfil.apellido.toLowerCase().includes(busqueda) ||
                                     perfil.email.toLowerCase().includes(busqueda);
            const coincideEstado = !estado || perfil.status_perfil === estado;
            const coincideProvincia = !provincia || perfil.provincia === provincia;

            return coincideBusqueda && coincideEstado && coincideProvincia;
        });

        mostrarPerfiles(perfilesFiltrados);
    }

    function llenarSelectProvincias() {
        const select = document.getElementById('filtro-provincia');
        if (!select) return;

        const provincias = [...new Set(perfilesPendientes.map(p => p.provincia).filter(Boolean))].sort();
        
        provincias.forEach(prov => {
            const option = document.createElement('option');
            option.value = prov;
            option.textContent = prov;
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

    function obtenerBadgeEstado(estado) {
        const badges = {
            'pendiente': '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Pendiente</span>',
            'validado': '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Validado</span>',
            'rechazado': '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Rechazado</span>'
        };
        return badges[estado] || '<span class="badge bg-secondary">Desconocido</span>';
    }
});
