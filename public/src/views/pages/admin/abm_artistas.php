<?php
session_start(); // ¡Muy importante!

define('ROOT_PATH', realpath(__DIR__ . '/../../../../'));
require_once(ROOT_PATH . '/config.php');

// Seguridad: solo admin o editor
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'editor'])) {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

// Datos simulados
$artistas = [
    ['id' => 1, 'nombreCompleto' => 'Mercedes Sosa', 'nombreArtistico' => 'La Negra Sosa', 'disciplina' => 'Música', 'estado' => 'fallecido', 'correo' => '', 'informante' => 'Biografía Histórica'],
    ['id' => 2, 'nombreCompleto' => 'Juan Carlos Castagnino', 'nombreArtistico' => 'Castagnino', 'disciplina' => 'Pintura', 'estado' => 'fallecido', 'correo' => '', 'informante' => 'Museo de Arte'],
    ['id' => 3, 'nombreCompleto' => 'Ana Pérez', 'nombreArtistico' => 'Anahí', 'disciplina' => 'Artesanía', 'estado' => 'vivo', 'correo' => 'ana@ejemplo.com', 'informante' => ''],
];

$page_title = "Gestión de Artistas - ID Cultural";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $page_title; ?></title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>static/css/main.css" />
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>static/css/dashboard.css" />
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>static/css/abm_artistas.css" />
</head>
<body>

<?php include(ROOT_PATH . '/components/navbar.php'); ?>

<main>
  <section class="form-section">
    <h2>Gestión de Artistas</h2>

    
  <div class="form-container">
    <form id="form-artista" class="form-grid" method="post" action="#">
      <div class="form-group">
        <label for="nombreCompleto">Nombre Completo:</label>
        <input type="text" id="nombreCompleto" name="nombreCompleto" required />
      </div>
      <div class="form-group">
        <label for="nombreArtistico">Nombre Artístico:</label>
        <input type="text" id="nombreArtistico" name="nombreArtistico" />
      </div>
      <div class="form-group">
        <label for="disciplina">Disciplina:</label>
        <input type="text" id="disciplina" name="disciplina" />
      </div>
      <div class="form-group">
        <label for="estado">Estado:</label>
        <select id="estado" name="estado">
          <option value="vivo">Vivo</option>
          <option value="fallecido">Fallecido</option>
        </select>
      </div>
      <div class="form-group">
        <label for="correo">Correo Electrónico (si está vivo):</label>
        <input type="email" id="correo" name="correo" />
      </div>
      <div class="form-group">
        <label for="informante">Fuente / Informante (si está fallecido):</label>
        <input type="text" id="informante" name="informante" />
      </div>
      <button type="submit">🎨 Registrar Artista</button>
    </form>
    </div>
  </section>

  <section class="tabla-section">
    <h3>Lista de Artistas</h3>
    <table>
      <thead>
        <tr>
          <th>Nombre Completo</th>
          <th>Nombre Artístico</th>
          <th>Disciplina</th>
          <th>Estado</th>
          <th>Correo / Informante</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="tabla-artistas">
        <?php foreach ($artistas as $artista): ?>
          <tr data-id="<?php echo $artista['id']; ?>">
            <td><?php echo htmlspecialchars($artista['nombreCompleto']); ?></td>
            <td><?php echo htmlspecialchars($artista['nombreArtistico']); ?></td>
            <td><?php echo htmlspecialchars($artista['disciplina']); ?></td>
            <td><?php echo htmlspecialchars($artista['estado']); ?></td>
            <td><?php echo htmlspecialchars($artista['estado'] === 'vivo' ? $artista['correo'] : $artista['informante']); ?></td>
            <td>
              <button class="btn-editar">Editar</button>
              <button class="btn-eliminar">Eliminar</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</main>

<?php include(ROOT_PATH . '/components/footer.php'); ?>

<script>
  const initialArtists = <?php echo json_encode($artistas); ?>;
</script>

</body>
</html>
