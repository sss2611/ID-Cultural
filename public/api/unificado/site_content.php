<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../backend/config/connection.php';

// Obtener acción desde GET o POST
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Verificar permisos de editor/admin
function checkEditorPermissions() {
    if (!isset($_SESSION['user_data']) || !in_array($_SESSION['user_data']['role'], ['editor', 'admin'])) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'No tienes permiso para realizar esta acción.']);
        exit;
    }
}

// Función para subir imágenes
function uploadSiteImage($file_key, $target_dir) {
    if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] == 0) {
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_name = time() . '_' . basename($_FILES[$file_key]['name']);
        $target_file = $target_dir . $file_name;
        
        // Validar tipo de archivo
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        if (!in_array($file_extension, $allowed_types)) {
            return ['error' => 'Tipo de archivo no permitido. Solo se permiten: ' . implode(', ', $allowed_types)];
        }
        
        // Validar tamaño (máximo 5MB)
        if ($_FILES[$file_key]['size'] > 5 * 1024 * 1024) {
            return ['error' => 'El archivo es demasiado grande. Máximo 5MB permitidos.'];
        }
        
        if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $target_file)) {
            return ['success' => 'static/uploads/site_content/' . $file_name];
        } else {
            return ['error' => 'Error al subir el archivo.'];
        }
    }
    return null;
}

try {
    switch ($action) {
        case 'get':
            // OBTENER CONTENIDO DEL SITIO (público)
            $key = $_GET['key'] ?? '';
            
            if ($key) {
                // Obtener contenido específico
                $stmt = $pdo->prepare("SELECT content_key, content_value FROM site_content WHERE content_key = ?");
                $stmt->execute([$key]);
                $content = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($content) {
                    echo json_encode($content);
                } else {
                    http_response_code(404);
                    echo json_encode(['status' => 'error', 'message' => 'Contenido no encontrado.']);
                }
            } else {
                // Obtener todo el contenido
                $stmt = $pdo->prepare("SELECT content_key, content_value FROM site_content");
                $stmt->execute();
                $content_items = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
                
                echo json_encode($content_items);
            }
            break;

        case 'update':
            // ACTUALIZAR CONTENIDO DEL SITIO (solo editor/admin)
            checkEditorPermissions();
            
            $pdo->beginTransaction();

            try {
                // Directorio para subir imágenes
                $upload_dir = __DIR__ . '/../../static/uploads/site_content/';
                
                // Actualizar campos de texto
                $text_fields = [
                    'welcome_title', 'welcome_paragraph', 'welcome_slogan',
                    'about_title', 'about_content', 'contact_info',
                    'footer_text', 'meta_description', 'meta_keywords'
                ];
                
                $updated_count = 0;
                foreach ($text_fields as $field) {
                    if (isset($_POST[$field])) {
                        $stmt = $pdo->prepare("UPDATE site_content SET content_value = ? WHERE content_key = ?");
                        $stmt->execute([trim($_POST[$field]), $field]);
                        $updated_count += $stmt->rowCount();
                    }
                }

                // Actualizar imágenes del carrusel
                for ($i = 1; $i <= 3; $i++) {
                    $image_key = 'carousel_image_' . $i;
                    $upload_result = uploadSiteImage($image_key, $upload_dir);
                    
                    if ($upload_result && isset($upload_result['success'])) {
                        $stmt = $pdo->prepare("UPDATE site_content SET content_value = ? WHERE content_key = ?");
                        $stmt->execute([$upload_result['success'], $image_key]);
                        $updated_count += $stmt->rowCount();
                    } elseif ($upload_result && isset($upload_result['error'])) {
                        throw new Exception($upload_result['error']);
                    }
                }

                // Actualizar logo y otras imágenes del sitio
                $image_fields = ['site_logo', 'favicon', 'default_image'];
                foreach ($image_fields as $image_field) {
                    $upload_result = uploadSiteImage($image_field, $upload_dir);
                    
                    if ($upload_result && isset($upload_result['success'])) {
                        $stmt = $pdo->prepare("UPDATE site_content SET content_value = ? WHERE content_key = ?");
                        $stmt->execute([$upload_result['success'], $image_field]);
                        $updated_count += $stmt->rowCount();
                    } elseif ($upload_result && isset($upload_result['error'])) {
                        throw new Exception($upload_result['error']);
                    }
                }

                $pdo->commit();
                
                if ($updated_count > 0) {
                    echo json_encode(['status' => 'ok', 'message' => 'Contenido del sitio actualizado con éxito.', 'updated' => $updated_count]);
                } else {
                    echo json_encode(['status' => 'info', 'message' => 'No se realizaron cambios en el contenido.']);
                }

            } catch (Exception $e) {
                $pdo->rollBack();
                throw new Exception($e->getMessage());
            }
            break;

        case 'update_bulk':
            // ACTUALIZACIÓN MASIVA DE CONTENIDO (solo editor/admin)
            checkEditorPermissions();
            
            $content_data = $_POST['content_data'] ?? '';
            
            if (empty($content_data)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Datos de contenido no proporcionados.']);
                exit;
            }

            // Decodificar JSON si viene como string
            if (is_string($content_data)) {
                $content_data = json_decode($content_data, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'Formato JSON inválido.']);
                    exit;
                }
            }

            $pdo->beginTransaction();

            try {
                $updated_count = 0;
                foreach ($content_data as $key => $value) {
                    $stmt = $pdo->prepare("UPDATE site_content SET content_value = ? WHERE content_key = ?");
                    $stmt->execute([trim($value), $key]);
                    $updated_count += $stmt->rowCount();
                }

                $pdo->commit();
                echo json_encode(['status' => 'ok', 'message' => 'Contenido actualizado masivamente.', 'updated' => $updated_count]);

            } catch (PDOException $e) {
                $pdo->rollBack();
                throw $e;
            }
            break;

        case 'get_keys':
            // OBTENER LISTA DE LLAVES DISPONIBLES (público o autenticado)
            $stmt = $pdo->prepare("SELECT content_key FROM site_content ORDER BY content_key");
            $stmt->execute();
            $keys = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            echo json_encode($keys);
            break;

        default:
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Acción no válida. Acciones permitidas: get, update, update_bulk, get_keys']);
            break;
    }

} catch (PDOException $e) {
    // Rollback si hay alguna transacción activa
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Rollback si hay alguna transacción activa
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>