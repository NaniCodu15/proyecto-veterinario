// JS para el módulo Login: gestiona la limpieza de mensajes de error en el formulario de acceso.
(function () {
    // Inputs del formulario de inicio de sesión y contenedor de errores mostrados por el backend.
    const inputs = document.querySelectorAll('.login-form input');
    const errorDiv = document.querySelector('.login-form .login-error');
    const toggleButtons = document.querySelectorAll('.toggle-password');

    // Si existe un mensaje de error visible, lo oculta cuando el usuario comienza a escribir nuevamente.
    if (errorDiv) {
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                errorDiv.style.display = 'none';
            });
        });
    }

    toggleButtons.forEach(button => {
        const passwordInput = button.closest('.input-group')?.querySelector('input[type="password"], input[type="text"]');

        if (!passwordInput) return;

        button.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';

            const icon = button.querySelector('i');

            if (icon) {
                icon.classList.toggle('fa-eye', !isPassword);
                icon.classList.toggle('fa-eye-slash', isPassword);
            }
        });
    });
})();
