<?php
session_start();
require_once __DIR__ . '/../../../../../config.php';
$page_title = "Login - ID Cultural";
$specific_css_files = ['login.css'];
include(__DIR__ . '/../../../../../components/header.php');
?>
<body class="dashboard-body">

  <?php include(__DIR__ . '/../../../../../components/navbar.php'); ?>

  <main class="login-container">
    <div class="login-box">
      <div class="text-center mb-4">
        <img src="<?php echo BASE_URL; ?>static/img/huella-idcultural.png" alt="Huella ID Cultural" class="logo-huella-login">
      </div>

      <h2 class="mb-4 text-center">Iniciar Sesión</h2>

      <form id="loginForm" novalidate>
        <div class="input-group mb-3">
          <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
          <input type="email" class="form-control" id="email" name="email" placeholder="Correo Electrónico" required>
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="bi bi-eye-slash" id="toggleIcon"></i>
            </button>
        </div>

        <div class="d-grid gap-2 mt-4">
          <button type="submit" class="btn btn-primary btn-lg">Ingresar</button>
        </div>
        
        <div class="d-flex justify-content-between mt-3">
            <a href="/recuperar-clave.php">¿Olvidaste tu contraseña?</a>
            <a href="/src/views/pages/auth/registro.php">Crear una cuenta</a>
        </div>
      </form>
    </div>
  </main>

  <?php include("../../../../../components/footer.php"); ?>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
  <script src="<?php echo BASE_URL; ?>static/js/login.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
        const togglePasswordBtn = document.getElementById('togglePassword');

        if (togglePasswordBtn) {
            togglePasswordBtn.addEventListener('click', () => {
                const passwordInput = document.getElementById('password');
                const icon = document.getElementById('toggleIcon');
                
                // Cambia el tipo de input de 'password' a 'text' y viceversa
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Cambia el icono del ojo
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            });
        }
    });
</script>

</body>
</html>
