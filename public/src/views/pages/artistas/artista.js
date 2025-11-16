document.addEventListener("DOMContentLoaded", () => {
  // Este objeto simula los datos que llegarían del backend
  const artista = getArtistaDesdeBackend(); // Aquí más adelante usarás fetch()

  const contenedor = document.getElementById("vista-artista");

  contenedor.innerHTML = `
    <div class="artista-full">
      <div class="cabecera">
        <img src="${artista.foto}" alt="${artista.nombre}" class="foto-perfil" />
        <div class="datos">
          <h2>${artista.nombre}</h2>
          <p><strong>Disciplina:</strong> ${artista.disciplina}</p>
          <p><strong>Localidad:</strong> ${artista.localidad}</p>
          <p class="bio">${artista.biografia}</p>
          <button class="boton-acceso">Regresar a la Wiki</button>
        </div>
      </div>

      <div class="galeria">
        <h2>Obras destacadas</h2>
        <div class="imagenes">
          ${artista.obras.map((obra) => `
            <div class="imagen-wrapper">
              <img src="${obra}" alt="Obra del artista">
            </div>
          `).join("")}
        </div>
      </div>
    </div>
  `;
});

// Función mock para simular backend (reemplazable con fetch después)
function getArtistaDesdeBackend() {
  return {
    nombre: "Lucía Gómez",
    disciplina: "Pintura Mural",
    localidad: "La Banda, Santiago del Estero",
    biografia: "Lucía es una artista autodidacta dedicada a la pintura mural con enfoque social. Ha intervenido más de 30 espacios públicos en barrios populares.",
    foto: "/ID-Cultural/static/img/artista/lucia.png",
    obras: [
      "/ID-Cultural/static/img/artista/1.png",
      "/ID-Cultural/static/img/artista/2.png"
    ]
  };
}

// Lightbox funcional
document.addEventListener("click", (e) => {
  if (e.target.matches(".imagen-wrapper img")) {
    const src = e.target.getAttribute("src");
    document.getElementById("lightbox-img").setAttribute("src", src);
    document.getElementById("lightbox").classList.remove("oculto");
  }

  if (e.target.matches(".cerrar") || e.target.matches("#lightbox")) {
    document.getElementById("lightbox").classList.add("oculto");
  }
});
