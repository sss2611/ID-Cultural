<?php
require_once __DIR__ . '/../config/connection.php'; // Incluye config y la conexión

header('Content-Type: application/json');

// 1. Verificación de seguridad: ¿Hay un usuario logueado?
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Acceso no autorizado. Debes iniciar sesión.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$clave_actual = $_POST['clave_actual'] ?? '';
$nueva_clave = $_POST['nueva_clave'] ?? '';

if (empty($clave_actual) || empty($nueva_clave)) {
    echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios.']);
    exit();
}

try {
    // 2. Obtenemos la contraseña actual hasheada de la base de datos
    $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Error: Usuario no encontrado.']);
        exit();
    }

    // 3. Verificamos si la contraseña actual que ingresó es correcta
    if (password_verify($clave_actual, $user['password'])) {
        // Si es correcta, hasheamos la nueva contraseña
        $nueva_clave_hasheada = password_hash($nueva_clave, PASSWORD_DEFAULT);

        // Actualizamos la base de datos
        $updateStmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $updateStmt->execute([$nueva_clave_hasheada, $user_id]);

        echo json_encode(['status' => 'ok', 'message' => 'Contraseña actualizada con éxito.']);

    } else {
        // Si la contraseña actual es incorrecta
        echo json_encode(['status' => 'error', 'message' => 'La contraseña actual no es correcta.']);
    }

} catch (PDOException $e) {
    error_log("Error al cambiar clave: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Error al contactar la base de datos.']);
}