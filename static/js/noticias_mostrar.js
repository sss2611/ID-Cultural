document.addEventListener("DOMContentLoaded", () => {
  const contenedor = document.getElementById("contenedor-noticias");
  const lista = JSON.parse(localStorage.getItem("noticiasHome") || "[]");

  contenedor.innerHTML = "";

  lista.forEach(noticia => {
    const card = document.createElement("div");
    card.classList.add("noticia-card");

    card.innerHTML = `
      ${noticia.imagen ? `<img src="${noticia.imagen}" alt="Imagen de la noticia">` : ""}
      <h3>${noticia.titulo}</h3>
      <p>${noticia.contenido}</p>
      <small>${noticia.fecha}</small>
    `;

    contenedor.appendChild(card);
  });
});
