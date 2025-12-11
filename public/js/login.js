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

    // Alterna la visibilidad de la contraseña al presionar el ícono de ojo.
    const passwordInput = document.querySelector('#password');
    const togglePasswordBtn = document.querySelector('#toggle-password');

    if (passwordInput && togglePasswordBtn) {
        const icon = togglePasswordBtn.querySelector('i');

        togglePasswordBtn.addEventListener('click', () => {
            const isHidden = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isHidden ? 'text' : 'password');
            togglePasswordBtn.setAttribute('aria-pressed', String(isHidden));

            if (icon) {
                icon.classList.toggle('fa-eye', isHidden);
                icon.classList.toggle('fa-eye-slash', !isHidden);
            }

            passwordInput.focus({ preventScroll: true });
            const caret = passwordInput.value.length;
            passwordInput.setSelectionRange(caret, caret);
        });
    }
})();
