document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const userKey = urlParams.get('user_key'); // Получаем токен из URL

    if (userKey) {
        // Отправляем запрос на подтверждение email
        fetch(`/update-email?user_key=${userKey}`, {
            method: 'GET',
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
                    }).then(() => {
                        // Перенаправляем пользователя на страницу настроек
                        window.location.href = '/settings';
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
    }
});
