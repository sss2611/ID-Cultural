<?php
// Recuperar contraseña
session_start();

// Si el usuario ya está logueado, redirigir
if (isset($_SESSION['user_data'])) {
    header('Location: /index.php');
    exit;
}

$token = $_GET['token'] ?? '';
$paso = 1; // 1: solicitar email, 2: cambiar contraseña

if ($token) {
    $paso = 2;
}

require_once __DIR__ . '/../config.php';
$page_title = "Recuperar Contraseña - ID Cultural";
$specific_css_files = ['login.css'];

include(__DIR__ . '/../components/header.php');
?>

<body>
  <?php include("../components/navbar.php"); ?>

  <main class="main-content">
    <div class="login-container">
      <div class="login-box">
        <h1>Recuperar Contraseña</h1>

        <?php if ($paso === 1): ?>
          <!-- PASO 1: Solicitar email -->
          <form id="form-solicitar-email" class="login-form">
            <div class="form-group">
              <label for="email">Email</label>
              <input 
                type="email" 
                id="email" 
                name="email" 
                required
                placeholder="tu@email.com"
              >
            </div>

            <button type="submit" class="btn-login">
              Enviar Enlace de Recuperación
            </button>

            <p class="form-footer">
              ¿Recuerdas tu contraseña? 
              <a href="/login.php">Inicia sesión aquí</a>
            </p>
          </form>

        <?php else: ?>
          <!-- PASO 2: Cambiar contraseña -->
          <form id="form-cambiar-clave" class="login-form">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="form-group">
              <label for="nueva-clave">Nueva Contraseña</label>
              <input 
                type="password" 
                id="nueva-clave" 
                name="nueva_clave" 
                required
                minlength="6"
                placeholder="Mínimo 6 caracteres"
              >
            </div>

            <div class="form-group">
              <label for="confirmar-clave">Confirmar Contraseña</label>
              <input 
                type="password" 
                id="confirmar-clave" 
                name="confirmar_clave" 
                required
                minlength="6"
                placeholder="Repite tu contraseña"
              >
            </div>

            <button type="submit" class="btn-login">
              Actualizar Contraseña
            </button>

            <p class="form-footer">
              <a href="/login.php">Volver al login</a>
            </p>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <?php include("../components/footer.php"); ?>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>

  <script>
    // Formulario 1: Solicitar email
    const formEmail = document.getElementById('form-solicitar-email');
    if (formEmail) {
      formEmail.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const email = document.getElementById('email').value.trim();
        
        try {
          const res = await fetch('/api/solicitar_recuperacion_clave.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'email=' + encodeURIComponent(email)
          });
          
          const data = await res.json();
          
          if (res.ok && data.success) {
            Swal.fire({
              title: '✓ Email Enviado',
              text: data.mensaje,
              icon: 'success',
              confirmButtonText: 'OK'
            }).then(() => {
              window.location.href = '/src/views/pages/auth/login.php';
            });
          } else {
            Swal.fire('Error', data.error || 'Error al procesar', 'error');
          }
        } catch (err) {
          console.error(err);
          Swal.fire('Error', 'Error de conexión', 'error');
        }
      });
    }

    // Formulario 2: Cambiar contraseña
    const formClave = document.getElementById('form-cambiar-clave');
    if (formClave) {
      formClave.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const token = document.querySelector('input[name="token"]').value;
        const nueva = document.getElementById('nueva-clave').value;
        const confirmar = document.getElementById('confirmar-clave').value;
        
        if (nueva !== confirmar) {
          Swal.fire('Error', 'Las contraseñas no coinciden', 'error');
          return;
        }
        
        if (nueva.length < 6) {
          Swal.fire('Error', 'La contraseña debe tener mínimo 6 caracteres', 'error');
          return;
        }
        
        try {
          const res = await fetch('/api/cambiar_clave_token.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'token=' + encodeURIComponent(token) + 
                  '&nueva_clave=' + encodeURIComponent(nueva)
          });
          
          const data = await res.json();
          
          if (res.ok && data.success) {
            Swal.fire({
              title: '✓ Contraseña Actualizada',
              text: 'Ya puedes ingresar con tu nueva contraseña',
              icon: 'success',
              confirmButtonText: 'Ir a Login'
            }).then(() => {
              window.location.href = '/src/views/pages/auth/login.php';
            });
          } else {
            Swal.fire('Error', data.error || 'Error al actualizar', 'error');
          }
        } catch (err) {
          console.error(err);
          Swal.fire('Error', 'Error de conexión', 'error');
        }
      });
    }
  </script>
</body>
</html>
