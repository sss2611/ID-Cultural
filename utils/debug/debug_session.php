<?php
// Script para verificar si la sesión está bien iniciada desde HTTP
session_start();
require 'backend/config/connection.php';

echo "=== Debug Sesión ===\n";
echo "Session ID: " . session_id() . "\n";
echo "Session Data: " . json_encode($_SESSION) . "\n";

// Simular lo que hace la página de validación
if (!isset($_SESSION['user_data']) || !in_array($_SESSION['user_data']['role'], ['admin', 'validador'])) {
    echo "\nNo tiene permisos o no hay sesión activa\n";
    echo "Necesita ser admin o validador\n";
} else {
    echo "\nTiene permisos de: " . $_SESSION['user_data']['role'] . "\n";
}
?>
