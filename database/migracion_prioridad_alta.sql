-- Migración: Agregar tabla de tokens de recuperación de contraseña
-- Fecha: 2025-11-07

-- Crear tabla si no existe
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_expiracion DATETIME NOT NULL,
    usado BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (usuario_id) REFERENCES artistas(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_usuario_usado (usuario_id, usado),
    INDEX idx_fecha_expiracion (fecha_expiracion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar columna de 'multimedia' si no existe (para compatibilidad)
ALTER TABLE publicaciones 
ADD COLUMN IF NOT EXISTS multimedia LONGTEXT COMMENT 'Almacena URLs de imágenes/videos en formato JSON'
AFTER campos_extra;

-- Crear tabla de auditoría para historial de cambios
CREATE TABLE IF NOT EXISTS auditoria_cambios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    tabla_afectada VARCHAR(100) NOT NULL,
    id_registro INT NOT NULL,
    accion VARCHAR(50) NOT NULL,
    valores_anteriores JSON,
    valores_nuevos JSON,
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuario_fecha (usuario_id, fecha_cambio),
    INDEX idx_tabla_accion (tabla_afectada, accion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear carpeta de uploads si es necesaria (se hace por PHP, esto es solo documentación)
-- Sistema de archivos necesario:
-- /public/uploads/
-- /public/uploads/imagenes/
-- /public/uploads/videos/
-- /public/uploads/audios/
