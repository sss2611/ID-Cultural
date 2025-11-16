<?php
require 'backend/config/connection.php';

try {
    echo "=== Limpiando y verificando BD ===\n\n";
    
    // Eliminar la obra ID 4 (test) porque tiene campos_extra corrupto
    $pdo->query("DELETE FROM publicaciones WHERE id = 4");
    echo "✓ Eliminada obra ID 4\n";
    
    // Verificar obras pendientes
    echo "\n=== Obras pendientes de validación ===\n";
    $stmt = $pdo->query("
        SELECT id, titulo, estado, usuario_id, fecha_envio_validacion, campos_extra FROM publicaciones 
        WHERE estado = 'pendiente_validacion'
    ");
    
    $obras = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($obras as $obra) {
        echo "ID: {$obra['id']}, Título: {$obra['titulo']}\n";
        echo "  Campos extra: {$obra['campos_extra']}\n";
        echo "  Fecha envío: {$obra['fecha_envio_validacion']}\n";
    }
    
    echo "\nTotal pendientes: " . count($obras) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
