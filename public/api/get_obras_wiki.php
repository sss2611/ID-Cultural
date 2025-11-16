<?php
/**
 * API para obtener obras publicadas (para la Wiki)
 * Archivo: /public/api/get_obras_wiki.php
 * 
 * Retorna todas las obras validadas/publicadas para mostrar en la wiki
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../backend/config/connection.php';

try {
    // Obtener filtros
    $categoria_filter = $_GET['categoria'] ?? null;
    $municipio_filter = $_GET['municipio'] ?? null;
    $artista_filter = $_GET['artista'] ?? null;
    $busqueda = $_GET['busqueda'] ?? null;

    // Consulta para obtener obras publicadas
    $sql = "
        SELECT 
            p.id,
            p.titulo,
            p.descripcion,
            p.categoria,
            p.estado,
            p.multimedia,
            p.fecha_creacion,
            a.id AS artista_id,
            CONCAT(a.nombre, ' ', a.apellido) AS artista_nombre,
            a.municipio,
            a.provincia,
            a.email AS artista_email,
            a.status AS artista_status
        FROM publicaciones p
        INNER JOIN artistas a ON p.usuario_id = a.id
        WHERE p.estado = 'validado' AND a.status = 'validado'
    ";
    
    $params = [];

    // Aplicar filtros
    if ($categoria_filter) {
        $sql .= " AND p.categoria = ?";
        $params[] = $categoria_filter;
    }

    if ($municipio_filter) {
        $sql .= " AND a.municipio = ?";
        $params[] = $municipio_filter;
    }

    if ($artista_filter) {
        $sql .= " AND a.id = ?";
        $params[] = $artista_filter;
    }

    if ($busqueda) {
        $sql .= " AND (p.titulo LIKE ? OR p.descripcion LIKE ? OR CONCAT(a.nombre, ' ', a.apellido) LIKE ?)";
        $busqueda_param = '%' . $busqueda . '%';
        $params[] = $busqueda_param;
        $params[] = $busqueda_param;
        $params[] = $busqueda_param;
    }

    $sql .= " ORDER BY p.fecha_creacion DESC LIMIT 100";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    $obras = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Procesar URLs de imágenes desde multimedia (JSON o ruta)
        foreach ($obras as &$obra) {
            // Si multimedia contiene JSON, extraer primera imagen
            $imagen_url = '/static/img/placeholder-obra.png';
            
            if (!empty($obra['multimedia'])) {
                // Intentar decodificar como JSON
                $multimedia = json_decode($obra['multimedia'], true);
                if (is_array($multimedia) && !empty($multimedia)) {
                    // Si es un array de imágenes, tomar la primera
                    $imagen_url = $multimedia[0];
                } else {
                    // Si es una ruta directa
                    $imagen_url = $obra['multimedia'];
                }
            }
            
            $obra['imagen_url'] = $imagen_url;
        }
        
        // Log para depuración
        error_log("get_obras_wiki.php: Encontradas " . count($obras) . " obras");
        
        echo json_encode([
            'status' => 'success',
            'total' => count($obras),
            'obras' => $obras
        ]);

    } catch (PDOException $e) {
        error_log("Error en get_obras_wiki.php: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Error al consultar las obras.',
            'error' => $e->getMessage()
        ]);
    }
    ?>
