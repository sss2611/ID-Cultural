-- database/optimizacion_indices.sql
-- Optimización de Base de Datos - Agregar Índices para Performance

-- ============================================================
-- ÍNDICES PARA BÚSQUEDA Y FILTRADO
-- ============================================================

-- Índices en tabla artistas (búsqueda frecuente)
CREATE INDEX idx_artistas_nombre ON artistas(nombre);
CREATE INDEX idx_artistas_municipio ON artistas(municipio);
CREATE INDEX idx_artistas_estado ON artistas(estado);
CREATE INDEX idx_artistas_email ON artistas(email);
CREATE INDEX idx_artistas_fecha_creacion ON artistas(fecha_creacion);

-- Índices en tabla usuarios (login y búsqueda)
CREATE INDEX idx_usuarios_email ON users(email);
CREATE INDEX idx_usuarios_role ON users(role);
CREATE INDEX idx_usuarios_estado ON users(estado);

-- Índices en tabla publicaciones (búsqueda por artista)
CREATE INDEX idx_publicaciones_artista_id ON publicaciones(artista_id);
CREATE INDEX idx_publicaciones_estado ON publicaciones(estado);
CREATE INDEX idx_publicaciones_categoria ON publicaciones(categoria);
CREATE INDEX idx_publicaciones_fecha ON publicaciones(fecha_creacion);

-- Índices en tabla intereses_artista (relaciones)
CREATE INDEX idx_intereses_artista_id ON intereses_artista(artista_id);
CREATE INDEX idx_intereses_interes ON intereses_artista(interes);

-- ============================================================
-- ÍNDICES COMPUESTOS (para queries frecuentes)
-- ============================================================

-- Búsqueda de artistas por municipio y estado
CREATE INDEX idx_artistas_municipio_estado ON artistas(municipio, estado);

-- Publicaciones por artista y estado
CREATE INDEX idx_publicaciones_artista_estado ON publicaciones(artista_id, estado);

-- ============================================================
-- ÍNDICES PARA RELACIONES
-- ============================================================

-- Password reset tokens
CREATE INDEX idx_password_reset_tokens_user_id ON password_reset_tokens(user_id);
CREATE INDEX idx_password_reset_tokens_token ON password_reset_tokens(token);

-- Logs de validación
CREATE INDEX idx_logs_validacion_artista_id ON logs_validacion_perfiles(artista_id);
CREATE INDEX idx_logs_validacion_validador_id ON logs_validacion_perfiles(validador_id);
CREATE INDEX idx_logs_validacion_fecha ON logs_validacion_perfiles(fecha);

-- System logs
CREATE INDEX idx_system_logs_accion ON system_logs(accion);
CREATE INDEX idx_system_logs_fecha ON system_logs(fecha_creacion);

-- ============================================================
-- ÍNDICES PARA NOTIFICACIONES (cuando se implemente)
-- ============================================================

CREATE INDEX idx_notificaciones_usuario_id ON notificaciones(usuario_id) IF NOT EXISTS;
CREATE INDEX idx_notificaciones_leida ON notificaciones(leida) IF NOT EXISTS;
CREATE INDEX idx_notificaciones_fecha ON notificaciones(fecha_creacion) IF NOT EXISTS;

-- ============================================================
-- FULLTEXT SEARCH (para búsquedas avanzadas)
-- ============================================================

-- Búsqueda de texto completo en artistas
ALTER TABLE artistas ADD FULLTEXT INDEX ft_artistas_nombre_bio (nombre, biografia);

-- Búsqueda de texto completo en publicaciones
ALTER TABLE publicaciones ADD FULLTEXT INDEX ft_publicaciones_titulo_desc (titulo, descripcion);

-- ============================================================
-- VERIFICAR ÍNDICES CREADOS
-- ============================================================

-- Para verificar los índices:
-- SHOW INDEXES FROM artistas;
-- SHOW INDEXES FROM publicaciones;
-- SHOW INDEXES FROM users;

-- ============================================================
-- OPTIMIZACIÓN DE TABLAS
-- ============================================================

-- Ejecutar después de cambios importantes
OPTIMIZE TABLE artistas;
OPTIMIZE TABLE publicaciones;
OPTIMIZE TABLE users;
OPTIMIZE TABLE intereses_artista;
OPTIMIZE TABLE logs_validacion_perfiles;
OPTIMIZE TABLE password_reset_tokens;
OPTIMIZE TABLE system_logs;
