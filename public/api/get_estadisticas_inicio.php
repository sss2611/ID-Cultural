<?php
/**
 * API para obtener estadísticas de la página de inicio
 * Archivo: /public/api/get_estadisticas_inicio.php
 * VERSIÓN COMPATIBLE CON BD ACTUAL (artistas + users separados)
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../backend/config/connection.php';

try {
    // Contar artistas validados (de la tabla artistas)
    $stmt_artistas = $pdo->prepare("
        SELECT COUNT(*) as total
        FROM artistas
        WHERE status = 'validado'
    ");
    $stmt_artistas->execute();
    $artistas = $stmt_artistas->fetchColumn();

    // Contar obras validadas (de la tabla publicaciones)
    $stmt_obras = $pdo->prepare("
        SELECT COUNT(*) as total
        FROM publicaciones
        WHERE estado = 'validado'
    ");
    $stmt_obras->execute();
    $obras = $stmt_obras->fetchColumn();

    // Contar noticias
    $stmt_noticias = $pdo->prepare("
        SELECT COUNT(*) as total
        FROM noticias
    ");
    $stmt_noticias->execute();
    $noticias = $stmt_noticias->fetchColumn();

    // Devolver estadísticas
    echo json_encode([
        'status' => 'ok',
        'artistas' => (int)$artistas,
        'obras' => (int)$obras,
        'noticias' => (int)$noticias
    ]);

} catch (PDOException $e) {
    error_log("Error en get_estadisticas_inicio.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Error al consultar la base de datos: ' . $e->getMessage(),
        'artistas' => 0,
        'obras' => 0,
        'noticias' => 0
    ]);
}