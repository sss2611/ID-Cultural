<?php
// tests/bootstrap.php - Bootstrap para PHPUnit

// Definir directorio base
define('BASE_PATH', dirname(__DIR__));

// Cargar autoload de Composer
require_once BASE_PATH . '/vendor/autoload.php';

// Configurar para tests
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'testing');
}

// No conectar a la BD automáticamente en tests
// Las pruebas unitarias no necesitan conexión real
define('SKIP_DB_CONNECTION', true);

// Cargar configuración solo si no estamos en testing
if (!defined('SKIP_DB_CONNECTION')) {
    require_once BASE_PATH . '/config.php';
    require_once BASE_PATH . '/backend/config/connection.php';
}

