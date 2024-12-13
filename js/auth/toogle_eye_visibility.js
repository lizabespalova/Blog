function togglePasswordVisibility(passwordFieldId, iconElement) {
    const passwordField = document.getElementById(passwordFieldId);

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        iconElement.classList.remove('bi-eye-slash');
        iconElement.classList.add('bi-eye');
    } else {
        passwordField.type = 'password';
        iconElement.classList.remove('bi-eye');
        iconElement.classList.add('bi-eye-slash');
    }
}
