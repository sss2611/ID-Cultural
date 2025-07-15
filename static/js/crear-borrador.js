document.getElementById("form-borrador").addEventListener("submit", function (e) {
    e.preventDefault();

    // 1. Selecciona el ícono dentro del botón
    const saveIcon = e.target.querySelector(".fa-save");

    // 2. Añade clases de Animate.css
    saveIcon.classList.add('animate__animated', 'animate__rotateIn');
    
    // Opcional: muestra la alerta después de una breve demora
    setTimeout(() => {
        alert("✅ Borrador guardado correctamente.");
    }, 500);

    // 3. Quita las clases después de que la animación termine para poder reutilizarla
    saveIcon.addEventListener('animationend', () => {
        saveIcon.classList.remove('animate__animated', 'animate__rotateIn');
    });

    
});

// Espera a que el documento esté listo
document.addEventListener('DOMContentLoaded', () => {

    const categoriaSelect = document.getElementById('categoria');
    const todosLosCamposExtra = document.querySelectorAll('.campos-extra');

    // Escucha cada vez que el usuario cambia la categoría
    categoriaSelect.addEventListener('change', function() {
        // 1. Oculta TODOS los campos extra primero
        todosLosCamposExtra.forEach(function(div) {
            div.style.display = 'none';
        });

        // 2. Obtiene el valor seleccionado (ej: "musica")
        const categoriaSeleccionada = this.value;

        if (categoriaSeleccionada) {
            // 3. Construye el ID del div a mostrar (ej: "campos-musica")
            const idDelDivAMostrar = 'campos-' + categoriaSeleccionada;
            const divAMostrar = document.getElementById(idDelDivAMostrar);

            // 4. Si el div existe, lo muestra
            if (divAMostrar) {
                divAMostrar.style.display = 'block'; // O 'flex' si usas flexbox
            }
        }
    });
});