document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("confirmModal");
    const confirmYes = document.getElementById("confirmYes");
    const confirmNo = document.getElementById("confirmNo");

    let formToSubmit = null; // Сохранение формы для отправки

    window.confirmDelete = function (button) {
        formToSubmit = button.closest("form"); // Получаем форму
        modal.style.display = "flex"; // Показываем модальное окно
    };

    confirmYes.addEventListener("click", function () {
        if (formToSubmit) {
            // Отправка запроса на сервер (например, AJAX)
            fetch(formToSubmit.action, {
                method: 'POST',
                body: new FormData(formToSubmit),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Успех: удаляем пользователя (или соответствующий элемент)
                        formToSubmit.closest('.user').remove(); // Например, удаляем элемент с классом 'user'
                    } else {
                        alert('Ошибка: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                });
        }
        modal.style.display = "none"; // Закрываем окно
    });

    confirmNo.addEventListener("click", function () {
        modal.style.display = "none"; // Закрываем окно
    });

    // Закрытие при клике вне окна
    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});
