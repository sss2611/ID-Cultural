<?php
require 'backend/config/connection.php';

try {
    echo "=== Verificando multimedia de obra validada ===\n\n";
    
    $stmt = $pdo->query("
        SELECT id, titulo, estado, multimedia 
        FROM publicaciones 
        WHERE estado = 'validado'
    ");
    
    $obras = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($obras as $obra) {
        echo "ID: {$obra['id']}, Título: {$obra['titulo']}\n";
        echo "Multimedia: " . ($obra['multimedia'] ?: 'NULL/VACÍO') . "\n";
        echo "Tipo: " . gettype($obra['multimedia']) . "\n\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
