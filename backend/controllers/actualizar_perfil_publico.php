<?php
/**
 * actualizar_perfil_publico.php
 * Controller para actualizar perfil público del artista (con validación)
 */

// Limpiar cualquier salida anterior si existe buffer
if (ob_get_length()) {
    ob_clean();
}

// Configurar error handling para que no muestre errores en pantalla
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=UTF-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../helpers/MultimediaValidator.php';

if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['role'] !== 'artista') {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$usuario_id = $_SESSION['user_data']['id'];

// Preparar datos
$biografia = $_POST['biografia'] ?? '';
$especialidades = $_POST['especialidades'] ?? '';
$instagram = $_POST['instagram'] ?? '';
$facebook = $_POST['facebook'] ?? '';
$twitter = $_POST['twitter'] ?? '';
$sitio_web = $_POST['sitio_web'] ?? '';
$foto_perfil = $_FILES['foto_perfil'] ?? null;

$foto_ruta = null;

// Procesar foto si existe
if ($foto_perfil && $foto_perfil['error'] === UPLOAD_ERR_OK) {
    try {
        $validator = new MultimediaValidator();
        $resultado_guardado = $validator->guardarArchivo($foto_perfil, 'imagen');
        
        if (!$resultado_guardado['exitoso']) {
            http_response_code(400);
            echo json_encode(['error' => $resultado_guardado['mensaje']], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        $foto_ruta = $resultado_guardado['ruta'];
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

try {
    // Actualizar directamente el perfil público en la tabla artistas
    $sql = "UPDATE artistas SET ";
    $campos = [];
    $valores = [];
    
    $campos[] = "biografia = ?";
    $valores[] = $biografia;
    
    $campos[] = "especialidades = ?";
    $valores[] = $especialidades;
    
    $campos[] = "instagram = ?";
    $valores[] = $instagram;
    
    $campos[] = "facebook = ?";
    $valores[] = $facebook;
    
    $campos[] = "twitter = ?";
    $valores[] = $twitter;
    
    $campos[] = "sitio_web = ?";
    $valores[] = $sitio_web;
    
    // Cambiar estado a pendiente de validación
    $campos[] = "status_perfil = ?";
    $valores[] = 'pendiente';
    
    if ($foto_ruta) {
        $campos[] = "foto_perfil = ?";
        $valores[] = $foto_ruta;
    }
    
    $sql .= implode(", ", $campos) . " WHERE id = ?";
    $valores[] = $usuario_id;
    
    $stmt = $pdo->prepare($sql);
    $resultado = $stmt->execute($valores);

    if ($resultado) {
        http_response_code(200);
        echo json_encode([
            'success' => true, 
            'mensaje' => 'Tu perfil público ha sido actualizado correctamente. Está en revisión del equipo de validación.'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'No se pudo procesar la solicitud'], JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    error_log("Error al actualizar perfil público: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al procesar la solicitud: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
