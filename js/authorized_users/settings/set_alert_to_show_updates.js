document.getElementById('personal-data-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Предотвращаем обычную отправку формы

    const formData = new FormData(this);

    // Отправляем форму через AJAX
    fetch('/settings/update-user', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json()) // Преобразуем ответ в JSON
        .then(data => {
            if (data.success) {
                // Если обновление прошло успешно
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#28a745' // Зеленый цвет для успеха
                });
            } else {
                // Если произошла ошибка
                Swal.fire({
                    title: 'Error!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonColor: '#dc3545' // Красный цвет для ошибки
                });
            }
        })
        .catch(error => {
            // Если произошла ошибка при отправке запроса
            Swal.fire({
                title: 'Error!',
                text: 'An unexpected error occurred. Please try again.' + error,
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
        });
});
