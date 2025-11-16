/**
 * public/static/js/api-sdk.js
 * SDK para consumir APIs de ID Cultural desde Frontend
 * 
 * Uso:
 * const api = new IdCulturalAPI('http://localhost:8080');
 * api.getArtistas().then(data => console.log(data));
 */

class IdCulturalAPI {
    constructor(baseUrl = '/') {
        this.baseUrl = baseUrl.endsWith('/') ? baseUrl : baseUrl + '/';
        this.token = this.getToken();
    }

    /**
     * Obtener token de sesión
     */
    getToken() {
        return localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token') || null;
    }

    /**
     * Guardar token
     */
    setToken(token) {
        localStorage.setItem('auth_token', token);
        this.token = token;
    }

    /**
     * Headers por defecto
     */
    getHeaders() {
        const headers = {
            'Content-Type': 'application/json'
        };

        if (this.token) {
            headers['Authorization'] = `Bearer ${this.token}`;
        }

        return headers;
    }

    /**
     * Request genérico
     */
    async request(method, endpoint, data = null) {
        try {
            const url = this.baseUrl + 'api/' + endpoint;
            const options = {
                method: method,
                headers: this.getHeaders()
            };

            if (data) {
                options.body = JSON.stringify(data);
            }

            const response = await fetch(url, options);
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Error en la solicitud');
            }

            return result;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    /**
     * ============================================
     * ARTISTAS ENDPOINTS
     * ============================================
     */

    /**
     * Obtener todos los artistas validados
     */
    getArtistas() {
        return this.request('GET', 'artistas.php?action=get');
    }

    /**
     * Obtener artista por ID
     */
    getArtista(id) {
        return this.request('GET', `artistas.php?action=get&id=${id}`);
    }

    /**
     * Registrar nuevo artista
     */
    registrarArtista(data) {
        return this.request('POST', 'artistas.php?action=register', data);
    }

    /**
     * Actualizar perfil artista
     */
    actualizarArtista(data) {
        return this.request('POST', 'actualizar_perfil_artista.php', data);
    }

    /**
     * ============================================
     * BORRADORES/PUBLICACIONES ENDPOINTS
     * ============================================
     */

    /**
     * Obtener borradores del artista
     */
    getBorradores() {
        return this.request('POST', 'borradores.php?action=get');
    }

    /**
     * Guardar borrador
     */
    guardarBorrador(data) {
        return this.request('POST', 'borradores.php?action=save', data);
    }

    /**
     * Eliminar borrador
     */
    eliminarBorrador(id) {
        return this.request('POST', 'borradores.php?action=delete', { id });
    }

    /**
     * Obtener publicaciones validadas
     */
    getPublicaciones(filters = {}) {
        const query = new URLSearchParams(filters).toString();
        return this.request('GET', `get_publicaciones.php?${query}`);
    }

    /**
     * ============================================
     * VALIDACIÓN ENDPOINTS
     * ============================================
     */

    /**
     * Obtener solicitudes pendientes (solo validador/admin)
     */
    getSolicitudes() {
        return this.request('GET', 'solicitudes.php?action=get_all');
    }

    /**
     * Validar perfil de artista
     */
    validarPerfil(artistaId, estado, comentario = '') {
        return this.request('POST', 'solicitudes.php?action=update', {
            artista_id: artistaId,
            estado: estado,
            comentario: comentario
        });
    }

    /**
     * ============================================
     * AUTENTICACIÓN ENDPOINTS
     * ============================================
     */

    /**
     * Login
     */
    login(email, password) {
        return this.request('POST', 'login.php', { email, password })
            .then(result => {
                if (result.token) {
                    this.setToken(result.token);
                }
                return result;
            });
    }

    /**
     * Logout
     */
    logout() {
        localStorage.removeItem('auth_token');
        sessionStorage.removeItem('auth_token');
        this.token = null;
        return Promise.resolve();
    }

    /**
     * Cambiar contraseña
     */
    cambiarPassword(passwordActual, passwordNueva) {
        return this.request('POST', 'cambiar_clave.php', {
            password_actual: passwordActual,
            password_nueva: passwordNueva
        });
    }

    /**
     * Solicitar recuperación de contraseña
     */
    solicitarRecuperacion(email) {
        return this.request('POST', 'solicitar_recuperacion_clave.php', { email });
    }

    /**
     * ============================================
     * WIKI ENDPOINTS
     * ============================================
     */

    /**
     * Obtener obras para wiki pública
     */
    getObrasWiki() {
        return this.request('GET', 'get_obras_wiki.php');
    }

    /**
     * ============================================
     * ESTADÍSTICAS ENDPOINTS
     * ============================================
     */

    /**
     * Obtener estadísticas de inicio
     */
    getEstadisticas() {
        return this.request('GET', 'get_estadisticas_inicio.php');
    }

    /**
     * Obtener estadísticas de validador
     */
    getEstadisticasValidador() {
        return this.request('GET', 'get_estadisticas_validador.php');
    }

    /**
     * ============================================
     * NOTICIAS ENDPOINTS
     * ============================================
     */

    /**
     * Obtener noticias
     */
    getNoticias() {
        return this.request('GET', 'noticias.php?action=get_all');
    }

    /**
     * Crear noticia (admin/editor)
     */
    crearNoticia(data) {
        return this.request('POST', 'noticias.php?action=create', data);
    }

    /**
     * ============================================
     * NOTIFICACIONES ENDPOINTS
     * ============================================
     */

    /**
     * Obtener notificaciones del usuario
     */
    getNotificaciones() {
        return this.request('GET', 'notificaciones.php?action=get');
    }

    /**
     * Marcar notificación como leída
     */
    marcarNotificacionLeida(notificacionId) {
        return this.request('POST', 'notificaciones.php?action=mark_read', {
            id: notificacionId
        });
    }

    /**
     * ============================================
     * UTILIDADES
     * ============================================
     */

    /**
     * Validar email
     */
    static validarEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    /**
     * Validar teléfono
     */
    static validarTelefono(telefono) {
        const regex = /^\+?[0-9]{7,}$/;
        return regex.test(telefono.replace(/[\s-]/g, ''));
    }

    /**
     * Validar contraseña (mínimo 8 caracteres, mayúscula, número)
     */
    static validarPassword(password) {
        const regex = /^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).{8,}$/;
        return regex.test(password);
    }

    /**
     * Validar archivo de imagen
     */
    static validarImagen(file) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        const maxSize = 10 * 1024 * 1024; // 10 MB

        if (!allowedTypes.includes(file.type)) {
            return { valid: false, error: 'Tipo de archivo no permitido' };
        }

        if (file.size > maxSize) {
            return { valid: false, error: 'Archivo muy grande (máximo 10 MB)' };
        }

        return { valid: true, error: null };
    }

    /**
     * Validar archivo de video
     */
    static validarVideo(file) {
        const allowedTypes = ['video/mp4', 'video/webm', 'video/quicktime'];
        const maxSize = 500 * 1024 * 1024; // 500 MB

        if (!allowedTypes.includes(file.type)) {
            return { valid: false, error: 'Tipo de video no permitido' };
        }

        if (file.size > maxSize) {
            return { valid: false, error: 'Video muy grande (máximo 500 MB)' };
        }

        return { valid: true, error: null };
    }

    /**
     * Obtener información de archivo
     */
    static getFileInfo(file) {
        return {
            name: file.name,
            size: file.size,
            sizeMB: (file.size / 1024 / 1024).toFixed(2),
            type: file.type,
            extension: file.name.split('.').pop()
        };
    }
}

// Exportar para uso global
if (typeof module !== 'undefined' && module.exports) {
    module.exports = IdCulturalAPI;
}
