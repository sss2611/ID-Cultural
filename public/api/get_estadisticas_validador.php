<?php
/**
 * API para obtener estadísticas del validador
 * Archivo: /public/api/get_estadisticas_validador.php
 */

session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../backend/config/connection.php';

// Verificar que el usuario sea validador o admin
if (!isset($_SESSION['user_data']) || !in_array($_SESSION['user_data']['role'], ['validador', 'admin'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

try {
    // Obtener estadísticas de publicaciones por estado
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(CASE WHEN estado = 'pendiente' THEN 1 END) as pendientes,
            COUNT(CASE WHEN estado = 'validado' THEN 1 END) as validados,
            COUNT(CASE WHEN estado = 'rechazado' THEN 1 END) as rechazados,
            COUNT(CASE WHEN estado = 'borrador' THEN 1 END) as borradores
        FROM publicaciones
    ");
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Obtener conteo de artistas validados (usuarios con al menos 1 obra validada)
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT usuario_id) as total_artistas_validados
        FROM publicaciones
        WHERE estado = 'validado'
    ");
    $stmt->execute();
    $artistas = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Convertir a enteros
    $estadisticas = [
        'pendientes' => (int)$stats['pendientes'],
        'validados' => (int)$stats['validados'],
        'rechazados' => (int)$stats['rechazados'],
        'borradores' => (int)$stats['borradores'],
        'total_artistas_validados' => (int)$artistas['total_artistas_validados']
    ];
    
    echo json_encode($estadisticas);

} catch (PDOException $e) {
    error_log("Error en get_estadisticas_validador.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
}