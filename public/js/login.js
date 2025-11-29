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

    // Alterna la visibilidad de la contraseña manteniendo el estilo del formulario.
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(button => {
        const targetSelector = button.getAttribute('data-target');
        const targetInput = document.querySelector(targetSelector);
        const icon = button.querySelector('i');

        if (!targetInput || !icon) return;

        button.addEventListener('click', () => {
            const isPassword = targetInput.type === 'password';
            targetInput.type = isPassword ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !isPassword);
            icon.classList.toggle('fa-eye-slash', isPassword);
            button.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
        });
    });
})();
