<?php
require 'backend/config/connection.php';

try {
    echo "=== Verificando estados de obras ===\n\n";
    
    $stmt = $pdo->query("
        SELECT id, titulo, estado, usuario_id, validador_id, fecha_validacion 
        FROM publicaciones 
        ORDER BY id DESC 
        LIMIT 10
    ");
    
    $obras = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($obras as $obra) {
        echo "ID: {$obra['id']}, Título: {$obra['titulo']}\n";
        echo "  Estado: {$obra['estado']}\n";
        echo "  Validador: " . ($obra['validador_id'] ? $obra['validador_id'] : 'NULL') . "\n";
        echo "  Fecha validación: " . ($obra['fecha_validacion'] ? $obra['fecha_validacion'] : 'NULL') . "\n\n";
    }
    
    echo "=== Obras con estado 'validado' ===\n";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM publicaciones WHERE estado = 'validado'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total: " . $result['total'] . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
