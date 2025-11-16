<?php
ob_start();
require_once __DIR__ . '/../../backend/config/connection.php'; 
ob_end_clean();

header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("SELECT id, user_name, action, details, timestamp FROM system_logs ORDER BY timestamp DESC");
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($logs);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al consultar la base de datos: ' . $e->getMessage()]);
}
exit;
?>
