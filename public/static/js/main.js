// ==========================================================
// ARCHIVO: main.js - VERSIÓN FINAL CORREGIDA
// ==========================================================

// ✅ Inclusión dinámica de HTML (Tu función original - sin cambios)
async function includeHTML(id, file) {
  const el = document.getElementById(id);
  if (el) {
    try {
      const res = await fetch(file);
      if (!res.ok) throw new Error("No se pudo cargar el archivo: " + file);
      const html = await res.text();
      el.innerHTML = html;
    } catch (error) {
      console.error("Error al incluir HTML:", error);
    }
  }
}

// ✅ Función para truncar texto y que no rompa el diseño
function truncarTexto(texto, longitud) {
  // Elimina etiquetas HTML para un conteo de caracteres más preciso
  const textoPlano = texto.replace(/<[^>]*>?/gm, '');
  if (textoPlano.length <= longitud) {
    return texto; // Si el texto original es corto, lo devolvemos con su HTML
  }
  // Si es largo, truncamos el texto plano y añadimos "..."
  return textoPlano.slice(0, longitud).trim() + '...';
}


// ✅ Renderizado de noticias en el home (FUNCIÓN CORREGIDA)
function cargarNoticiasHome() {
  const contenedor = document.getElementById("contenedor-noticias");
  if (!contenedor) return;

  // Limpiamos el contenedor para evitar duplicados
  contenedor.innerHTML = ''; 

  const lista = JSON.parse(localStorage.getItem("noticiasHome") || "[]");
  // Tu lógica para obtener las últimas 3 es correcta
  const ultimas = lista.slice(-3).reverse();

  if (ultimas.length === 0) {
    contenedor.innerHTML = '<p class="text-center col-12 text-muted">No hay noticias disponibles.</p>';
    return;
  }

  ultimas.forEach(noticia => {
    // === PASO 1: CREAR LA COLUMNA DE BOOTSTRAP (LA "CAJA") ===
    const columna = document.createElement("div");
    // Esto le dice a Bootstrap: ocupa 4 de 12 columnas en pantallas grandes (3 por fila)
    columna.classList.add("col-lg-4", "col-md-6", "col-12", "mb-4");

    // === PASO 2: CREAR TU TARJETA DE NOTICIA ===
    const card = document.createElement("div");
    card.classList.add("noticia-card"); // Usamos tu clase .noticia-card

    // Truncamos el contenido para un mejor diseño
    const contenidoCorto = truncarTexto(noticia.contenido, 120);

    // === PASO 3: RELLENAR LA TARJETA CON LA INFORMACIÓN ===
    card.innerHTML = `
      ${noticia.imagen ? `<img src="${noticia.imagen}" alt="Imagen para ${noticia.titulo}">` : ""}
      <h3>${noticia.titulo}</h3>
      <p>${contenidoCorto}</p>
      <small class="mt-auto">Fecha: ${new Date(noticia.fecha).toLocaleDateString()}</small>
    `;

    // === PASO 4: PONER LA TARJETA DENTRO DE LA COLUMNA ===
    columna.appendChild(card);

    // === PASO 5: PONER LA COLUMNA (CON LA TARJETA DENTRO) EN EL CONTENEDOR ===
    contenedor.appendChild(columna);
  });
}


// ✅ Cargar todo cuando el DOM esté listo (Tu código original)
document.addEventListener("DOMContentLoaded", () => {
  // Recordatorio: Estas líneas son innecesarias si index.php ya incluye el header/footer.
  // No son la causa del error, pero es una buena práctica eliminarlas para evitar doble carga.
  includeHTML("navbar", "/ID-Cultural/src/views/pages/public/components/navbar.html");
  includeHTML("footer", "/ID-Cultural/src/views/pages/public/components/footer.html");

  cargarNoticiasHome();
});