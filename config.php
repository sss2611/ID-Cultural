<?php

/**
 * Configuración Global - ID Cultural
 * Detecta automáticamente el entorno y configura BASE_URL
 */

// Detectar si estamos en desarrollo local o producción
$is_local = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', 'idcultural_web']);
$is_tailscale = strpos($_SERVER['HTTP_HOST'] ?? '', '.ts.net') !== false;

// Configurar BASE_URL según el entorno
if ($is_local) {
    // Desarrollo local (Docker)
    define('BASE_URL', 'http://localhost:8080/');
} elseif ($is_tailscale) {
    // Tailscale Funnel - raíz del dominio
    define('BASE_URL', 'https://server-itse.tail0ce263.ts.net/');
} else {
    // Producción (otro servidor)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $server_ip = $_SERVER['SERVER_NAME'] ?? $_SERVER['SERVER_ADDR'] ?? 'localhost';
    $server_port = $_SERVER['SERVER_PORT'] ?? '80';
    $port_suffix = ($server_port == '80' || $server_port == '443') ? '' : ':' . $server_port;
    define('BASE_URL', $protocol . '://' . $server_ip . $port_suffix . '/');
}

// Configuración de Base de Datos
define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_USER', getenv('DB_USER') ?: 'runatechdev');
define('DB_PASS', getenv('DB_PASS') ?: '1234');
define('DB_NAME', getenv('DB_NAME') ?: 'idcultural');

// Mostrar errores solo en desarrollo
if ($is_local) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/logs/php-errors.log');
}

?>