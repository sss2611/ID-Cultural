document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registroForm');
    const acceptTermsCheckbox = document.getElementById('acceptTerms');
    const submitButton = document.getElementById('submit-button');

    // Lógica para habilitar/deshabilitar el botón de registro
    acceptTermsCheckbox.addEventListener('change', () => {
        submitButton.disabled = !acceptTermsCheckbox.checked;
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Doble verificación por si el usuario habilita el botón con las herramientas de desarrollador
        if (!acceptTermsCheckbox.checked) {
            Swal.fire('Atención', 'Debes aceptar los Términos y Condiciones para registrarte.', 'warning');
            return;
        }

        const password = document.getElementById('password').value;
        const confirm_password = document.getElementById('confirm_password').value;

        if (password !== confirm_password) {
            Swal.fire('Error', 'Las contraseñas no coinciden.', 'error');
            return;
        }

        const formData = new FormData();
        // Recolectar todos los campos
        formData.append('nombre', document.getElementById('nombre').value);
        formData.append('apellido', document.getElementById('apellido').value);
        formData.append('email', document.getElementById('email').value);
        formData.append('password', password);
        formData.append('confirm_password', confirm_password);
        formData.append('fecha_nacimiento', document.getElementById('fecha_nacimiento').value);
        formData.append('genero', document.getElementById('genero').value);
        formData.append('pais', document.getElementById('pais').value);
        formData.append('provincia', document.getElementById('provincia').value);
        formData.append('municipio', document.getElementById('municipio').value);

        // Recolectar los intereses seleccionados
        const interesesSeleccionados = [];
        document.querySelectorAll('input[name="intereses"]:checked').forEach((checkbox) => {
            interesesSeleccionados.push(checkbox.value);
        });
        formData.append('intereses', JSON.stringify(interesesSeleccionados));
        formData.append('action', 'register');

        try {
            const response = await fetch(`${BASE_URL}api/artistas.php`, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (response.ok && result.status === 'ok') {
                Swal.fire({
                    title: '¡Registro Exitoso!',
                    text: result.message,
                    icon: 'success',
                    confirmButtonText: 'Ok'
                }).then(() => {
                    window.location.href = `${BASE_URL}src/views/pages/auth/login.php`;
                });
            } else {
                Swal.fire('Error en el Registro', result.message, 'error');
            }
        } catch (error) {
            Swal.fire('Error de Conexión', 'No se pudo conectar con el servidor.', 'error');
        }
    });
});
