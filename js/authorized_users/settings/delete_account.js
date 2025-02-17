document.querySelector('#delete-account-form').addEventListener('submit', function(event) {
    event.preventDefault();  // Предотвращаем стандартную отправку формы

    let password = document.querySelector('#password').value;

    if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
        // Создаем объект с данными формы
        let formData = new FormData();
        formData.append('password', password);  // Добавляем пароль в форму

        // Отправляем запрос
        fetch('/delete_account', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())  // Получаем ответ в формате JSON
            .then(data => {
                if (data.status === 'success') {
                    // Если операция успешна, показываем сообщение и перенаправляем
                    // alert(data.message);
                    window.location.href = '/search';  // Перенаправление на главную входа
                } else {
                    // Если ошибка, перенаправляем на страницу с ошибкой и сообщением
                    window.location.href = "/error?message=" + encodeURIComponent(data.message);
                }
            })
            .catch(error => {
                console.error('Error during account deletion:', error);
                // window.location.href = "/error?message=" + encodeURIComponent('An error occurred while deleting your account.');
            });
    }
});
