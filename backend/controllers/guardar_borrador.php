<?php
session_start();
require_once __DIR__ . '/../config/connection.php';

header('Content-Type: application/json');

// Verificamos si hay un usuario logueado
if (!isset($_SESSION['user']['id'])) {
    echo json_encode(["status" => "error", "message" => "Sesión no válida"]);
    exit;
}

$usuarioId = $_SESSION['user']['id'];

// Recoger datos obligatorios
$titulo      = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$categoria   = $_POST['categoria'] ?? '';

// Multimedia puede ser texto único o array
$multimedia = $_POST['multimedia'] ?? '';
if (is_array($multimedia)) {
    $multimedia = implode(",", $multimedia);
}

// Construir campos_extra excluyendo los principales
$excluir = ['titulo', 'descripcion', 'categoria', 'multimedia'];
$campos_extra = array_diff_key($_POST, array_flip($excluir));
$campos_extra_json = json_encode($campos_extra);

if (!$titulo || !$descripcion || !$categoria) {
    echo json_encode(["status" => "error", "message" => "Faltan campos obligatorios"]);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO borradores (
        usuario_id, titulo, descripcion, categoria, campos_extra, multimedia, estado
    ) VALUES (?, ?, ?, ?, ?, ?, 'borrador')");

    $stmt->execute([$usuarioId, $titulo, $descripcion, $categoria, $campos_extra_json, $multimedia]);

    echo json_encode(["status" => "ok", "message" => "Borrador guardado correctamente"]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Error al guardar: " . $e->getMessage()]);
}
