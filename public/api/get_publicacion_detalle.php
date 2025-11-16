<?php
/**
 * API para obtener detalle completo de una publicación
 * Archivo: /public/api/get_publicacion_detalle.php
 */

session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../backend/config/connection.php';

$publicacion_id = $_GET['id'] ?? null;

if (empty($publicacion_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de publicación requerido.']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            p.id,
            p.titulo,
            p.descripcion,
            p.categoria,
            p.campos_extra,
            p.multimedia,
            p.estado,
            p.fecha_creacion,
            p.fecha_envio_validacion,
            p.fecha_validacion,
            a.id AS usuario_id,
            CONCAT(a.nombre, ' ', a.apellido) AS artista_nombre,
            a.email AS artista_email,
            a.municipio,
            a.provincia,
            a.status,
            CASE 
                WHEN a.status = 'validado' THEN TRUE 
                ELSE FALSE 
            END AS es_artista_validado,
            COALESCE(v.nombre, 'Sistema') AS validador_nombre
        FROM publicaciones p
        INNER JOIN artistas a ON p.usuario_id = a.id
        LEFT JOIN users v ON p.validador_id = v.id
        WHERE p.id = ?
    ");
    
    $stmt->execute([$publicacion_id]);
    $publicacion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$publicacion) {
        http_response_code(404);
        echo json_encode(['error' => 'Publicación no encontrada.']);
        exit;
    }

    // Verificar permisos de acceso
    $user_data = $_SESSION['user_data'] ?? null;
    
    if ($user_data) {
        $puede_ver = (
            $publicacion['usuario_id'] == $user_data['id'] || // Es el dueño
            in_array($user_data['role'], ['validador', 'admin', 'editor']) || // Es staff
            $publicacion['estado'] == 'validado' // Es público
        );
    } else {
        // Si no está logueado, solo puede ver publicaciones validadas
        $puede_ver = ($publicacion['estado'] === 'validado');
    }
    
    if (!$puede_ver) {
        http_response_code(403);
        echo json_encode(['error' => 'No tienes permiso para ver esta publicación.']);
        exit;
    }

    // Convertir campos JSON a arrays
    $publicacion['campos_extra'] = $publicacion['campos_extra'] ? json_decode($publicacion['campos_extra'], true) : null;
    $publicacion['multimedia'] = $publicacion['multimedia'] ? json_decode($publicacion['multimedia'], true) : null;
    $publicacion['es_artista_validado'] = (bool)$publicacion['es_artista_validado'];

    echo json_encode($publicacion);

} catch (PDOException $e) {
    error_log("Error en get_publicacion_detalle.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al consultar la base de datos.']);
}