<?php
require 'backend/config/connection.php';

try {
    echo "=== Tablas en la BD ===\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo "$table\n";
    }
    
    echo "\n=== Estructura tabla artistas ===\n";
    $stmt = $pdo->query("DESCRIBE artistas");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "{$col['Field']}: {$col['Type']}\n";
    }
    
    echo "\n=== Artistas en BD ===\n";
    $stmt = $pdo->query("SELECT id, nombre, apellido, status FROM artistas LIMIT 5");
    $artistas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($artistas as $art) {
        echo "ID: {$art['id']}, Nombre: {$art['nombre']} {$art['apellido']}, Status: {$art['status']}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
