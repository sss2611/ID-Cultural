<?php
require_once __DIR__ . '/../config/connection.php'; // Incluye config y la conexión
require_once __DIR__ . '/../helpers/EmailHelper.php';

header('Content-Type: application/json');

// 1. Verificación de seguridad: ¿Quien hace la petición es un validador o admin?
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'validador'])) {
    echo json_encode(['status' => 'error', 'message' => 'Acceso no autorizado.']);
    exit();
}

$id_artista = $_POST['id'] ?? null;
$nuevo_estado = $_POST['estado'] ?? null;
$estado_anterior = '';

if (!$id_artista || !$nuevo_estado) {
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos para la actualización.']);
    exit();
}

try {
    // Obtener datos de la obra y artista
    $stmt = $pdo->prepare("
        SELECT p.id, p.titulo, a.email, CONCAT(a.nombre, ' ', a.apellido) as artista_nombre
        FROM publicaciones p
        INNER JOIN artistas a ON p.usuario_id = a.id
        WHERE p.id = ?
    ");
    $stmt->execute([$id_artista]);
    $obra = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$obra) {
        echo json_encode(['status' => 'error', 'message' => 'No se encontró la obra.']);
        exit();
    }

    // 2. Preparamos la actualización
    $fecha_validacion = ($nuevo_estado === 'publicada' || $nuevo_estado === 'rechazada') ? date('Y-m-d H:i:s') : null;

    $updateStmt = $pdo->prepare("UPDATE publicaciones SET estado = ?, fecha_validacion = ? WHERE id = ?");
    $success = $updateStmt->execute([$nuevo_estado, $fecha_validacion, $id_artista]);

    if ($success) {
        // Enviar notificación por email
        try {
            /** @var EmailHelper $emailHelper */
            $emailHelper = new EmailHelper();
            
            if ($nuevo_estado === 'publicada') {
                // Obra aprobada
                $emailHelper->notificarObraAprobada(
                    $obra['email'],
                    $obra['artista_nombre'],
                    $obra['titulo']
                );
            } elseif ($nuevo_estado === 'rechazada') {
                // Obra rechazada (sin motivo específico aquí, se podría agregar)
                $emailHelper->notificarObraRechazada(
                    $obra['email'],
                    $obra['artista_nombre'],
                    $obra['titulo'],
                    'Revisar los criterios de publicación'
                );
            }
        } catch (Exception $e) {
            error_log("Error enviando email: " . $e->getMessage());
            // Continuar aunque falle el email
        }
        
        echo json_encode([
            'status' => 'ok',
            'message' => 'Estado actualizado correctamente.',
            'fechaValidacion' => $fecha_validacion ? date('Y-m-d', strtotime($fecha_validacion)) : '-'
        ]);
    } else {
        throw new Exception("No se pudo actualizar la base de datos.");
    }

} catch (Exception $e) {
    error_log("Error al actualizar estado: " . $e->getMessage());
    echo json_encode([
        'status' => 'error', 
        'message' => 'Error del servidor al actualizar el estado.',
        'estadoAnterior' => $estado_anterior
    ]);
}
?>