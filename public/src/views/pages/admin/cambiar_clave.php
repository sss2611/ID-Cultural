<?php
define('ROOT_PATH', realpath(__DIR__ . '/../../../../'));
require_once(ROOT_PATH . '/config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'src/views/pages/auth/login.php');
    exit();
}

$page_title = "Restablecer Contraseña - ID Cultural";
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title><?php echo $page_title; ?></title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>static/css/main.css" />
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>static/css/dashboard.css" />
</head>

<body>

  <?php include(ROOT_PATH . '/components/navbar.php'); ?>

  <main class="blanqueo-container">
    <h2>Restablecer Contraseña</h2>
    <p>Ingresa tu correo electrónico para recibir un enlace de cambio de contraseña.</p>

    <div class="form-container">
      <form id="solicitarCambioClaveForm" class="form-vertical" method="post" action="#">
        <div class="form-group">
          <label for="correo">Correo electrónico:</label>
          <input type="email" id="correo" name="correo" required>
        </div>

        <div class="form-group">
          <button type="submit" class="btn-primario">Enviar enlace de cambio</button>
        </div>
      </form>
      <div id="mensaje" class="mensaje" hidden></div>
    </div>
  </main>

  <?php include(ROOT_PATH . '/components/footer.php'); ?>

  <script src="<?php echo BASE_URL; ?>static/js/restablecer_clave.js"></script>
</body>

</html>
