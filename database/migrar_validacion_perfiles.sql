-- ========================================
-- MIGRACIÓN: Validación de Perfiles de Artistas
-- Fecha: 8 de noviembre de 2025
-- ========================================

-- 1. Agregar campo de estado del perfil
ALTER TABLE artistas ADD COLUMN IF NOT EXISTS status_perfil VARCHAR(20) DEFAULT 'pendiente';

-- 2. Agregar campo para motivo de rechazo
ALTER TABLE artistas ADD COLUMN IF NOT EXISTS motivo_rechazo TEXT NULL;

-- 3. Agregar índice para búsquedas rápidas de perfiles pendientes
CREATE INDEX IF NOT EXISTS idx_status_perfil ON artistas(status_perfil);

-- 4. Agregar índice combinado para filtros comunes
CREATE INDEX IF NOT EXISTS idx_status_provincia ON artistas(status_perfil, provincia);

-- 5. Crear tabla de logs de validación de perfiles (opcional pero recomendado)
CREATE TABLE IF NOT EXISTS logs_validacion_perfiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    artista_id INT NOT NULL,
    validador_id INT NOT NULL,
    accion VARCHAR(20) NOT NULL, -- 'validar', 'rechazar', 'editar'
    motivo_rechazo TEXT NULL,
    fecha_accion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    detalles JSON NULL,
    FOREIGN KEY (artista_id) REFERENCES artistas(id) ON DELETE CASCADE,
    FOREIGN KEY (validador_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_artista_id (artista_id),
    INDEX idx_validador_id (validador_id),
    INDEX idx_fecha_accion (fecha_accion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Establecer valores iniciales para artistas existentes
-- Si ya tienen foto_perfil o biografia, marcar como 'validado'
-- Si no tienen nada, dejar como 'pendiente'
UPDATE artistas 
SET status_perfil = 'validado' 
WHERE (foto_perfil IS NOT NULL AND foto_perfil != '') 
   OR (biografia IS NOT NULL AND biografia != '');

-- Mostrar resultado
SELECT 'Migración completada' AS estado;
SELECT 
    status_perfil, 
    COUNT(*) as cantidad 
FROM artistas 
GROUP BY status_perfil;
