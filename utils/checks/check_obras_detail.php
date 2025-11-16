<?php
require 'backend/config/connection.php';

try {
    echo "=== Detalles completos de las obras ===\n\n";
    
    $stmt = $pdo->query("
        SELECT 
            p.id,
            p.titulo,
            p.estado,
            p.usuario_id,
            p.multimedia,
            p.fecha_creacion,
            p.fecha_envio_validacion,
            p.fecha_validacion,
            u.username,
            u.email
        FROM publicaciones p
        LEFT JOIN usuarios u ON p.usuario_id = u.id
        ORDER BY p.id DESC
        LIMIT 10
    ");
    
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        echo "ID: {$row['id']}\n";
        echo "  Título: {$row['titulo']}\n";
        echo "  Estado: {$row['estado']}\n";
        echo "  Usuario: {$row['username']} (ID: {$row['usuario_id']})\n";
        echo "  Multimedia: " . ($row['multimedia'] ? 'Sí - ' . $row['multimedia'] : 'No') . "\n";
        echo "  Creada: {$row['fecha_creacion']}\n";
        echo "  Envío validación: " . ($row['fecha_envio_validacion'] ? $row['fecha_envio_validacion'] : 'Nunca') . "\n";
        echo "  Validada: " . ($row['fecha_validacion'] ? $row['fecha_validacion'] : 'No') . "\n";
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
