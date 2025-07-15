document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("form-noticia");
  const mensaje = document.getElementById("mensaje-confirmacion");
  const tabla = document
    .getElementById("tabla-noticias")
    .querySelector("tbody");

  function cargarNoticias() {
    const lista = JSON.parse(localStorage.getItem("noticiasHome") || "[]");
    tabla.innerHTML = "";

    lista.forEach((noticia) => {
      const fila = document.createElement("tr");
      fila.innerHTML = `
  <td>${noticia.titulo}</td>
  <td>${noticia.contenido}</td>
  <td>${noticia.fecha}</td>
  <td>
    <button class="editar-btn" data-id="${noticia.id}">Editar</button>
    <button class="eliminar-btn" data-id="${noticia.id}">Eliminar</button>
  </td>
`;


      tabla.appendChild(fila);
    });
  }

  function leerImagen(input) {
    return new Promise((resolve) => {
      const file = input.files[0];
      if (!file) return resolve(null);

      const reader = new FileReader();
      reader.onload = () => resolve(reader.result);
      reader.readAsDataURL(file);
    });
  }

  form.addEventListener("submit", async function (e) {
    e.preventDefault();

    const id = document.getElementById("noticia-id").value;
    const titulo = document.getElementById("titulo").value;
    const contenido = document.getElementById("contenido").value;
    const imagenInput = document.getElementById("imagen");
    const imagen = await leerImagen(imagenInput);

    let lista = JSON.parse(localStorage.getItem("noticiasHome") || "[]");

    if (id) {
      const index = lista.findIndex((n) => n.id === id);
      if (index !== -1) {
        lista[index].titulo = titulo;
        lista[index].contenido = contenido;
        if (imagen) lista[index].imagen = imagen;
      }
    } else {
      const nueva = {
        id: crypto.randomUUID(),
        titulo,
        contenido,
        imagen,
        fecha: new Date().toLocaleDateString(),
      };
      lista.push(nueva);
    }

    localStorage.setItem("noticiasHome", JSON.stringify(lista));
    mensaje.hidden = false;
    form.reset();
    document.getElementById("noticia-id").value = "";
    cargarNoticias();
  });

  tabla.addEventListener("click", function (e) {
    if (e.target.classList.contains("editar-btn")) {
      const id = e.target.dataset.id;
      const lista = JSON.parse(localStorage.getItem("noticiasHome") || "[]");
      const noticia = lista.find((n) => n.id === id);

      if (noticia) {
        document.getElementById("noticia-id").value = noticia.id;
        document.getElementById("titulo").value = noticia.titulo;
        document.getElementById("contenido").value = noticia.contenido;
        window.scrollTo(0, 0);
      }
    } else if (e.target.classList.contains("eliminar-btn")) {
      const id = e.target.dataset.id;
      let lista = JSON.parse(localStorage.getItem("noticiasHome") || "[]");
      lista = lista.filter((n) => n.id !== id);
      localStorage.setItem("noticiasHome", JSON.stringify(lista));
      cargarNoticias();
    }
  });

  cargarNoticias();
});
