<?php
require_once __DIR__ . '/../config/connection.php';

$email = strtolower(trim($_POST['email'] ?? ''));
$intereses = $_POST['intereses'] ?? [];

header('Content-Type: text/plain');

if (!$email || empty($intereses)) {
    echo "❌ Faltan datos";
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM artistas WHERE email = ?");
$stmt->execute([$email]);
$artista = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$artista) {
    echo "❌ Usuario no encontrado";
    exit;
}

$artistaId = $artista['id'];

try {
    $insert = $pdo->prepare("INSERT INTO intereses_artista (artista_id, interes) VALUES (?, ?)");

    foreach ($intereses as $interes) {
        $insert->execute([$artistaId, $interes]);
    }

    echo "✅ Intereses guardados";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
