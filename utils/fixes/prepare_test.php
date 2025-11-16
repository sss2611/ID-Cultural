<?php
require 'backend/config/connection.php';

try {
    echo "=== Limpiando y preparando test ===\n\n";
    
    // Eliminar todas las obras de prueba
    $pdo->query("DELETE FROM publicaciones WHERE usuario_id = 8");
    echo "✓ Eliminadas obras del usuario 8\n";
    
    // Verificar estado inicial
    echo "\n=== Obras pendientes de validación DESPUÉS de limpiar ===\n";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM publicaciones WHERE estado IN ('pendiente', 'pendiente_validacion')");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total: " . $result['total'] . "\n";
    
    echo "\n✓ Base de datos preparada para test\n";
    echo "Ahora crea una NUEVA obra desde artista y envía a validación\n";
    echo "Luego verifica que aparezca en el panel de validación\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
