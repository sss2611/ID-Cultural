document.addEventListener('DOMContentLoaded', () => {
    const categoriaSelect = document.getElementById('categoria');
    const camposContainer = document.getElementById('campos-condicionales-container');
    const form = document.getElementById('form-borrador');

    const camposPorCategoria = {
        musica: `
            <div class="row">
                <div class="col-md-6 mb-3"><label for="plataformas" class="form-label">Plataformas Digitales</label><input type="text" id="plataformas" class="form-control"></div>
                <div class="col-md-6 mb-3"><label for="sello" class="form-label">Sello Discográfico</label><input type="text" id="sello" class="form-control"></div>
            </div>`,
        literatura: `
            <div class="row">
                <div class="col-md-6 mb-3"><label for="genero-lit" class="form-label">Género Literario</label><input type="text" id="genero-lit" class="form-control"></div>
                <div class="col-md-6 mb-3"><label for="editorial" class="form-label">Editorial</label><input type="text" id="editorial" class="form-control"></div>
            </div>`,
        artes_visuales: `
            <div class="row">
                <div class="col-md-4 mb-3"><label for="tecnica" class="form-label">Técnica/Soporte</label><input type="text" id="tecnica" class="form-control"></div>
                <div class="col-md-4 mb-3"><label for="dimensiones_av" class="form-label">Dimensiones</label><input type="text" id="dimensiones_av" class="form-control"></div>
                <div class="col-md-4 mb-3"><label for="ano_creacion" class="form-label">Año de Creación</label><input type="number" id="ano_creacion" class="form-control"></div>
            </div>`,
        // Añade aquí las plantillas para las otras categorías (escultura, danza, etc.)
    };

    categoriaSelect.addEventListener('change', () => {
        const categoria = categoriaSelect.value;
        camposContainer.innerHTML = camposPorCategoria[categoria] || '';
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const estado = e.submitter.id === 'btn-enviar-validacion' ? 'pendiente' : 'borrador';
        
        // Validaciones básicas
        const titulo = document.getElementById('titulo').value.trim();
        const descripcion = document.getElementById('descripcion').value.trim();
        const categoria = document.getElementById('categoria').value;
        
        if (!titulo || !descripcion || !categoria) {
            Swal.fire('Error', 'Por favor completa todos los campos obligatorios.', 'error');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'save');
        formData.append('titulo', titulo);
        formData.append('descripcion', descripcion);
        formData.append('categoria', categoria);
        formData.append('estado', estado);

        // Agregar archivos multimedia (si los hay)
        const multimediaFiles = document.getElementById('multimedia').files;
        if (multimediaFiles.length > 0) {
            for (let i = 0; i < multimediaFiles.length; i++) {
                formData.append('multimedia[]', multimediaFiles[i]);
            }
        }

        // Recolectar campos extra
        const extraFields = camposContainer.querySelectorAll('input, select, textarea');
        extraFields.forEach(field => {
            if (field.value.trim()) {
                formData.append(field.id, field.value.trim());
            }
        });

        // Mostrar indicador de carga
        Swal.fire({
            title: 'Procesando...',
            text: 'Guardando tu borrador',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            console.log('Enviando formulario a:', `${BASE_URL}api/borradores.php`);
            
            const response = await fetch(`${BASE_URL}api/borradores.php`, {
                method: 'POST',
                body: formData
            });
            
            console.log('Response status:', response.status);
            console.log('Response headers:', [...response.headers.entries()]);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const responseText = await response.text();
            console.log('Raw response:', responseText);
            
            let result;
            try {
                result = JSON.parse(responseText);
            } catch (parseError) {
                console.error('JSON parse error:', parseError);
                throw new Error('La respuesta del servidor no es JSON válido');
            }
            
            console.log('Parsed response:', result);

            if (result.status === 'ok') {
                Swal.fire({
                    title: '¡Éxito!',
                    text: result.message,
                    icon: 'success',
                }).then(() => {
                    window.location.href = `${BASE_URL}src/views/pages/artista/dashboard-artista.php`;
                });
            } else {
                Swal.fire('Error', result.message || 'Error desconocido del servidor', 'error');
            }
        } catch (error) {
            console.error('Error details:', error);
            Swal.fire({
                title: 'Error de Conexión',
                text: 'No se pudo conectar con el servidor. Error: ' + error.message,
                icon: 'error'
            });
        }
    });
});
