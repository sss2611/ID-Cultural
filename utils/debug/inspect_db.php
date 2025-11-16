<?php
require 'backend/config/connection.php';

try {
    echo "=== Estructura tabla users ===\n";
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "{$col['Field']}: {$col['Type']}\n";
    }
    
    echo "\n=== Primeros registros de users ===\n";
    $stmt = $pdo->query("SELECT * FROM users LIMIT 3");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($users)) {
        print_r($users[0]);
    } else {
        echo "No hay users\n";
    }
    
    echo "\n=== Obras pendientes de validación ===\n";
    $stmt = $pdo->query("
        SELECT id, titulo, estado, usuario_id FROM publicaciones 
        WHERE estado = 'pendiente_validacion'
    ");
    $obras = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Total: " . count($obras) . "\n";
    foreach ($obras as $obra) {
        echo "ID: {$obra['id']}, Título: {$obra['titulo']}, Estado: {$obra['estado']}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
