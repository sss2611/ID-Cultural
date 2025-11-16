<?php
require 'config.php';

try {
    $conn = new PDO('mysql:host=db;dbname=id_cultural', 'root', 'idcultural_pass');
    
    // Check for validators and admins
    $stmt = $conn->query("SELECT id, username, email, role FROM usuarios WHERE role IN ('validador', 'admin') LIMIT 10");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "=== Usuarios Validador/Admin ===\n";
    if (empty($users)) {
        echo "No hay usuarios con rol validador o admin\n";
    } else {
        foreach ($users as $user) {
            echo "ID: {$user['id']}, Usuario: {$user['username']}, Email: {$user['email']}, Rol: {$user['role']}\n";
        }
    }
    
    // Check works pending validation
    echo "\n=== Obras Pendientes de Validación ===\n";
    $stmt = $conn->query("SELECT id, titulo, estado, usuario_id FROM publicaciones WHERE estado = 'pendiente_validacion' LIMIT 5");
    $obras = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($obras as $obra) {
        echo "ID: {$obra['id']}, Título: {$obra['titulo']}, Estado: {$obra['estado']}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
