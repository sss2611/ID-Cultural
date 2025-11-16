<?php
session_start();
require_once __DIR__ . '/../../../../../config.php';
$page_title = "Términos y Condiciones - ID Cultural";
// Puedes crear un CSS específico si lo necesitas
// $specific_css_files = ['terminos.css']; 
include(__DIR__ . '/../../../../../components/header.php');
?>
<body class="dashboard-body">

  <?php include(__DIR__ . '/../../../../../components/navbar.php'); ?>

  <main class="container my-5">
    <div class="card">
        <div class="card-body p-4 p-md-5">
            <h1>Términos y Condiciones de Uso - ID Cultural</h1>
            <hr>

            <h2>1. Condiciones Generales de Uso</h2>
            <p>Antes de utilizar cualquier servicio ofrecido en la plataforma ID Cultural (en adelante, la "Plataforma"), por favor lee atentamente estos términos y condiciones. El uso de la Plataforma implica que el usuario ha leído, entendido y aceptado íntegramente este acuerdo.</p>
            <p>El acceso y uso de la Plataforma atribuye la condición de "Usuario/a" e implica la aceptación expresa y voluntaria de estos Términos y Condiciones.</p>
            <p><strong>EL USUARIO DECLARA TENER CAPACIDAD LEGAL PARA CONTRATAR.</strong> SI NO ESTÁ DE ACUERDO CON ESTOS TÉRMINOS, DEBE ABSTENERSE DE UTILIZAR LA PLATAFORMA.</p>

            <h2 class="mt-4">2. Modificaciones</h2>
            <p>ID Cultural se reserva el derecho de modificar estos términos en cualquier momento. Las modificaciones entrarán en vigencia desde su publicación. El uso continuo de la Plataforma por parte del Usuario implica la aceptación de las condiciones actualizadas.</p>

            <h2 class="mt-4">3. Funcionamiento de la Plataforma</h2>
            <p>ID Cultural es una plataforma gratuita creada con el objetivo de visibilizar, promocionar y conectar artistas culturales.</p>
            
            <h3 class="mt-3">a. Registro</h3>
            <p>Para utilizar la Plataforma, el usuario debe registrarse y proporcionar los siguientes datos: nombre, apellido, correo electrónico, contraseña, fecha de nacimiento y áreas de interés cultural. Al registrarse, el usuario acepta los presentes términos y condiciones.</p>
            
            <h3 class="mt-3">b. Perfil de Artista</h3>
            <p>Una vez registrada y validada la cuenta, el usuario podrá crear su perfil artístico, donde podrá describir su proyecto cultural, adjuntar material, compartir logros y crear un portafolio virtual.</p>

            <!-- ... (El resto de tu texto de términos y condiciones) ... -->

            <h2 class="mt-4">8. Contacto</h2>
            <p>Cualquier comunicación referida a estos Términos y Condiciones deberá enviarse a: <a href="mailto:dnicultural.contacto@gmail.com">dnicultural.contacto@gmail.com</a></p>
        </div>
    </div>
  </main>

  <?php include("../../../../../components/footer.php"); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
