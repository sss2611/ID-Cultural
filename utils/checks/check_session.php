<?php
session_start();
require 'backend/config/connection.php';

echo "=== Estado de Sesión ===\n";
if (isset($_SESSION['user_data'])) {
    echo "Usuario: " . $_SESSION['user_data']['username'] . "\n";
    echo "Rol: " . $_SESSION['user_data']['role'] . "\n";
    echo "ID: " . $_SESSION['user_data']['id'] . "\n";
} else {
    echo "No hay sesión activa\n";
}

echo "\n=== Usuarios validador/admin en DB ===\n";
$stmt = $pdo->query("SELECT id, username, email, role FROM users WHERE role IN ('validador', 'admin') LIMIT 10");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($users as $user) {
    echo "ID: {$user['id']}, Usuario: {$user['username']}, Email: {$user['email']}, Rol: {$user['role']}\n";
}

echo "\n=== Verificando obras pendientes ===\n";
$stmt = $pdo->query("
    SELECT 
        p.id,
        p.titulo,
        p.estado,
        p.usuario_id,
        p.fecha_envio_validacion,
        u.username
    FROM publicaciones p
    LEFT JOIN users u ON p.usuario_id = u.id
    WHERE p.estado = 'pendiente_validacion'
    ORDER BY p.id DESC
");

$obras = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Total obras pendiente_validacion: " . count($obras) . "\n";
foreach ($obras as $obra) {
    echo "ID: {$obra['id']}, Título: {$obra['titulo']}, Estado: {$obra['estado']}, Usuario: {$obra['username']}\n";
}
?>
