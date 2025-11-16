document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm");
  const errorMsg = document.getElementById("mensaje-error");

  if (!form) {
    console.warn("Formulario de login no encontrado");
    return;
  }

  form.addEventListener("submit", async function (e) {
    e.preventDefault();
    console.log("Enviando formulario...");

    const email = document.getElementById("email").value.trim().toLowerCase();
    const password = document.getElementById("password").value.trim();

    const formData = new FormData();
    formData.append("email", email);
    formData.append("password", password);

    try {
      const res = await fetch("/api/login.php", { // Llama al nuevo endpoint
        method: "POST",
        body: formData
      });

      const resultado = await res.json();
      console.log("Respuesta del servidor:", resultado);

      if (resultado.status === "ok") {
        // Redirige usando la URL que viene del backend
        console.log("Redirigiendo a:", resultado.redirect);
        window.location.href = resultado.redirect;
      } else {
        errorMsg.textContent = resultado.message;
        errorMsg.hidden = false;
      }

    } catch (error) {
      console.error("Error al iniciar sesión:", error);
      errorMsg.textContent = "Error de conexión con el servidor.";
      errorMsg.hidden = false;
    }
  });
  // (Removidos los fetch de navbar/footer como antes)
});