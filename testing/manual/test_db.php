<?php
require 'backend/config/connection.php';

try {
    echo "=== Verificando tabla publicaciones ===\n";
    
    // Contar registros
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM publicaciones");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total de registros: {$result['total']}\n\n";
    
    if ($result['total'] > 0) {
        $stmt = $pdo->query("SELECT * FROM publicaciones LIMIT 5");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        print_r($rows);
    } else {
        echo "No hay registros en publicaciones\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString();
}
?>
