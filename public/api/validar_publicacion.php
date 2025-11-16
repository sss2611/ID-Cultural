<?php
/**
 * API para validar o rechazar publicaciones
 * Archivo: /public/api/validar_publicacion.php
 */

session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../backend/config/connection.php';

// Verificar que el usuario sea validador o admin
if (!isset($_SESSION['user_data']) || !in_array($_SESSION['user_data']['role'], ['validador', 'admin'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Acceso no autorizado.']);
    exit;
}

$publicacion_id = $_POST['id'] ?? null;
$accion = $_POST['accion'] ?? null; // 'validar' o 'rechazar'
$motivo = trim($_POST['motivo'] ?? '');
$validador_id = $_SESSION['user_data']['id'];
// Construir nombre del validador desde sesión con fallback
$validador_nombre = trim(($_SESSION['user_data']['nombre'] ?? '') . ' ' . ($_SESSION['user_data']['apellido'] ?? ''));
$validador_nombre = !empty($validador_nombre) ? $validador_nombre : $_SESSION['user_data']['role'];

// Validaciones
if (empty($publicacion_id) || empty($accion)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos para procesar la solicitud.']);
    exit;
}

if (!in_array($accion, ['validar', 'rechazar'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Acción no válida.']);
    exit;
}

if ($accion === 'rechazar' && empty($motivo)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Debes proporcionar un motivo de rechazo.']);
    exit;
}

try {
    // Iniciar transacción
    $pdo->beginTransaction();

    // 1. Obtener información de la publicación y el artista
    $stmt = $pdo->prepare("
        SELECT p.usuario_id, a.status, a.nombre, a.apellido
        FROM publicaciones p
        INNER JOIN artistas a ON p.usuario_id = a.id
        WHERE p.id = ? AND p.estado IN ('pendiente', 'pendiente_validacion')
    ");
    $stmt->execute([$publicacion_id]);
    $publicacion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$publicacion) {
        $pdo->rollBack();
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Publicación no encontrada o ya fue procesada.']);
        exit;
    }

    $usuario_id = $publicacion['usuario_id'];
    $usuario_nombre = $publicacion['nombre'] . ' ' . $publicacion['apellido'];

    // 2. Actualizar el estado de la publicación
    if ($accion === 'validar') {
        $nuevo_estado = 'validado';
        $stmt = $pdo->prepare("
            UPDATE publicaciones 
            SET estado = ?, 
                validador_id = ?, 
                fecha_validacion = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$nuevo_estado, $validador_id, $publicacion_id]);

        // 3. Actualizar el status del artista a 'validado' si es su primera obra validada
        if ($publicacion['status'] !== 'validado') {
            $stmt = $pdo->prepare("UPDATE artistas SET status = 'validado' WHERE id = ?");
            $stmt->execute([$usuario_id]);
        }

        $mensaje = 'La publicación ha sido aprobada y ahora es visible en la Wiki de Artistas.';
        $log_action = 'VALIDACIÓN DE PUBLICACIÓN';
        $log_details = "Publicación ID: {$publicacion_id} del artista '{$usuario_nombre}' (ID: {$usuario_id}) ha sido validada.";

    } else { // rechazar
        $nuevo_estado = 'rechazado';
        $stmt = $pdo->prepare("
            UPDATE publicaciones 
            SET estado = ?, 
                validador_id = ?, 
                fecha_validacion = NOW(),
                motivo_rechazo = ?
            WHERE id = ?
        ");
        $stmt->execute([$nuevo_estado, $validador_id, $motivo, $publicacion_id]);

        $mensaje = 'La publicación ha sido rechazada. El artista será notificado.';
        $log_action = 'RECHAZO DE PUBLICACIÓN';
        $log_details = "Publicación ID: {$publicacion_id} del artista '{$usuario_nombre}' (ID: {$usuario_id}) ha sido rechazada. Motivo: {$motivo}";
    }

    // 4. Registrar en el log del sistema
    $stmt = $pdo->prepare("
        INSERT INTO system_logs (user_id, user_name, action, details)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $validador_id,
        $validador_nombre,
        $log_action,
        $log_details
    ]);

    // 5. Confirmar transacción
    $pdo->commit();

    echo json_encode([
        'status' => 'ok',
        'message' => $mensaje,
        'nuevo_estado' => $nuevo_estado
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Error en validar_publicacion.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos.']);
}