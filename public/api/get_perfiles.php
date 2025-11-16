<?php
/**
 * get_perfiles.php
 * Obtiene la lista de perfiles de artistas filtrados por estado
 */

// Evitar que se muestren warnings/notices que rompan el JSON
ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../backend/config/connection.php';

header('Content-Type: application/json; charset=UTF-8');

// ============================================
// 1. VERIFICACIÓN DE SEGURIDAD
// ============================================

if (!isset($_SESSION['user_data']) || !in_array($_SESSION['user_data']['role'], ['validador', 'admin'])) {
    http_response_code(401);
    echo json_encode([
        'error' => 'No tienes permisos para acceder a esta información'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ============================================
// 2. OBTENER PARÁMETROS
// ============================================

$estado_filter = $_GET['estado'] ?? 'pendiente';
$provincia_filter = $_GET['provincia'] ?? null;
$limite = (int)($_GET['limite'] ?? 50);
$pagina = (int)($_GET['pagina'] ?? 1);
$offset = ($pagina - 1) * $limite;

// Validar estado
if (!in_array($estado_filter, ['pendiente', 'validado', 'rechazado', 'todos'])) {
    $estado_filter = 'pendiente';
}

// ============================================
// 3. CONSTRUIR CONSULTA
// ============================================

try {
    // Consulta base
    $sql = "SELECT 
        a.id,
        a.nombre,
        a.apellido,
        a.email,
        a.provincia,
        a.municipio,
        a.especialidades,
        a.biografia,
        a.foto_perfil,
        a.instagram,
        a.facebook,
        a.twitter,
        a.sitio_web,
        a.status_perfil,
        a.motivo_rechazo
    FROM artistas a
    WHERE 1=1";
    
    $params = [];

    // Filtro de estado
    if ($estado_filter !== 'todos') {
        $sql .= " AND a.status_perfil = ?";
        $params[] = $estado_filter;
    }

    // Filtro de provincia (opcional)
    if ($provincia_filter) {
        $sql .= " AND a.provincia = ?";
        $params[] = $provincia_filter;
    }

    // Ordenar: pendientes primero, luego por ID (más reciente si es autoincrement)
    $sql .= " ORDER BY 
        FIELD(a.status_perfil, 'pendiente', 'rechazado', 'validado') ASC,
        a.id DESC";

    // Contar total de registros (antes de agregar LIMIT)
    $count_params = $params; // Copiar parámetros
    $sql_count = "SELECT COUNT(*) as total FROM artistas a WHERE 1=1";
    
    // Aplicar los mismos filtros a la consulta de conteo
    if ($estado_filter !== 'todos') {
        $sql_count .= " AND a.status_perfil = ?";
    }
    if ($provincia_filter) {
        $sql_count .= " AND a.provincia = ?";
    }

    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute($count_params);
    $result_count = $stmt_count->fetch(PDO::FETCH_ASSOC);
    $total = $result_count ? $result_count['total'] : 0;

    // Agregar paginación DIRECTAMENTE (no como parámetro preparado)
    $sql .= " LIMIT " . (int)$limite . " OFFSET " . (int)$offset;

    // Ejecutar consulta
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $perfiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ============================================
    // 4. RESPUESTA
    // ============================================

    // Si solo se requieren los perfiles (sin paginación info)
    if (count($perfiles) > 0) {
        echo json_encode($perfiles, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([], JSON_UNESCAPED_UNICODE);
    }

} catch (PDOException $e) {
    error_log("Error PDO al obtener perfiles: " . $e->getMessage());
    error_log("SQL: " . $sql ?? "");
    error_log("Params: " . json_encode($params ?? []));
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al obtener los perfiles',
        'debug' => 'Error de base de datos: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    error_log("Error general al obtener perfiles: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al obtener los perfiles',
        'debug' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

?>
