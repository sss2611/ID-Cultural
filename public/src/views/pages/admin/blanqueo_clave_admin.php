<?php
// 1. Definimos la ruta raíz y cargamos la configuración
define('ROOT_PATH', realpath(__DIR__ . '/../../../../'));
require_once(ROOT_PATH . '/config.php');

// 2. Lógica de seguridad: solo los administradores pueden estar aquí
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

$page_title = "Blanqueo de Clave - ID Cultural";
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

<?php
  // 3. Incluimos el navbar desde su ubicación REAL
  include(ROOT_PATH . '/components/navbar.php');
  ?>

    <main class="blanqueo-container">
        <h2>Blanqueo de Clave (Administrador)</h2>
        <p>Ingresa el correo del usuario para reestablecer su contraseña a una nueva clave aleatoria.</p>
        <form id="blanqueoForm">
            <label for="email">Correo del Usuario a Blanquear:</label>
            <input type="email" id="email" name="email" required>
            <input type="submit" value="Blanquear Clave">
        </form>
        <div id="mensaje" class="mensaje" hidden></div>
    </main>

    <?php
  // 4. Incluimos el footer desde su ubicación REAL
  include(ROOT_PATH . '/components/footer.php');
  ?>

    <script src="<?php echo BASE_URL; ?>static/js/blanqueo_clave.js"></script>
</body>
</html>