<?php
session_start();
require_once __DIR__ . '/../../../../../config.php';

// Redirección al panel compartido para Validador
header('Location: ' . BASE_URL . 'src/views/pages/shared/gestion_artistas_obras.php');
exit();
?>