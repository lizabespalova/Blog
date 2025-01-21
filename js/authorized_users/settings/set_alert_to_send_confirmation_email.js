document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('personal-data-form');
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(form);
        const login = formData.get('login');
        const email = formData.get('email');
        const password = formData.get('password');

        // Отправка данных на сервер
        fetch('/settings/update-user', {
            method: 'POST',
            body: JSON.stringify({ login, email, password }),
            headers: {
                'Content-Type': 'application/json',
            },
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Успешная отправка данных
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#28a745',
                    });
                } else {
                    // Ошибка
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonColor: '#dc3545',
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'An unexpected error occurred. Please try again.' + error,
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                });
            });
    });
});
