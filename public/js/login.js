document.addEventListener('DOMContentLoaded', () => {
    const inputs = document.querySelectorAll('.login-form input');
    const errorDiv = document.querySelector('.login-form .login-error');

    if (!errorDiv || !inputs.length) {
        return;
    }

    inputs.forEach(input => {
        input.addEventListener('input', () => {
            errorDiv.style.display = 'none';
        });
    });
});
