<?php
require 'backend/config/connection.php';

try {
    echo "=== Verificando obras creadas ===\n\n";
    
    // Ver todas las obras
    $stmt = $pdo->query("
        SELECT id, titulo, estado, usuario_id, fecha_envio_validacion, campos_extra 
        FROM publicaciones 
        ORDER BY id DESC 
        LIMIT 10
    ");
    
    $obras = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($obras as $obra) {
        echo "ID: {$obra['id']}, Título: {$obra['titulo']}, Estado: {$obra['estado']}\n";
        echo "  Usuario: {$obra['usuario_id']}, Fecha envío: " . ($obra['fecha_envio_validacion'] ?: 'NULL') . "\n";
        echo "  Campos extra: " . substr($obra['campos_extra'], 0, 50) . "...\n\n";
    }
    
    echo "=== Test API ===\n";
    echo "GET /api/get_publicaciones.php?estado=pendiente_validacion\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
