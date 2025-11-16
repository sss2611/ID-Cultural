<?php
require 'backend/config/connection.php';

try {
    echo "=== Crear/Actualizar Usuario Admin ===\n\n";
    
    // Hash de contraseña: password123
    $password = password_hash('password123', PASSWORD_BCRYPT);
    
    // Intentar actualizar si existe
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ? AND role = 'admin'");
    $result = $stmt->execute([$password, 'admin@idcultural.com']);
    
    if ($stmt->rowCount() > 0) {
        echo "✓ Usuario admin actualizado\n";
    } else {
        // Si no existe, crear uno
        $stmt = $pdo->prepare("INSERT INTO users (nombre, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Administrador', 'admin@idcultural.com', $password, 'admin']);
        echo "✓ Usuario admin creado\n";
    }
    
    echo "\n=== Credenciales ===\n";
    echo "Email: admin@idcultural.com\n";
    echo "Contraseña: password123\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
