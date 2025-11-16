<?php
session_start();
require_once __DIR__ . '/../config/connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(["status" => "error", "message" => "Sesión no válida"]);
    exit;
}

$usuarioId = $_SESSION['user']['id'];

try {
    // Seleccionamos borradores en estado 'borrador'
    $stmt = $pdo->prepare("SELECT * FROM borradores WHERE usuario_id = ? AND estado = 'borrador'");
    $stmt->execute([$usuarioId]);
    $borradores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$borradores) {
        echo json_encode(["status" => "error", "message" => "No hay borradores para enviar."]);
        exit;
    }

    // Iniciamos transacción
    $pdo->beginTransaction();

    $insert = $pdo->prepare("INSERT INTO borradores_pendientes (
        borrador_id, usuario_id, titulo, descripcion, categoria, campos_extra, multimedia
    ) VALUES (?, ?, ?, ?, ?, ?, ?)");

    $update = $pdo->prepare("UPDATE borradores SET estado = 'pendiente_validacion' WHERE id = ?");

    $count = 0;
    foreach ($borradores as $borrador) {
        $insert->execute([
            $borrador['id'],
            $usuarioId,
            $borrador['titulo'],
            $borrador['descripcion'],
            $borrador['categoria'],
            $borrador['campos_extra'],
            $borrador['multimedia']
        ]);

        $update->execute([$borrador['id']]);
        $count++;
    }

    $pdo->commit();

    echo json_encode([
        "status" => "ok",
        "message" => "Se enviaron $count borradores para validación."
    ]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode([
        "status" => "error",
        "message" => "Error al enviar: " . $e->getMessage()
    ]);
}
