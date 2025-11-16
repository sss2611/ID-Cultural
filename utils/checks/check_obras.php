<?php
require 'backend/config/connection.php';

try {
    $conn = $pdo;
    
    echo "=== Obras por estado (últimas 15) ===\n";
    $stmt = $conn->query("SELECT id, titulo, estado, usuario_id, multimedia, fecha_envio_validacion FROM publicaciones ORDER BY id DESC LIMIT 15");
    
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        echo "ID: {$row['id']}, Título: {$row['titulo']}, Estado: {$row['estado']}, Usuario: {$row['usuario_id']}, Multimedia: " . ($row['multimedia'] ? 'Sí' : 'No') . "\n";
    }
    
    echo "\n=== Obras pendiente_validacion ===\n";
    $stmt = $conn->query("SELECT id, titulo, usuario_id, multimedia, fecha_envio_validacion FROM publicaciones WHERE estado = 'pendiente_validacion'");
    $obras = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($obras as $row) {
        echo "ID: {$row['id']}, Título: {$row['titulo']}, Usuario: {$row['usuario_id']}\n";
    }
    echo "Total: " . count($obras) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
