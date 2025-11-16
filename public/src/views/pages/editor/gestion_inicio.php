<?php
session_start();
require_once __DIR__ . '/../../../../../config.php';

// --- Bloque de seguridad ---
if (!isset($_SESSION['user_data']) || !in_array($_SESSION['user_data']['role'], ['editor', 'admin'])) {
    header('Location: ' . BASE_URL . 'src/views/pages/auth/login.php');
    exit();
}

// --- Variables para el header ---
$page_title = "Editar Página Principal";
$specific_css_files = ['dashboard.css', 'gestion_inicio.css'];

// --- Incluir la cabecera ---
include(__DIR__ . '/../../../../../components/header.php');
?>
<body class="dashboard-body">

    <?php include(__DIR__ . '/../../../../../components/navbar.php'); ?>

    <main class="container my-5">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="mb-0">Editar Página Principal</h1>
                        <p class="lead">Modifica los textos y las imágenes que se muestran en la página de inicio.</p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>src/views/pages/editor/panel_editor.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al Panel
                    </a>
                </div>

                <form id="form-edit-inicio">
                    <!-- Sección de Bienvenida -->
                    <h4 class="mb-3">Sección de Bienvenida</h4>
                    <div class="mb-3">
                        <label class="form-label">Título Principal</label>
                        <div id="editor_welcome_title" style="height: 100px;"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Párrafo de Bienvenida</label>
                        <div id="editor_welcome_paragraph" style="height: 250px; background-image: url('<?php echo BASE_URL; ?>static/img/pattern.png'); background-size: 200px 200px; background-position: center; background-attachment: fixed;"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Eslogan</label>
                        <div id="editor_welcome_slogan" style="height: 100px;"></div>
                    </div>

                    <hr class="my-4">

                    <!-- Sección del Carrusel -->
                    <h4 class="mb-3">Carrusel de Imágenes</h4>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="carousel_image_1" class="form-label">Imagen 1</label>
                            <input class="form-control" type="file" id="carousel_image_1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="carousel_image_2" class="form-label">Imagen 2</label>
                            <input class="form-control" type="file" id="carousel_image_2">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="carousel_image_3" class="form-label">Imagen 3</label>
                            <input class="form-control" type="file" id="carousel_image_3">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary mt-3">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </main>

    <?php include(__DIR__ . '/../../../../../components/footer.php'); ?>
    
    <!-- Quill Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <style>
      /* Estilos para el selector de fuentes */
      .ql-snow .ql-picker.ql-font {
        width: auto;
        min-width: 100px;
      }
      
      .ql-snow .ql-picker.ql-font .ql-picker-label {
        font-family: 'Arial', sans-serif;
        padding: 0 8px;
      }
      
      .ql-snow .ql-picker.ql-font .ql-picker-item {
        padding: 5px 8px;
      }
      
      /* Ocultar los pseudo-elementos que causan problemas */
      .ql-snow .ql-picker.ql-font .ql-picker-label::before,
      .ql-snow .ql-picker.ql-font .ql-picker-item::before {
        display: none !important;
        content: none !important;
      }
      
      /* Mostrar el texto de las opciones */
      .ql-snow .ql-picker.ql-font .ql-picker-label {
        display: flex;
        align-items: center;
        min-height: 26px;
      }
      
      .ql-snow .ql-picker.ql-font .ql-picker-options {
        padding: 5px 0;
        min-width: 120px;
      }
    </style>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
    <script>
        // Configurar fuentes disponibles (web-safe fonts)
        const Font = Quill.import('formats/font');
        Font.whitelist = [
            'sans-serif',
            'serif', 
            'monospace',
            'arial',
            'georgia',
            'verdana',
            'times-new-roman',
            'courier'
        ];
        Quill.register(Font, true);

        // Inicializar Quill para el título
        const quillTitle = new Quill('#editor_welcome_title', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ font: Font.whitelist }],
                    [{ size: ['small', false, 'large', 'huge'] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ color: [] }, { background: [] }],
                    [{ align: [] }],
                    ['clean']
                ]
            }
        });

        // Inicializar Quill para el párrafo
        const quillParagraph = new Quill('#editor_welcome_paragraph', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ font: Font.whitelist }],
                    [{ size: ['small', false, 'large', 'huge'] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ color: [] }, { background: [] }],
                    [{ align: [] }],
                    ['blockquote', 'code-block'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link', 'image'],
                    ['clean']
                ]
            }
        });

        // Inicializar Quill para el eslogan
        const quillSlogan = new Quill('#editor_welcome_slogan', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ font: Font.whitelist }],
                    [{ size: ['small', false, 'large', 'huge'] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ color: [] }, { background: [] }],
                    [{ align: [] }],
                    ['clean']
                ]
            }
        });

        // Configurar etiquetas de fuentes después de que Quill se inicialice
        setTimeout(() => {
            const fontLabels = [
                'Sin serifs',
                'Con serifs',
                'Monoespaciada',
                'Arial',
                'Georgia',
                'Verdana',
                'Times New Roman',
                'Courier'
            ];
            
            // Buscar todos los selectores de fuentes (uno por cada editor)
            const fontPickers = document.querySelectorAll('.ql-picker.ql-font');
            
            fontPickers.forEach(picker => {
                const options = picker.querySelectorAll('.ql-picker-item');
                const label = picker.querySelector('.ql-picker-label');
                
                // Configurar cada opción de fuente
                options.forEach((option, index) => {
                    if (fontLabels[index]) {
                        option.textContent = fontLabels[index];
                        option.setAttribute('data-label', fontLabels[index]);
                    }
                });
                
                // Configurar el label principal
                if (label) {
                    label.textContent = 'Fuente';
                    label.setAttribute('data-label', 'Fuente');
                }
            });
        }, 300);

        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('form-edit-inicio');

            // Cargar datos actuales en el formulario
            async function cargarContenido() {
                try {
                    const response = await fetch('<?php echo BASE_URL; ?>api/site_content.php?action=get');
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    const content = await response.json();
                    
                    quillTitle.root.innerHTML = content.welcome_title || '';
                    quillParagraph.root.innerHTML = content.welcome_paragraph || '';
                    quillSlogan.root.innerHTML = content.welcome_slogan || '';
                } catch (error) {
                    console.error('Error al cargar contenido:', error);
                    Swal.fire('Error', 'No se pudo cargar el contenido actual.', 'error');
                }
            }

            // Enviar formulario para guardar cambios
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                formData.append('action', 'update');
                formData.append('welcome_title', quillTitle.root.innerHTML);
                formData.append('welcome_paragraph', quillParagraph.root.innerHTML);
                formData.append('welcome_slogan', quillSlogan.root.innerHTML);

                try {
                    const response = await fetch('<?php echo BASE_URL; ?>api/site_content.php', {
                        method: 'POST',
                        body: formData
                    });
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    const result = await response.json();

                    if (result.status === 'ok') {
                        Swal.fire('¡Guardado!', result.message, 'success');
                    } else {
                        Swal.fire('Error', result.message, 'error');
                    }
                } catch (error) {
                    console.error('Error al guardar:', error);
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                }
            });

            cargarContenido();
        });
    </script>
</body>
</html>
