// Захват всех форм с классом follow-unfollow
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Останавливаем стандартное поведение формы

        // Получаем текущий action
        const actionUrl = form.action;

        // Собираем данные формы
        const formData = new FormData(form);

        // Отправляем запрос
        fetch(actionUrl, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json()) // Ожидаем JSON
            .then(data => {
                if (data.success) {
                    const button = form.querySelector('button');
                    const followersCountSpan = document.getElementById('followers-count');

                    // Изменяем текст кнопки и action
                    if (button.textContent.trim() === 'Follow') {
                        button.textContent = 'Unfollow';
                        form.setAttribute('action', actionUrl.replace('/follow/', '/unfollow/'));

                        // Увеличиваем число подписчиков на 1
                        if (followersCountSpan) {
                            followersCountSpan.textContent = parseInt(followersCountSpan.textContent) + 1;
                        }
                    } else {
                        button.textContent = 'Follow';
                        form.setAttribute('action', actionUrl.replace('/unfollow/', '/follow/'));

                        // Уменьшаем число подписчиков на 1
                        if (followersCountSpan) {
                            followersCountSpan.textContent = parseInt(followersCountSpan.textContent) - 1;
                        }
                    }
                } else {
                    alert('Something went wrong!');
                }
            });
    });
});
