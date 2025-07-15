// =======================
// LÓGICA DEL LOGIN
// =======================
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm");
  const errorMsg = document.getElementById("mensaje-error");

  const usuariosFijos = [
  {
    email: "admin@cultural.ar",
    password: "admin123",
    rol: "admin",
    redirect: "/ID-Cultural/src/views/pages/user/dashboard-adm.html"
  },
  {
    email: "editor@cultural.ar",
    password: "editor123",
    rol: "editor",
    redirect: "/ID-Cultural/src/views/pages/editor/panel_editor.html"
  },
  {
    email: "validador@cultural.ar",
    password: "validador123",
    rol: "validador",
    redirect: "/ID-Cultural/src/views/pages/auth/Validador.html"
  }
];


  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault(); // Evita que recargue la página

      const usuario = document.getElementById("usuario").value.trim().toLowerCase();
      const clave = document.getElementById("clave").value.trim();

      const usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];

      // Buscar si el usuario existe
      const usuarioEncontrado = usuarios.find(
        (u) => u.email.toLowerCase() === usuario && u.password === clave
      );

      if (usuarioEncontrado) {
        localStorage.setItem("usuarioActivo", usuarioEncontrado.email);
        window.location.href = "/ID-Cultural/src/views/pages/user/dashboard-user.html";
      } else if (usuario === "admin" && clave === "1234") {
        localStorage.setItem("usuarioActivo", "admin");
        window.location.href = "/ID-Cultural/src/views/pages/user/dashboard-adm.html";
      } else if (usuario === "editor" && clave === "1234") {
        localStorage.setItem("usuarioActivo", "editor");
        window.location.href = "/ID-Cultural/src/views/pages/user/dashboard-editor.html";
      } else {
        errorMsg.style.display = "block";
      }
    });
  }

  // =======================
  // CARGA DE COMPONENTES
  // =======================
  fetch("/componentes/navbar.html")
    .then((response) => response.text())
    .then((data) => {
      document.getElementById("navbar").innerHTML = data;
    })
    .catch((err) => {
      console.error("Error al cargar el navbar:", err);
    });

  fetch("/componentes/footer.html")
    .then((response) => response.text())
    .then((data) => {
      document.getElementById("footer").innerHTML = data;
    })
    .catch((err) => {
      console.error("Error al cargar el footer:", err);
    });
});
// =======================
// LÓGICA DEL LOGIN
// =======================
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm");
  const errorMsg = document.getElementById("mensaje-error");

  // Usuarios fijos con roles definidos
  const usuariosFijos = [
    {
      email: "admin@cultural.ar",
      password: "admin123",
      rol: "admin",
      redirect: "/src/views/pages/user/dashboard-adm.html"
    },
    {
      email: "editor@cultural.ar",
      password: "editor123",
      rol: "editor",
      redirect: "/src/views/pages/editor/panel_editor.html"
    },
    {
      email: "validador@cultural.ar",
      password: "validador123",
      rol: "validador",
      redirect: "/src/views/pages/auth/Validador.html"
    }
  ];

  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const usuario = document.getElementById("usuario").value.trim().toLowerCase();
      const clave = document.getElementById("clave").value.trim();

      // Validar primero contra usuarios fijos
      const fijo = usuariosFijos.find(
        (u) => u.email.toLowerCase() === usuario && u.password === clave
      );

      if (fijo) {
        localStorage.setItem("usuarioActivo", fijo.email);
        localStorage.setItem("rolActivo", fijo.rol);
        window.location.href = fijo.redirect;
        return;
      }

      // Validar contra usuarios registrados dinámicamente
      const usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];
      const dinamico = usuarios.find(
        (u) => u.email.toLowerCase() === usuario && u.password === clave
      );

      if (dinamico) {
        localStorage.setItem("usuarioActivo", dinamico.email);
        localStorage.setItem("rolActivo", "user");
        window.location.href = "/src/views/pages/user/dashboard-user.html";
      } else {
        errorMsg.style.display = "block";
      }
    });
  }

  // =======================
  // CARGA DE COMPONENTES
  // =======================
  fetch("/componentes/navbar.html")
    .then((response) => response.text())
    .then((data) => {
      document.getElementById("navbar").innerHTML = data;
    })
    .catch((err) => {
      console.error("Error al cargar el navbar:", err);
    });

  fetch("/componentes/footer.html")
    .then((response) => response.text())
    .then((data) => {
      document.getElementById("footer").innerHTML = data;
    })
    .catch((err) => {
      console.error("Error al cargar el footer:", err);
    });
});
