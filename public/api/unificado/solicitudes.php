<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../backend/config/connection.php';

// Obtener acción desde GET o POST
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Verificar permisos de artista
function checkArtistaPermissions() {
    if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['role'] !== 'artista') {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Acceso no autorizado.']);
        exit;
    }
}

// Verificar permisos de validador/admin
function checkValidadorPermissions() {
    if (!isset($_SESSION['user_data']) || !in_array($_SESSION['user_data']['role'], ['validador', 'admin'])) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'No tienes permiso para realizar esta acción.']);
        exit;
    }
}

try {
    switch ($action) {
        case 'get_my':
            // OBTENER MIS SOLICITUDES (solo artista)
            checkArtistaPermissions();
            
            $artista_id = $_SESSION['user_data']['id'];
            $id = $_GET['id'] ?? 0;

            if ($id) {
                // Obtener solicitud específica del artista
                $stmt = $pdo->prepare(
                    "SELECT id, titulo, fecha_envio_validacion, estado, contenido 
                     FROM publicaciones 
                     WHERE id = ? AND usuario_id = ? AND estado != 'borrador'"
                );
                $stmt->execute([$id, $artista_id]);
                $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($solicitud) {
                    echo json_encode($solicitud);
                } else {
                    http_response_code(404);
                    echo json_encode(['status' => 'error', 'message' => 'Solicitud no encontrada.']);
                }
            } else {
                // Obtener todas las solicitudes del artista
                $stmt = $pdo->prepare(
                    "SELECT id, titulo, fecha_envio_validacion, estado 
                     FROM publicaciones 
                     WHERE usuario_id = ? AND estado != 'borrador'
                     ORDER BY fecha_envio_validacion DESC"
                );
                
                $stmt->execute([$artista_id]);
                $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode($solicitudes);
            }
            break;

        case 'get_all':
            // OBTENER TODAS LAS SOLICITUDES PENDIENTES (solo validador/admin)
            checkValidadorPermissions();
            
            $id = $_GET['id'] ?? 0;

            if ($id) {
                // Obtener solicitud específica con detalles completos
                $stmt = $pdo->prepare("
                    SELECT 
                        p.id AS publicacion_id, 
                        p.titulo, 
                        p.contenido,
                        p.fecha_envio_validacion,
                        p.estado,
                        a.id AS artista_id,
                        CONCAT(a.nombre, ' ', a.apellido) AS nombre_artista,
                        a.municipio,
                        a.email,
                        a.provincia
                    FROM publicaciones p
                    JOIN artistas a ON p.usuario_id = a.id
                    WHERE p.id = ?
                ");
                $stmt->execute([$id]);
                $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($solicitud) {
                    echo json_encode($solicitud);
                } else {
                    http_response_code(404);
                    echo json_encode(['status' => 'error', 'message' => 'Solicitud no encontrada.']);
                }
            } else {
                // Obtener todas las solicitudes pendientes
                $estado = $_GET['estado'] ?? 'pendiente_validacion';
                $sql_where = "WHERE p.estado = ?";
                $params = [$estado];

                // Si se solicita un estado específico o todos
                if ($estado === 'all') {
                    $sql_where = "WHERE p.estado != 'borrador'";
                    $params = [];
                }

                $stmt = $pdo->prepare("
                    SELECT 
                        p.id AS publicacion_id, 
                        p.titulo, 
                        p.fecha_envio_validacion,
                        p.estado,
                        a.id AS artista_id,
                        CONCAT(a.nombre, ' ', a.apellido) AS nombre_artista,
                        a.municipio
                    FROM publicaciones p
                    JOIN artistas a ON p.usuario_id = a.id
                    $sql_where
                    ORDER BY p.fecha_envio_validacion ASC
                ");
                
                $stmt->execute($params);
                $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode($solicitudes);
            }
            break;

        case 'update':
            // ACTUALIZAR ESTADO DE SOLICITUD (solo validador/admin)
            checkValidadorPermissions();
            
            $id = $_POST['id'] ?? '';
            $nuevo_estado = $_POST['estado'] ?? '';
            $motivo = trim($_POST['motivo'] ?? '');
            $validador_id = $_SESSION['user_data']['id'] ?? null;
            $validador_nombre = $_SESSION['user_data']['nombre'] ?? 'Usuario Desconocido';

            // Validaciones
            if (empty($id) || empty($nuevo_estado) || empty($validador_id)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Faltan datos para procesar la solicitud.']);
                exit;
            }
            
            if (!in_array($nuevo_estado, ['validado', 'rechazado'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Estado no válido.']);
                exit;
            }

            $pdo->beginTransaction();

            try {
                // 1. Actualizar el estado de la publicación
                $stmt = $pdo->prepare("
                    UPDATE publicaciones 
                    SET estado = ?, validador_id = ?, fecha_validacion = CURRENT_TIMESTAMP 
                    WHERE id = ? AND estado = 'pendiente_validacion'
                ");
                $stmt->execute([$nuevo_estado, $validador_id, $id]);

                if ($stmt->rowCount() > 0) {
                    // 2. Crear el registro en el log del sistema
                    $action_log = ($nuevo_estado == 'validado') ? 'VALIDACIÓN DE PUBLICACIÓN' : 'RECHAZO DE PUBLICACIÓN';
                    $details = "Se ha {$nuevo_estado} la solicitud con ID: {$id}.";
                    if (!empty($motivo)) {
                        $details .= ($nuevo_estado == 'validado') ? " Comentario: {$motivo}" : " Motivo: {$motivo}";
                    }

                    $log_stmt = $pdo->prepare("INSERT INTO system_logs (user_id, user_name, action, details) VALUES (?, ?, ?, ?)");
                    $log_stmt->execute([$validador_id, $validador_nombre, $action_log, $details]);

                    $pdo->commit();
                    echo json_encode(['status' => 'ok', 'message' => 'El estado de la solicitud ha sido actualizado.']);

                } else {
                    $pdo->rollBack();
                    echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar la solicitud o ya fue procesada.']);
                }

            } catch (PDOException $e) {
                $pdo->rollBack();
                throw $e;
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Acción no válida. Acciones permitidas: get_my, get_all, update']);
            break;
    }

} catch (PDOException $e) {
    // Rollback si hay alguna transacción activa
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>