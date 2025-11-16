<?php
/**
 * cambiar_clave_token.php
 * Valida token y permite cambio de contraseña
 */

require_once __DIR__ . '/../config/connection.php';

header('Content-Type: application/json');

$token = trim($_POST['token'] ?? '');
$nueva_clave = $_POST['nueva_clave'] ?? '';

if (!$token || !$nueva_clave || strlen($nueva_clave) < 6) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos inválidos. La contraseña debe tener mínimo 6 caracteres.']);
    exit;
}

try {
    // Buscar token válido
    $stmt = $pdo->prepare("
        SELECT prt.usuario_id, prt.fecha_expiracion, prt.usado
        FROM password_reset_tokens prt
        WHERE prt.token = ?
    ");
    $stmt->execute([$token]);
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$registro) {
        http_response_code(400);
        echo json_encode(['error' => 'Token inválido']);
        exit;
    }
    
    if ($registro['usado'] == 1) {
        http_response_code(400);
        echo json_encode(['error' => 'Este token ya fue utilizado']);
        exit;
    }
    
    if (strtotime($registro['fecha_expiracion']) < time()) {
        http_response_code(400);
        echo json_encode(['error' => 'El token ha expirado. Solicita uno nuevo.']);
        exit;
    }
    
    // Actualizar contraseña
    $hashedPassword = password_hash($nueva_clave, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE artistas SET password = ? WHERE id = ?");
    $stmt->execute([$hashedPassword, $registro['usuario_id']]);
    
    // Marcar token como usado
    $stmt = $pdo->prepare("UPDATE password_reset_tokens SET usado = 1 WHERE token = ?");
    $stmt->execute([$token]);
    
    http_response_code(200);
    echo json_encode(['success' => true, 'mensaje' => 'Contraseña actualizada exitosamente']);
    
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error del servidor']);
}
