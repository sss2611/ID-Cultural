<?php
/**
 * actualizar_datos_contacto.php
 * Controller para actualizar datos de contacto del artista
 */
session_start();
require_once __DIR__ . '/../config/connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['role'] !== 'artista') {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$usuario_id = $_SESSION['user_data']['id'];
$datos = json_decode(file_get_contents('php://input'), true);

if (!$datos) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos inválidos']);
    exit;
}

$campos_requeridos = ['nombre', 'apellido', 'fecha_nacimiento', 'genero', 'pais', 'provincia', 'municipio'];
foreach ($campos_requeridos as $campo) {
    if (empty($datos[$campo])) {
        http_response_code(400);
        echo json_encode(['error' => "El campo '{$campo}' es requerido"]);
        exit;
    }
}

try {
    $stmt = $pdo->prepare("
        UPDATE artistas 
        SET 
            nombre = ?,
            apellido = ?,
            fecha_nacimiento = ?,
            genero = ?,
            pais = ?,
            provincia = ?,
            municipio = ?
        WHERE id = ?
    ");

    $resultado = $stmt->execute([
        $datos['nombre'],
        $datos['apellido'],
        $datos['fecha_nacimiento'],
        $datos['genero'],
        $datos['pais'],
        $datos['provincia'],
        $datos['municipio'],
        $usuario_id
    ]);

    if ($resultado && $stmt->rowCount() > 0) {
        $_SESSION['user_data']['nombre'] = $datos['nombre'];
        $_SESSION['user_data']['apellido'] = $datos['apellido'];

        http_response_code(200);
        echo json_encode(['success' => true, 'mensaje' => 'Datos de contacto actualizados correctamente']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'No se realizaron cambios']);
    }
} catch (Exception $e) {
    error_log("Error al actualizar datos: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al actualizar los datos']);
}
