<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../helpers/EmailHelper.php';

header('Content-Type: text/plain');

// Inicializar conexión
$pdo = getConnection();

$nombre = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$fecha = trim($_POST['fechaNacimiento'] ?? '');
$genero = $_POST['genero'] ?? '';
$pais = $_POST['pais'] ?? '';
$provincia = $_POST['provincia'] ?? '';
$municipio = $_POST['municipio'] ?? '';
$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if (!$email || !$password || !$nombre || !$apellido || !$fecha) {
    echo "❌ Faltan datos";
    exit;
}

try {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO artistas 
        (nombre, apellido, fecha_nacimiento, genero, pais, provincia, municipio, email, password)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([$nombre, $apellido, $fecha, $genero, $pais, $provincia, $municipio, $email, $hashedPassword]);

    // Enviar email de bienvenida
    try {
        $emailHelper = new EmailHelper();
        $emailHelper->enviarBienvenida($email, $nombre);
    } catch (Exception $e) {
        error_log("Error enviando email: " . $e->getMessage());
    }

    echo "✅ Registro exitoso";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
