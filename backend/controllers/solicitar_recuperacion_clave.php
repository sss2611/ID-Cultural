<?php
/**
 * solicitar_recuperacion_clave.php
 * Maneja solicitud de recuperación de contraseña
 */

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../helpers/EmailHelper.php';

header('Content-Type: application/json');

$email = strtolower(trim($_POST['email'] ?? ''));

if (!$email) {
    http_response_code(400);
    echo json_encode(['error' => 'Email requerido']);
    exit;
}

try {
    // Buscar usuario
    $stmt = $pdo->prepare("SELECT id, nombre, apellido, email FROM artistas WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        // Por seguridad, no indicar si el email existe o no
        http_response_code(200);
        echo json_encode(['success' => true, 'mensaje' => 'Si el email existe en nuestros registros, recibirás un enlace de recuperación.']);
        exit;
    }
    
    // Generar token único
    $token = bin2hex(random_bytes(32));
    $fecha_expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Guardar token en BD
    $stmt = $pdo->prepare("
        INSERT INTO password_reset_tokens (usuario_id, token, fecha_expiracion)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        token = VALUES(token),
        fecha_expiracion = VALUES(fecha_expiracion),
        usado = 0
    ");
    $stmt->execute([$usuario['id'], $token, $fecha_expiracion]);
    
    // Enviar email
    /** @var EmailHelper $emailHelper */
    $emailHelper = new EmailHelper();
    $nombreCompleto = $usuario['nombre'] . ' ' . $usuario['apellido'];
    $enviado = $emailHelper->enviarRecuperacionClave($usuario['email'], $nombreCompleto, $token);
    
    if ($enviado) {
        http_response_code(200);
        echo json_encode(['success' => true, 'mensaje' => 'Si el email existe en nuestros registros, recibirás un enlace de recuperación.']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al enviar email']);
    }
    
} catch (Exception $e) {
    error_log("Error en recuperación: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error del servidor']);
}
