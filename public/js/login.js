// JS para el módulo Login: gestiona la limpieza de mensajes de error en el formulario de acceso.
(function () {
    // Inputs del formulario de inicio de sesión y contenedor de errores mostrados por el backend.
    const inputs = document.querySelectorAll('.login-form input');
    const errorDiv = document.querySelector('.login-form .login-error');

    // Si existe un mensaje de error visible, lo oculta cuando el usuario comienza a escribir nuevamente.
    if (errorDiv) {
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                errorDiv.style.display = 'none';
            });
        });
    }
})();
