// Функция для обработки кнопок Follow/Unfollow/Cancel Request
function handleFollowUnfollowButton(button) {
    button.addEventListener('click', function () {
        const followedUserId = button.getAttribute('data-followed-user-id'); // ID пользователя
        const followersCountSpan = document.getElementById('followers-count'); // Счетчик подписчиков
        const isPrivateProfile = button.getAttribute('data-private') === 'true'; // Приватный профиль?
        const currentAction = button.textContent.trim(); // Текущий текст кнопки
        const previousText = button.textContent; // Запоминаем текст перед изменением

        let newActionUrl;
        let newButtonText;

        // Определяем новое действие
        if (currentAction === 'Unfollow' || currentAction === 'Cancel Request') {
            newActionUrl = `/unfollow/${followedUserId}`;
            newButtonText = 'Follow'; // После любого удаления запроса или отписки всегда "Follow"
        } else {
            newActionUrl = `/follow/${followedUserId}`;
            newButtonText = isPrivateProfile ? 'Cancel Request' : 'Unfollow';
        }

        button.disabled = true; // Блокируем кнопку

        // Отправляем запрос
        fetch(newActionUrl, {
            method: 'POST',
            body: new URLSearchParams({ followed_user_id: followedUserId }),
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Устанавливаем новое действие
                    button.textContent = newButtonText;
                    button.setAttribute('data-action', newActionUrl);

                    // Обновляем счетчик подписчиков
                    if (followersCountSpan && !isPrivateProfile) {
                        let currentCount = parseInt(followersCountSpan.textContent, 10);
                        followersCountSpan.textContent = (currentAction === 'Unfollow' || currentAction === 'Cancel Request')
                            ? currentCount - 1
                            : currentCount + 1;
                    }
                } else {

                    throw new Error(data.message || 'Ошибка сервера');
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                button.textContent = previousText; // Восстанавливаем текст при ошибке
                window.location.href = "/error?message=" + error;
            })
            .finally(() => {
                button.disabled = false; // Разблокируем кнопку
            });
    });
}

// Применение функции ко всем кнопкам Follow/Unfollow/Cancel Request
document.querySelectorAll('.follow-button').forEach(button => handleFollowUnfollowButton(button));

// Функция для навигации на другой URL
function navigateTo(url) {
    window.location.href = url;
}
