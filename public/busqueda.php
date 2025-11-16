<?php
session_start();
require_once __DIR__ . '/../config.php';
// --- 1. CONECTAR A LA BASE DE DATOS ---
require_once __DIR__ . '/../backend/config/connection.php';
require_once __DIR__ . '/../backend/helpers/Pagination.php';

// --- 2. OBTENER EL CONTENIDO DEL SITIO DESDE LA BD ---
try {
    $stmt = $pdo->prepare("SELECT content_key, content_value FROM site_content");
    $stmt->execute();
    // Guardamos todo el contenido en un array asociativo
    $site_content = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    // Si hay un error, usamos valores por defecto para que la página no se rompa
    $site_content = [];
    // Opcional: registrar el error
    error_log("Error al cargar el contenido del sitio: " . $e->getMessage());
}

// --- Variables para el header ---
$page_title = "Inicio - ID Cultural";
$specific_css_files = ['busqueda.css'];

include(__DIR__ . '/../components/header.php');
?>

<body>

  <?php include("../components/navbar.php"); ?>

  <!-- 🔘 Botonera de Categorías -->
  <section class="contenedor">
    <h2 class="titulo-seccion">Explorar por Categoría</h2>
    <div class="categorias">
      <?php
      $categorias = ["Danza", "Música", "Arte", "Teatro", "Literatura", "Escultura", "Artesanía", "Audiovisual"];
      foreach ($categorias as $cat) {
        echo '<a href="?categoria=' . urlencode($cat) . '" class="btn-categoria">' . $cat . '</a>';
      }
      ?>
    </div>
  </section>

  <!-- 🎭 Resultados de Búsqueda -->
  <?php
  $categorias_db = [
    "Danza" => "danza",
    "Música" => "musica", 
    "Arte" => "arte",
    "Teatro" => "teatro",
    "Literatura" => "literatura",
    "Escultura" => "escultura", 
    "Artesanía" => "artesania",
    "Audiovisual" => "audiovisual"
  ];

  $obras = [];
  $tituloResultado = "";
  $pagination = null;
  $paginaActual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;

  // Buscar por término de búsqueda (q) o por categoría
  if (isset($_GET['q']) && !empty(trim($_GET['q']))):
    $busqueda = trim($_GET['q']);
    $tituloResultado = htmlspecialchars($busqueda);
    
    try {
      // Contar total de resultados
      $stmtCount = $pdo->prepare("
        SELECT COUNT(*) as total
        FROM publicaciones p
        INNER JOIN artistas a ON p.usuario_id = a.id
        WHERE (p.estado = 'publicada' OR p.estado = 'validado') AND a.status = 'validado' 
        AND (p.titulo LIKE ? OR p.descripcion LIKE ? OR CONCAT(a.nombre, ' ', a.apellido) LIKE ?)
      ");
      $searchTerm = '%' . $busqueda . '%';
      $stmtCount->execute([$searchTerm, $searchTerm, $searchTerm]);
      $totalObrasBusqueda = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
      
      // Crear paginación
      $pagination = new Pagination($totalObrasBusqueda, 12, $paginaActual);
      
      // Buscar en título, descripción y nombre del artista (con paginación)
      $stmt = $pdo->prepare("
        SELECT p.id, p.titulo, p.descripcion, p.multimedia, p.fecha_creacion,
               a.id AS artista_id, CONCAT(a.nombre, ' ', a.apellido) AS artista_nombre,
               a.municipio
        FROM publicaciones p
        INNER JOIN artistas a ON p.usuario_id = a.id
        WHERE (p.estado = 'publicada' OR p.estado = 'validado') AND a.status = 'validado' 
        AND (p.titulo LIKE ? OR p.descripcion LIKE ? OR CONCAT(a.nombre, ' ', a.apellido) LIKE ?)
        ORDER BY p.fecha_creacion DESC
        " . $pagination->getLimitSQL() . "
      ");
      $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
      $obras = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      $obras = [];
      error_log("Error al buscar obras: " . $e->getMessage());
    }
  elseif (isset($_GET['categoria']) && array_key_exists($_GET['categoria'], $categorias_db)):
    $tituloResultado = htmlspecialchars($_GET['categoria']);
    $categoriaDB = $categorias_db[$_GET['categoria']];
    
    try {
      // Contar total de resultados
      $stmtCount = $pdo->prepare("
        SELECT COUNT(*) as total
        FROM publicaciones p
        INNER JOIN artistas a ON p.usuario_id = a.id
        WHERE (p.estado = 'publicada' OR p.estado = 'validado') AND a.status = 'validado' AND p.categoria = ?
      ");
      $stmtCount->execute([$categoriaDB]);
      $totalObrasBusqueda = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
      
      // Crear paginación
      $pagination = new Pagination($totalObrasBusqueda, 12, $paginaActual);
      
      // Obtener obras publicadas de esa categoría (con paginación)
      $stmt = $pdo->prepare("
        SELECT p.id, p.titulo, p.descripcion, p.multimedia, p.fecha_creacion,
               a.id AS artista_id, CONCAT(a.nombre, ' ', a.apellido) AS artista_nombre,
               a.municipio
        FROM publicaciones p
        INNER JOIN artistas a ON p.usuario_id = a.id
        WHERE (p.estado = 'publicada' OR p.estado = 'validado') AND a.status = 'validado' AND p.categoria = ?
        ORDER BY p.fecha_creacion DESC
        " . $pagination->getLimitSQL() . "
      ");
      $stmt->execute([$categoriaDB]);
      $obras = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      $obras = [];
      error_log("Error al buscar obras: " . $e->getMessage());
    }
  endif;

  if (!empty($tituloResultado) || (isset($_GET['categoria']) && !empty($_GET['categoria']))):
  ?>
    <section class="contenedor">
      <h3 class="titulo-resultados">Resultados para: <?= $tituloResultado ?: 'búsqueda' ?></h3>
      <div class="resultados">
        <?php if (count($obras) > 0): ?>
          <?php foreach ($obras as $obra): 
            // Procesar imagen
            $imagen = '/static/img/placeholder-obra.png';
            if (!empty($obra['multimedia'])) {
              $multimedia = json_decode($obra['multimedia'], true);
              if (is_array($multimedia) && !empty($multimedia)) {
                $imagen = $multimedia[0];
              } elseif (is_string($obra['multimedia'])) {
                $imagen = $obra['multimedia'];
              }
            }
          ?>
            <div class="card animate__animated animate__fadeIn">
              <img src="<?= htmlspecialchars($imagen) ?>" 
                   alt="<?= htmlspecialchars($obra['titulo']) ?>" 
                   style="width:100%; height:200px; object-fit:cover;">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($obra['titulo']) ?></h5>
                <p class="card-text"><?= htmlspecialchars(substr($obra['descripcion'], 0, 100)) ?>...</p>
                <small class="text-muted d-block mb-2">
                  <i class="bi bi-person"></i> <?= htmlspecialchars($obra['artista_nombre']) ?><br>
                  <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($obra['municipio'] ?? 'Santiago del Estero') ?>
                </small>
                <a href="/perfil_artista.php?id=<?= $obra['artista_id'] ?>" class="btn-biografia">Ver Perfil del Artista</a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="alert alert-info text-center py-5">
            <i class="bi bi-search"></i>
            <p class="mt-3">No se encontraron resultados para tu búsqueda</p>
          </div>
        <?php endif; ?>
      </div>
      
      <!-- Paginador -->
      <?php if ($pagination && $pagination->getTotalPages() > 1): ?>
        <div style="margin-top: 40px; display: flex; justify-content: center;">
          <?php
            $baseUrl = $_SERVER['REQUEST_URI'];
            $baseUrl = explode('?', $baseUrl)[0]; // Remover query string anterior
            $params = [];
            if (isset($_GET['q'])) $params['q'] = $_GET['q'];
            if (isset($_GET['categoria'])) $params['categoria'] = $_GET['categoria'];
            echo $pagination->renderHTML($baseUrl, $params);
          ?>
        </div>
      <?php endif; ?>
    </section>
  <?php endif; ?>

  <?php include("../components/footer.php"); ?>

  <!-- Scripts -->
  <script src="/static/js/main.js"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
  <script src="/static/js/navbar.js"></script>
</body>
</html>