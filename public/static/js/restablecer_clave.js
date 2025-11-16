document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("solicitarCambioClaveForm");
  const mensaje = document.getElementById("mensaje");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const correo = document.getElementById("correo").value;

    // Acá podrías agregar validación adicional

    // Simulación de envío
    mensaje.textContent = "📧 Si el correo está registrado, te enviaremos un enlace para restablecer la contraseña.";
    mensaje.hidden = false;

    form.reset(); // Limpiamos el formulario
  });
});
