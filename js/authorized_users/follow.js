// Функция для обработки формы Follow/Unfollow
function handleFollowUnfollowForm(form) {
    form.addEventListener('submit', function (event) {
        event.preventDefault(); // Останавливаем стандартное поведение формы

        const actionUrl = form.action; // Текущий action формы
        const formData = new FormData(form); // Данные формы

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

                    // Изменение состояния кнопки и URL действия
                    if (button.textContent.trim() === 'Follow') {
                        button.textContent = 'Unfollow';
                        form.setAttribute('action', actionUrl.replace('/follow/', '/unfollow/'));

                        // Увеличиваем количество подписчиков
                        if (followersCountSpan) {
                            followersCountSpan.textContent = parseInt(followersCountSpan.textContent) + 1;
                        }
                    } else {
                        button.textContent = 'Follow';
                        form.setAttribute('action', actionUrl.replace('/unfollow/', '/follow/'));

                        // Уменьшаем количество подписчиков
                        if (followersCountSpan) {
                            followersCountSpan.textContent = parseInt(followersCountSpan.textContent) - 1;
                        }
                    }
                } else {
                    alert('Something went wrong!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // alert('An error occurred while processing your request.');
            });
    });
}

// Применение функции ко всем формам Follow/Unfollow
document.querySelectorAll('form.follow-unfollow').forEach(form => handleFollowUnfollowForm(form));

function navigateTo(url) {
    window.location.href = url;
}
