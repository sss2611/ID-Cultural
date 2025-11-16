<?php
/**
 * API para obtener publicaciones según estado
 * Archivo: /public/api/get_publicaciones.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../backend/config/connection.php';

// Obtener el filtro de estado desde la URL (ej: ?estado=pendiente)
$estado_filter = $_GET['estado'] ?? null;
$categoria_filter = $_GET['categoria'] ?? null;
$municipio_filter = $_GET['municipio'] ?? null;

try {
    // Consulta mejorada que une 'publicaciones' con 'artistas'
    $sql = "
        SELECT 
            p.id,
            p.titulo,
            p.descripcion,
            p.categoria,
            p.estado,
            p.fecha_envio_validacion,
            p.fecha_creacion,
            a.id AS usuario_id,
            CONCAT(a.nombre, ' ', a.apellido) AS artista_nombre,
            a.municipio,
            a.provincia,
            a.email AS artista_email,
            a.status,
            CASE 
                WHEN a.status = 'validado' THEN TRUE 
                ELSE FALSE 
            END AS es_artista_validado
        FROM publicaciones p
        INNER JOIN artistas a ON p.usuario_id = a.id
        WHERE 1=1
    ";
    
    $params = [];

    // Aplicar filtros
    if ($estado_filter) {
        // Si busca 'pendiente', incluir también 'pendiente_validacion' (normalización)
        if ($estado_filter === 'pendiente') {
            $sql .= " AND p.estado IN (?, ?)";
            $params[] = 'pendiente';
            $params[] = 'pendiente_validacion';
        } else {
            $sql .= " AND p.estado = ?";
            $params[] = $estado_filter;
        }
    }

    if ($categoria_filter) {
        $sql .= " AND p.categoria = ?";
        $params[] = $categoria_filter;
    }

    if ($municipio_filter) {
        $sql .= " AND a.municipio = ?";
        $params[] = $municipio_filter;
    }

    $sql .= " ORDER BY p.fecha_envio_validacion DESC, p.fecha_creacion DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    $publicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Convertir el campo booleano a true/false para JSON
    foreach ($publicaciones as &$pub) {
        $pub['es_artista_validado'] = (bool)$pub['es_artista_validado'];
    }
    
    echo json_encode($publicaciones);

} catch (PDOException $e) {
    error_log("Error en get_publicaciones.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al consultar la base de datos.']);
}