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

try {
    switch ($action) {
        case 'get':
            // OBTENER BORRADORES (solo artista)
            checkArtistaPermissions();
            
            $artista_id = $_SESSION['user_data']['id'];
            $id = $_GET['id'] ?? 0;

            if ($id) {
                // Obtener borrador específico
                $stmt = $pdo->prepare(
                    "SELECT id, titulo, descripcion, categoria, campos_extra, fecha_creacion 
                     FROM publicaciones 
                     WHERE id = ? AND usuario_id = ? AND estado = 'borrador'"
                );
                $stmt->execute([$id, $artista_id]);
                $borrador = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($borrador) {
                    // Decodificar campos extra si existen
                    if ($borrador['campos_extra']) {
                        $borrador['campos_extra'] = json_decode($borrador['campos_extra'], true);
                    } else {
                        $borrador['campos_extra'] = [];
                    }
                    echo json_encode($borrador);
                } else {
                    http_response_code(404);
                    echo json_encode(['status' => 'error', 'message' => 'Borrador no encontrado.']);
                }
            } else {
                // Obtener todos los borradores del artista
                $stmt = $pdo->prepare(
                    "SELECT id, titulo, fecha_creacion 
                     FROM publicaciones 
                     WHERE usuario_id = ? AND estado = 'borrador'
                     ORDER BY fecha_creacion DESC"
                );
                
                $stmt->execute([$artista_id]);
                $borradores = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode($borradores);
            }
            break;

        case 'save':
            // GUARDAR BORRADOR (solo artista)
            checkArtistaPermissions();
            
            $usuario_id = $_SESSION['user_data']['id'];
            $id = $_POST['id'] ?? 0; // Para actualización
            $titulo = trim($_POST['titulo'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $categoria = trim($_POST['categoria'] ?? '');
            $estado = trim($_POST['estado'] ?? 'borrador'); // 'borrador' o 'pendiente_validacion'

            // Validaciones básicas
            if (empty($titulo) || empty($descripcion) || empty($categoria)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'El título, la descripción y la categoría son obligatorios.']);
                exit;
            }

            // Recopilar campos extra
            $campos_extra = [];
            foreach ($_POST as $key => $value) {
                if (!in_array($key, ['id', 'titulo', 'descripcion', 'categoria', 'estado'])) {
                    $campos_extra[$key] = trim($value);
                }
            }
            $campos_extra_json = json_encode($campos_extra);

            // Si se envía a validación, actualizamos la fecha de envío
            $fecha_envio = ($estado === 'pendiente_validacion') ? date('Y-m-d H:i:s') : null;

            $pdo->beginTransaction();

            try {
                if ($id) {
                    // ACTUALIZAR borrador existente
                    $stmt = $pdo->prepare(
                        "UPDATE publicaciones 
                         SET titulo = ?, descripcion = ?, categoria = ?, campos_extra = ?, estado = ?, fecha_envio_validacion = ?
                         WHERE id = ? AND usuario_id = ? AND estado = 'borrador'"
                    );
                    $stmt->execute([$titulo, $descripcion, $categoria, $campos_extra_json, $estado, $fecha_envio, $id, $usuario_id]);
                    
                    $message = ($estado === 'pendiente_validacion') 
                        ? 'Publicación enviada a validación con éxito.' 
                        : 'Borrador actualizado con éxito.';
                } else {
                    // CREAR nuevo borrador
                    $stmt = $pdo->prepare(
                        "INSERT INTO publicaciones (usuario_id, titulo, descripcion, categoria, campos_extra, estado, fecha_envio_validacion) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)"
                    );
                    $stmt->execute([$usuario_id, $titulo, $descripcion, $categoria, $campos_extra_json, $estado, $fecha_envio]);
                    
                    $message = ($estado === 'pendiente_validacion') 
                        ? 'Publicación creada y enviada a validación con éxito.' 
                        : 'Borrador guardado con éxito.';
                }

                // Si se envía a validación, actualizar estado del artista si es necesario
                if ($estado === 'pendiente_validacion') {
                    $update_artista = $pdo->prepare("UPDATE artistas SET status = 'pendiente' WHERE id = ? AND status != 'validado'");
                    $update_artista->execute([$usuario_id]);
                }

                $pdo->commit();
                echo json_encode(['status' => 'ok', 'message' => $message]);

            } catch (PDOException $e) {
                $pdo->rollBack();
                throw $e;
            }
            break;

        case 'delete':
            // ELIMINAR BORRADOR (solo artista)
            checkArtistaPermissions();
            
            $id = $_POST['id'] ?? '';
            $artista_id = $_SESSION['user_data']['id'];

            if (empty($id)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'ID de borrador no proporcionado.']);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM publicaciones WHERE id = ? AND usuario_id = ? AND estado = 'borrador'");
            $stmt->execute([$id, $artista_id]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(['status' => 'ok', 'message' => 'Borrador eliminado con éxito.']);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'No se encontró el borrador a eliminar.']);
            }
            break;

        case 'submit':
            // ENVIAR BORRADOR A VALIDACIÓN (acción específica)
            checkArtistaPermissions();
            
            $id = $_POST['id'] ?? '';
            $artista_id = $_SESSION['user_data']['id'];

            if (empty($id)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'ID de borrador no proporcionado.']);
                exit;
            }

            $pdo->beginTransaction();

            try {
                // Actualizar estado a pendiente_validacion
                $stmt = $pdo->prepare(
                    "UPDATE publicaciones 
                     SET estado = 'pendiente_validacion', fecha_envio_validacion = CURRENT_TIMESTAMP 
                     WHERE id = ? AND usuario_id = ? AND estado = 'borrador'"
                );
                $stmt->execute([$id, $artista_id]);

                if ($stmt->rowCount() > 0) {
                    // Actualizar estado del artista si es necesario
                    $update_artista = $pdo->prepare("UPDATE artistas SET status = 'pendiente' WHERE id = ? AND status != 'validado'");
                    $update_artista->execute([$artista_id]);

                    $pdo->commit();
                    echo json_encode(['status' => 'ok', 'message' => 'Borrador enviado a validación con éxito.']);
                } else {
                    $pdo->rollBack();
                    echo json_encode(['status' => 'error', 'message' => 'No se pudo enviar el borrador a validación.']);
                }

            } catch (PDOException $e) {
                $pdo->rollBack();
                throw $e;
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Acción no válida. Acciones permitidas: get, save, delete, submit']);
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