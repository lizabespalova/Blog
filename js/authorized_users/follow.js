// Функция для обработки кнопок Follow/Unfollow
function handleFollowUnfollowButton(button) {
    button.addEventListener('click', function () {
        const actionUrl = button.getAttribute('data-action'); // Получаем URL действия
        const followedUserId = button.getAttribute('data-followed-user-id'); // ID пользователя
        const followersCountSpan = document.getElementById('followers-count'); // Счетчик подписчиков

        const isFollowing = button.textContent.trim() === 'Unfollow'; // Проверяем текущее состояние
        const previousText = button.textContent; // Сохраняем текст кнопки
        button.textContent = isFollowing ? 'Follow' : 'Unfollow'; // Обновляем текст кнопки
        button.disabled = true; // Блокируем кнопку на время запроса

        if (followersCountSpan) {
            const currentCount = parseInt(followersCountSpan.textContent, 10);
            followersCountSpan.textContent = isFollowing ? currentCount - 1 : currentCount + 1; // Обновляем счетчик
        }

        // Отправляем запрос
        fetch(actionUrl, {
            method: 'POST',
            body: new URLSearchParams({ followed_user_id: followedUserId }),
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Обновляем URL действия
                    button.setAttribute('data-action', isFollowing
                        ? `/follow/${followedUserId}`
                        : `/unfollow/${followedUserId}`);
                } else {
                    // Возвращаем предыдущее состояние в случае ошибки
                    button.textContent = previousText;
                    if (followersCountSpan) {
                        const currentCount = parseInt(followersCountSpan.textContent, 10);
                        followersCountSpan.textContent = isFollowing ? currentCount + 1 : currentCount - 1;
                    }
                    alert(data.message || 'Something went wrong!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Восстанавливаем предыдущее состояние при ошибке
                button.textContent = previousText;
                if (followersCountSpan) {
                    const currentCount = parseInt(followersCountSpan.textContent, 10);
                    followersCountSpan.textContent = isFollowing ? currentCount + 1 : currentCount - 1;
                }
            })
            .finally(() => {
                button.disabled = false; // Разблокируем кнопку
            });
    });
}

// Применение функции ко всем кнопкам Follow/Unfollow
document.querySelectorAll('.follow-button').forEach(button => handleFollowUnfollowButton(button));
function navigateTo(url) {
    window.location.href = url;
}