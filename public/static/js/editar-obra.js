document.addEventListener('DOMContentLoaded', () => {
    const categoriaSelect = document.getElementById('categoria');
    const camposContainer = document.getElementById('campos-condicionales-container');
    const form = document.getElementById('form-editar-obra');
    const multimediaInput = document.getElementById('multimedia');
    const imagePreviewsContainer = document.getElementById('image-previews');
    const previewContainer = document.getElementById('preview-container');

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
    };

    // Cargar categoría actual y campos
    const categoriasActual = obraData.categoria;
    categoriaSelect.value = categoriasActual;
    camposContainer.innerHTML = camposPorCategoria[categoriasActual] || '';

    // Manejo de preview de nuevas imágenes
    multimediaInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);
        imagePreviewsContainer.innerHTML = '';

        if (files.length > 0) {
            previewContainer.style.display = 'block';
        } else {
            previewContainer.style.display = 'none';
        }

        files.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    const previewHTML = `
                        <div class="col-md-3 col-sm-4 col-6">
                            <div class="card h-100 border-0 shadow-sm position-relative">
                                <img src="${event.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="Preview ${index + 1}">
                                <div class="card-footer bg-light small">
                                    <span class="text-muted">${file.name.substring(0, 15)}...</span>
                                    <button type="button" class="btn btn-sm btn-danger float-end p-0" onclick="removeImage(this)">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    imagePreviewsContainer.insertAdjacentHTML('beforeend', previewHTML);
                };
                reader.readAsDataURL(file);
            }
        });
    });

    categoriaSelect.addEventListener('change', () => {
        const categoria = categoriaSelect.value;
        camposContainer.innerHTML = camposPorCategoria[categoria] || '';
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Mostrar confirmación
        const confirmacion = await Swal.fire({
            title: '¿Estás seguro?',
            html: `
                <div class="text-start">
                    <p>Al guardar los cambios, la obra será <strong>enviada a validación</strong>.</p>
                    <p class="mb-0"><strong>⚠️ Importante:</strong> Durante el proceso de validación, la obra se ocultará de la plataforma y no será visible para otros usuarios hasta que sea aprobada.</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0066cc',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, enviar a validación',
            cancelButtonText: 'Cancelar'
        });

        if (!confirmacion.isConfirmed) {
            return;
        }

        const formData = new FormData();
        formData.append('action', 'update');
        formData.append('id', document.getElementById('obra-id').value);
        formData.append('titulo', document.getElementById('titulo').value);
        formData.append('descripcion', document.getElementById('descripcion').value);
        formData.append('categoria', document.getElementById('categoria').value);

        // Agregar archivos multimedia solo si se seleccionaron nuevos
        const multimediaFiles = document.getElementById('multimedia').files;
        if (multimediaFiles.length > 0) {
            for (let i = 0; i < multimediaFiles.length; i++) {
                formData.append('multimedia[]', multimediaFiles[i]);
            }
        }

        // Recolectar campos extra
        const extraFields = camposContainer.querySelectorAll('input, select, textarea');
        extraFields.forEach(field => {
            formData.append(field.id, field.value);
        });

        try {
            const response = await fetch(`${BASE_URL}api/borradores.php`, {
                method: 'POST',
                body: formData
            });
            
            const contentType = response.headers.get('content-type');
            let result;
            
            if (contentType && contentType.includes('application/json')) {
                result = await response.json();
            } else {
                const text = await response.text();
                console.error('Respuesta no JSON:', text);
                throw new Error('El servidor no devolvió una respuesta JSON válida');
            }

            if (response.ok && result.status === 'ok') {
                Swal.fire({
                    title: '¡Éxito!',
                    text: result.message,
                    icon: 'success',
                    html: `
                        <div class="text-start">
                            <p><strong>${result.message}</strong></p>
                            <p class="mb-0 text-muted small">Tu obra ha sido enviada a validación. Será revisada por nuestro equipo.</p>
                        </div>
                    `
                }).then(() => {
                    window.location.href = `${BASE_URL}src/views/pages/artista/mis-obras-validadas.php`;
                });
            } else {
                Swal.fire('Error', result.message || 'Error desconocido', 'error');
            }
        } catch (error) {
            console.error('Error completo:', error);
            Swal.fire('Error', 'No se pudo conectar con el servidor: ' + error.message, 'error');
        }
    });
});

/**
 * Función para remover una imagen del preview
 */
function removeImage(button) {
    button.closest('.col-md-3').remove();
}
