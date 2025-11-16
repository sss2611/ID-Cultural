<?php
require_once __DIR__ . '/../config/connection.php'; // Incluye config y la conexión

header('Content-Type: application/json');

// 1. Verificación de seguridad: ¿Quien hace la petición es un admin?
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Acceso no autorizado.']);
    exit();
}

$email = $_POST['email'] ?? '';

if (empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'El correo no puede estar vacío.']);
    exit();
}

// 2. Generar una contraseña aleatoria y segura
$nueva_clave_aleatoria = bin2hex(random_bytes(4)); // Genera 8 caracteres aleatorios (ej: a3f7b1c9)

// 3. Hashear la nueva contraseña antes de guardarla (¡NUNCA GUARDAR TEXTO PLANO!)
$nueva_clave_hasheada = password_hash($nueva_clave_aleatoria, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE email = ?");
    $stmt->execute([$nueva_clave_hasheada, $email]);

    // Verificamos si se actualizó alguna fila
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'status' => 'ok',
            'message' => "La clave para '$email' fue blanqueada. La nueva clave es: " . $nueva_clave_aleatoria
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => "No se encontró ningún usuario con el correo: $email"]);
    }
} catch (PDOException $e) {
    error_log("Error en blanqueo: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Error al actualizar la base de datos.']);
}