-- Tabla de Notificaciones - ID Cultural
-- Migración: Agregar sistema de notificaciones integrado

CREATE TABLE IF NOT EXISTS notificaciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    tipo ENUM('info', 'success', 'warning', 'error', 'validacion', 'mensaje') DEFAULT 'info',
    titulo VARCHAR(255) NOT NULL,
    mensaje TEXT NOT NULL,
    datos_adicionales JSON,
    leida BOOLEAN DEFAULT FALSE,
    fecha_lectura DATETIME,
    url_accion VARCHAR(500),
    icono VARCHAR(50),
    color VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_leida (leida),
    INDEX idx_tipo (tipo),
    INDEX idx_created_at (created_at)
);

-- Tabla de preferencias de notificaciones
CREATE TABLE IF NOT EXISTS preferencias_notificaciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    notificaciones_email BOOLEAN DEFAULT TRUE,
    notificaciones_perfil BOOLEAN DEFAULT TRUE,
    notificaciones_validacion BOOLEAN DEFAULT TRUE,
    notificaciones_comentarios BOOLEAN DEFAULT TRUE,
    notificaciones_mensajes BOOLEAN DEFAULT TRUE,
    frecuencia_email ENUM('inmediato', 'diario', 'semanal', 'nunca') DEFAULT 'diario',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_usuario (usuario_id)
);

-- Tabla de plantillas de notificaciones
CREATE TABLE IF NOT EXISTS plantillas_notificaciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(100) NOT NULL UNIQUE,
    titulo_template VARCHAR(255) NOT NULL,
    mensaje_template TEXT NOT NULL,
    tipo ENUM('info', 'success', 'warning', 'error', 'validacion', 'mensaje') DEFAULT 'info',
    icono VARCHAR(50),
    color VARCHAR(20),
    url_accion_template VARCHAR(500),
    descripcion VARCHAR(500),
    variables JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_codigo (codigo)
);

-- Insertar plantillas predefinidas
INSERT INTO plantillas_notificaciones (codigo, titulo_template, mensaje_template, tipo, icono, color, descripcion, variables) VALUES
('perfil_validado', 'Perfil Validado', 'Tu perfil ha sido validado exitosamente', 'success', 'bi-check-circle', 'success', 'Se envía cuando un artista es validado', '["artista_nombre"]'),
('perfil_rechazado', 'Perfil Rechazado', 'Tu perfil ha sido rechazado. Razón: {razon}', 'error', 'bi-x-circle', 'danger', 'Se envía cuando un artista es rechazado', '["razon"]'),
('solicitud_nueva', 'Nueva Solicitud de Validación', 'Nueva solicitud de validación: {artista_nombre}', 'info', 'bi-inbox', 'info', 'Se envía a validadores', '["artista_nombre", "url"]'),
('comentario_nuevo', 'Nuevo Comentario', '{usuario_nombre} comentó en tu perfil', 'info', 'bi-chat-dots', 'info', 'Se envía cuando hay comentario nuevo', '["usuario_nombre"]'),
('obra_comentada', 'Nueva Obra Comentada', 'Comentaron en tu obra: {nombre_obra}', 'info', 'bi-chat-dots', 'info', 'Comentarios en obras', '["nombre_obra"]'),
('evento_proximo', 'Evento Próximo', 'No olvides: {nombre_evento} en {fecha}', 'warning', 'bi-calendar-event', 'warning', 'Recordatorio de evento', '["nombre_evento", "fecha"]'),
('mensaje_nuevo', 'Mensaje Nuevo', '{usuario_nombre} te envió un mensaje', 'mensaje', 'bi-envelope', 'info', 'Nuevo mensaje', '["usuario_nombre"]');
