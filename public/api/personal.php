<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../backend/config/connection.php';

// Obtener acción desde GET o POST
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Verificar permisos de administrador
function checkAdminPermissions() {
    if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'No tienes permiso para realizar esta acción.']);
        exit;
    }
}

// Función para validar email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Función para validar contraseña
function isValidPassword($password) {
    return strlen($password) >= 8;
}

// Función para validar datos básicos del usuario
function validateUserData($nombre, $email, $rol) {
    if (empty($nombre) || empty($email) || empty($rol)) {
        return 'Todos los campos son obligatorios.';
    }
    
    if (!isValidEmail($email)) {
        return 'El formato del correo electrónico no es válido.';
    }
    
    if (!in_array($rol, ['admin', 'editor', 'validador'])) {
        return 'El rol especificado no es válido.';
    }
    
    return null;
}

try {
    switch ($action) {
        case 'get':
            // OBTENER PERSONAL (solo admin)
            checkAdminPermissions();
            
            $id = $_GET['id'] ?? 0;
            
            if ($id) {
                // Obtener usuario específico
                $stmt = $pdo->prepare("SELECT id, nombre, email, role FROM users WHERE id = ? AND role IN ('admin', 'editor', 'validador')");
                $stmt->execute([$id]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($usuario) {
                    echo json_encode($usuario);
                } else {
                    http_response_code(404);
                    echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado.']);
                }
            } else {
                // Obtener todo el personal
                $stmt = $pdo->prepare("SELECT id, nombre, email, role FROM users WHERE role IN ('admin', 'editor', 'validador') ORDER BY id DESC");
                $stmt->execute();
                $personal = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($personal);
            }
            break;

        case 'add':
            // AGREGAR PERSONAL (solo admin)
            checkAdminPermissions();
            
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $rol = trim($_POST['rol'] ?? '');
            $password = $_POST['password'] ?? '';

            // Validaciones
            $validationError = validateUserData($nombre, $email, $rol);
            if ($validationError) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => $validationError]);
                exit;
            }

            if (!isValidPassword($password)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'La contraseña debe tener al menos 8 caracteres.']);
                exit;
            }

            // Hashear la contraseña
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            try {
                $stmt = $pdo->prepare("INSERT INTO users (nombre, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nombre, $email, $hashed_password, $rol]);

                echo json_encode(['status' => 'ok', 'message' => 'Usuario agregado con éxito.']);

            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    echo json_encode(['status' => 'error', 'message' => 'El correo electrónico ya está registrado.']);
                } else {
                    throw $e;
                }
            }
            break;

        case 'update':
            // ACTUALIZAR PERSONAL (solo admin)
            checkAdminPermissions();
            
            $id = $_POST['id'] ?? '';
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = trim($_POST['role'] ?? '');
            $password = $_POST['password'] ?? '';

            // Validaciones básicas
            if (empty($id) || empty($nombre) || empty($email) || empty($role)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Los campos nombre, email y rol son obligatorios.']);
                exit;
            }

            if (!isValidEmail($email)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'El formato del correo electrónico no es válido.']);
                exit;
            }

            if (!in_array($role, ['admin', 'editor', 'validador'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'El rol especificado no es válido.']);
                exit;
            }

            // Si se proporciona contraseña, validarla
            if (!empty($password) && !isValidPassword($password)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'La nueva contraseña debe tener al menos 8 caracteres.']);
                exit;
            }

            // Construir consulta dinámicamente
            $sql_parts = [
                "nombre = :nombre",
                "email = :email", 
                "role = :role"
            ];
            $params = [
                ':id' => $id,
                ':nombre' => $nombre,
                ':email' => $email,
                ':role' => $role
            ];

            // Si se proporcionó contraseña, añadirla
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql_parts[] = "password = :password";
                $params[':password'] = $hashed_password;
            }

            $sql = "UPDATE users SET " . implode(', ', $sql_parts) . " WHERE id = :id";
            
            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                if ($stmt->rowCount() > 0) {
                    echo json_encode(['status' => 'ok', 'message' => 'Usuario actualizado con éxito.']);
                } else {
                    // Puede que no se hayan cambiado datos o que el usuario no exista
                    echo json_encode(['status' => 'ok', 'message' => 'No se realizaron cambios o el usuario no existe.']);
                }

            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    echo json_encode(['status' => 'error', 'message' => 'El correo electrónico ya está en uso por otra cuenta.']);
                } else {
                    throw $e;
                }
            }
            break;

        case 'delete':
            // ELIMINAR PERSONAL (solo admin)
            checkAdminPermissions();
            
            $id = $_POST['id'] ?? '';

            if (empty($id)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'ID de usuario no proporcionado.']);
                exit;
            }

            // No permitir eliminarse a sí mismo
            if ($id == $_SESSION['user_data']['id']) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'No puedes eliminar tu propio usuario.']);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(['status' => 'ok', 'message' => 'Usuario eliminado con éxito.']);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'No se encontró el usuario a eliminar.']);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Acción no válida. Acciones permitidas: get, add, update, delete']);
            break;
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>