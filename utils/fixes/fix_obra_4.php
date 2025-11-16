<?php
require 'backend/config/connection.php';

try {
    // Actualizar la obra ID 4 a estado pendiente_validacion y limpiar campos_extra
    $stmt = $pdo->prepare("
        UPDATE publicaciones 
        SET 
            estado = 'pendiente_validacion',
            fecha_envio_validacion = NOW(),
            campos_extra = JSON_SET(campos_extra, '$.action', NULL)
        WHERE id = 4
    ");
    $stmt->execute();
    
    echo "Obra ID 4 actualizada\n";
    echo "Filas afectadas: " . $stmt->rowCount() . "\n";
    
    // Mostrar estado actual
    $stmt = $pdo->prepare("SELECT id, titulo, estado, campos_extra FROM publicaciones WHERE id = 4");
    $stmt->execute();
    $obra = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "\nEstado actual:\n";
    echo "ID: {$obra['id']}\n";
    echo "Título: {$obra['titulo']}\n";
    echo "Estado: {$obra['estado']}\n";
    echo "Campos Extra: {$obra['campos_extra']}\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
