<?php
/**
 * validar_perfil.php
 * Endpoint para validar o rechazar perfiles de artistas
 * Solo accesible por validadores y admin
 */

// Evitar que se muestren warnings/notices que rompan el JSON
ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../backend/config/connection.php';
require_once __DIR__ . '/../../backend/helpers/EmailHelper.php';

header('Content-Type: application/json; charset=UTF-8');

// ============================================
// 1. VERIFICACIÓN DE SEGURIDAD
// ============================================

if (!isset($_SESSION['user_data']) || !in_array($_SESSION['user_data']['role'], ['validador', 'admin'])) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'No tienes permisos para validar perfiles'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$validador_id = $_SESSION['user_data']['id'];
$validador_nombre = trim(($_SESSION['user_data']['nombre'] ?? '') . ' ' . ($_SESSION['user_data']['apellido'] ?? ''));
if (empty($validador_nombre)) {
    $validador_nombre = $_SESSION['user_data']['nombre'] ?? 'Sistema';
}

// ============================================
// 2. OBTENCIÓN Y VALIDACIÓN DE DATOS
// ============================================

$artista_id = $_POST['id'] ?? null;
$accion = $_POST['accion'] ?? null; // 'validar' o 'rechazar'
$motivo = $_POST['motivo'] ?? null;

if (!$artista_id || !is_numeric($artista_id)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'ID de artista inválido'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!in_array($accion, ['validar', 'rechazar'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Acción inválida. Use "validar" o "rechazar"'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($accion === 'rechazar' && empty($motivo)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Debe proporcionar un motivo de rechazo'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ============================================
// 3. OPERACIONES EN BD
// ============================================

try {
    // Obtener datos del artista antes de actualizar
    $stmt_get = $pdo->prepare("SELECT id, nombre, apellido, email FROM artistas WHERE id = ?");
    $stmt_get->execute([$artista_id]);
    $artista = $stmt_get->fetch(PDO::FETCH_ASSOC);

    if (!$artista) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'No se encontró el artista especificado'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Iniciar transacción
    $pdo->beginTransaction();

    try {
        // Actualizar estado del perfil
        if ($accion === 'validar') {
            $nuevo_estado = 'validado';
            $stmt_update = $pdo->prepare("
                UPDATE artistas 
                SET status_perfil = ?, 
                    motivo_rechazo = NULL
                WHERE id = ?
            ");
            $stmt_update->execute([$nuevo_estado, $artista_id]);

        } else { // rechazar
            $nuevo_estado = 'rechazado';
            $stmt_update = $pdo->prepare("
                UPDATE artistas 
                SET status_perfil = ?, 
                    motivo_rechazo = ?
                WHERE id = ?
            ");
            $stmt_update->execute([$nuevo_estado, $motivo, $artista_id]);
        }

        // Registrar en log de validaciones
        $stmt_log = $pdo->prepare("
            INSERT INTO logs_validacion_perfiles 
            (artista_id, validador_id, accion, motivo_rechazo, fecha_accion)
            VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
        ");
        
        $motivo_log = ($accion === 'rechazar') ? $motivo : null;
        $stmt_log->execute([$artista_id, $validador_id, $accion, $motivo_log]);

        // Enviar email de notificación
        try {
            $emailHelper = new EmailHelper();
            $nombreCompleto = $artista['nombre'] . ' ' . $artista['apellido'];

            if ($accion === 'validar') {
                $emailHelper->notificarPerfilValidado($artista['email'], $nombreCompleto);
            } else {
                $emailHelper->notificarPerfilRechazado($artista['email'], $nombreCompleto, $motivo);
            }
        } catch (Exception $e) {
            error_log("Error enviando email: " . $e->getMessage());
            // Continuar aunque falle el email
        }

        // Confirmar transacción
        $pdo->commit();

        // Respuesta exitosa
        http_response_code(200);
        echo json_encode([
            'status' => 'ok',
            'message' => $accion === 'validar' 
                ? 'Perfil de artista validado exitosamente'
                : 'Perfil de artista rechazado. Se notificó al artista.',
            'artista_id' => $artista_id,
            'nuevo_estado' => $nuevo_estado
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    error_log("Error al validar perfil: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Error al procesar la solicitud',
        'debug' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

?>
