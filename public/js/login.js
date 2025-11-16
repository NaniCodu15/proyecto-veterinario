(function () {
    const inputs = document.querySelectorAll('.login-form input');
    const errorDiv = document.querySelector('.login-form .login-error');

    if (errorDiv) {
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                errorDiv.style.display = 'none';
            });
        });
    }
})();
