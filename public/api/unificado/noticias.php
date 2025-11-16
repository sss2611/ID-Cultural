<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../backend/config/connection.php';

// Obtener acción desde GET o POST
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Verificar permisos para acciones que requieren autenticación
function checkPermissions($allowed_roles = ['editor', 'admin']) {
    if (!isset($_SESSION['user_data']) || !in_array($_SESSION['user_data']['role'], $allowed_roles)) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'No tienes permiso para realizar esta acción.']);
        exit;
    }
}

// Función para subir imagen
function uploadNoticiaImage() {
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $upload_dir = __DIR__ . '/../../static/uploads/noticias/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = time() . '_' . basename($_FILES['imagen']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
            return 'static/uploads/noticias/' . $file_name;
        }
    }
    return null;
}

// Función para eliminar imagen física
function deleteNoticiaImage($imagen_url) {
    if (!empty($imagen_url)) {
        $file_path = __DIR__ . '/../../' . $imagen_url;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
}

try {
    switch ($action) {
        case 'get':
            // OBTENER NOTICIAS (público)
            $id = $_GET['id'] ?? 0;
            
            if ($id) {
                // Obtener noticia específica
                $stmt = $pdo->prepare("SELECT id, titulo, contenido, imagen_url, fecha_creacion FROM noticias WHERE id = ?");
                $stmt->execute([$id]);
                $noticia = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($noticia) {
                    echo json_encode($noticia);
                } else {
                    http_response_code(404);
                    echo json_encode(['status' => 'error', 'message' => 'Noticia no encontrada.']);
                }
            } else {
                // Obtener todas las noticias
                $stmt = $pdo->prepare("SELECT id, titulo, contenido, imagen_url, fecha_creacion FROM noticias ORDER BY fecha_creacion DESC");
                $stmt->execute();
                $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($noticias);
            }
            break;

        case 'add':
            // AGREGAR NOTICIA (solo editor/admin)
            checkPermissions(['editor', 'admin']);
            
            $titulo = trim($_POST['titulo'] ?? '');
            $contenido = trim($_POST['contenido'] ?? '');
            $editor_id = $_SESSION['user_data']['id'] ?? null;
            $imagen_url = uploadNoticiaImage();

            if (empty($titulo) || empty($contenido) || empty($editor_id)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'El título y el contenido son obligatorios.']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO noticias (titulo, contenido, imagen_url, editor_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$titulo, $contenido, $imagen_url, $editor_id]);
            echo json_encode(['status' => 'ok', 'message' => 'Noticia guardada con éxito.']);
            break;

        case 'update':
            // ACTUALIZAR NOTICIA (solo editor/admin)
            checkPermissions(['editor', 'admin']);
            
            $id = $_POST['id'] ?? '';
            $titulo = trim($_POST['titulo'] ?? '');
            $contenido = trim($_POST['contenido'] ?? '');

            if (empty($id) || empty($titulo) || empty($contenido)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'El ID, título y contenido son obligatorios.']);
                exit;
            }

            // Obtener la URL de la imagen actual
            $stmt = $pdo->prepare("SELECT imagen_url FROM noticias WHERE id = ?");
            $stmt->execute([$id]);
            $noticia_actual = $stmt->fetch(PDO::FETCH_ASSOC);

            $imagen_url = null;
            $sql_imagen = "";

            // Procesar nueva imagen si se subió
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                $nueva_imagen_url = uploadNoticiaImage();
                if ($nueva_imagen_url) {
                    // Eliminar imagen anterior si existe
                    if ($noticia_actual && !empty($noticia_actual['imagen_url'])) {
                        deleteNoticiaImage($noticia_actual['imagen_url']);
                    }
                    $imagen_url = $nueva_imagen_url;
                    $sql_imagen = ", imagen_url = ?";
                }
            }

            if (!empty($sql_imagen)) {
                $stmt = $pdo->prepare("UPDATE noticias SET titulo = ?, contenido = ? $sql_imagen WHERE id = ?");
                $stmt->execute([$titulo, $contenido, $imagen_url, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE noticias SET titulo = ?, contenido = ? WHERE id = ?");
                $stmt->execute([$titulo, $contenido, $id]);
            }

            echo json_encode(['status' => 'ok', 'message' => 'Noticia actualizada con éxito.']);
            break;

        case 'delete':
            // ELIMINAR NOTICIA (solo editor/admin)
            checkPermissions(['editor', 'admin']);
            
            $id = $_POST['id'] ?? '';

            if (empty($id)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'ID de noticia no proporcionado.']);
                exit;
            }

            // Obtener la URL de la imagen para borrar el archivo físico
            $stmt = $pdo->prepare("SELECT imagen_url FROM noticias WHERE id = ?");
            $stmt->execute([$id]);
            $noticia = $stmt->fetch(PDO::FETCH_ASSOC);

            // Borrar el registro de la base de datos
            $delete_stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
            $delete_stmt->execute([$id]);

            if ($delete_stmt->rowCount() > 0) {
                // Borrar imagen física si existe
                if ($noticia && !empty($noticia['imagen_url'])) {
                    deleteNoticiaImage($noticia['imagen_url']);
                }
                echo json_encode(['status' => 'ok', 'message' => 'Noticia eliminada con éxito.']);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'No se encontró la noticia a eliminar.']);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Acción no válida. Las acciones permitidas son: get, add, update, delete']);
            break;
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>