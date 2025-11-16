<?php
session_start();
require_once __DIR__ . '/../../../../../config.php';
$page_title = "Registro de Artista - ID Cultural";
// Cargamos un CSS específico para el registro
$specific_css_files = ['registro.css'];
include(__DIR__ . '/../../../../../components/header.php');
?>
<body class="dashboard-body">

  <?php include(__DIR__ . '/../../../../../components/navbar.php'); ?>

  <main class="container my-5">
    <div class="card col-lg-8 mx-auto">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <i class="bi bi-person-plus-fill display-1 text-primary"></i>
                <h1 class="mb-2">Crear Cuenta de Artista</h1>
                <p class="lead">Completa tus datos para formar parte de la comunidad.</p>
            </div>

            <form id="registroForm" novalidate>
                <!-- Datos Personales -->
                <h5 class="mb-3">Datos Personales</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="apellido" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" id="fecha_nacimiento">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="genero" class="form-label">Género</label>
                        <select id="genero" class="form-select">
                            <option value="" selected>Prefiero no decirlo</option>
                            <option value="femenino">Femenino</option>
                            <option value="masculino">Masculino</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Datos de Ubicación -->
                <h5 class="mb-3">Ubicación</h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="pais" class="form-label">País</label>
                        <select id="pais" class="form-select">
                            <option value="Argentina" selected>Argentina</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="provincia" class="form-label">Provincia</label>
                        <select id="provincia" class="form-select">
                            <option value="Santiago del Estero" selected>Santiago del Estero</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="municipio" class="form-label">Municipio</label>
                        <select id="municipio" class="form-select">
                            <option value="" selected disabled>Seleccionar...</option>
                            <option value="Añatuya">Añatuya</option>
                            <option value="Campo Gallo">Campo Gallo</option>
                            <option value="Clodomira">Clodomira</option>
                            <option value="Colonia Dora">Colonia Dora</option>
                            <option value="Fernández">Fernández</option>
                            <option value="Frías">Frías</option>
                            <option value="La Banda">La Banda</option>
                            <option value="Loreto">Loreto</option>
                            <option value="Monte Quemado">Monte Quemado</option>
                            <option value="Pampa de los Guanacos">Pampa de los Guanacos</option>
                            <option value="Quimilí">Quimilí</option>
                            <option value="Santiago del Estero">Santiago del Estero (Capital)</option>
                            <option value="Sumampa">Sumampa</option>
                            <option value="Suncho Corral">Suncho Corral</option>
                            <option value="Termas de Río Hondo">Termas de Río Hondo</option>
                            <option value="Tintina">Tintina</option>
                            <option value="Villa Ojo de Agua">Villa Ojo de Agua</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Datos de Cuenta -->
                <h5 class="mb-3">Datos de la Cuenta</h5>
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="confirm_password" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="confirm_password" required>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Intereses -->
                <h5 class="mb-3">¿Cuáles son tus áreas de interés?</h5>
                <div class="interest-container">
                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="interes-musica" value="musica" name="intereses"><label class="form-check-label" for="interes-musica"><i class="bi bi-music-note-beamed"></i> Música</label></div>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="interes-artes-visuales" value="artes_visuales" name="intereses"><label class="form-check-label" for="interes-artes-visuales"><i class="bi bi-palette-fill"></i> Artes Visuales</label></div>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="interes-letras" value="letras" name="intereses"><label class="form-check-label" for="interes-letras"><i class="bi bi-book-half"></i> Letras</label></div>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="interes-danza" value="danza" name="intereses"><label class="form-check-label" for="interes-danza"><i class="bi bi-person-walking"></i> Danza</label></div>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="interes-teatro" value="teatro" name="intereses"><label class="form-check-label" for="interes-teatro"><i class="bi bi-mask"></i> Teatro</label></div>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="interes-cine" value="cine" name="intereses"><label class="form-check-label" for="interes-cine"><i class="bi bi-film"></i> Cine</label></div>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="interes-artesanias" value="artesanias" name="intereses"><label class="form-check-label" for="interes-artesanias"><i class="bi bi-gem"></i> Artesanías</label></div>
                </div>

                <hr class="my-4">

                <!-- Checkbox de Términos y Condiciones -->
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="acceptTerms" required>
                    <label class="form-check-label" for="acceptTerms">
                        Acepto los <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Términos y Condiciones</a>
                    </label>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" id="submit-button" class="btn btn-primary btn-lg" disabled>Registrarse</button>
                </div>
                
                <p class="text-center mt-3">¿Ya tienes una cuenta? <a href="/src/views/pages/auth/login.php">Inicia sesión aquí</a></p>
            </form>
        </div>
    </div>
  </main>

<!-- Modal de Términos y Condiciones -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="termsModalLabel">Términos y Condiciones de Uso</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php
          // Esta línea incluye el contenido del archivo que ya tienes abierto.
          // Asegúrate de que la ruta sea correcta desde tu archivo registro.php
          include(__DIR__ . '/../terminos_condiciones_content.php'); 
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendido</button>
      </div>
    </div>
  </div>
</div>

  <?php include("../../../../../components/footer.php"); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
  <script>
      const BASE_URL = '<?php echo BASE_URL; ?>';
  </script>
  <script src="<?php echo BASE_URL; ?>static/js/registro.js"></script>

</body>
</html>
