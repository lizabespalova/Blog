// Обработка кнопок Follow/Unfollow/Cancel Request
document.querySelectorAll('.follow-button').forEach(button => {
    button.addEventListener('click', () => {
        const followedUserId = button.getAttribute('data-followed-user-id');
        // const isPrivateProfile = button.getAttribute('data-private') === 'true';
        const actionType = button.getAttribute('data-action-type');

        let actionUrl = '';

        if (actionType === 'unfollow') {
            actionUrl = `/unfollow/${followedUserId}`;
        } else if (actionType === 'cancel-request') {
            actionUrl = `/cancel-follow-request/${followedUserId}`;
        } else {
            actionUrl = `/follow/${followedUserId}`;
        }

        button.disabled = true;

        fetch(actionUrl, {
            method: 'POST',
            body: new URLSearchParams({ followed_user_id: followedUserId }),
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Просто обновляем страницу после успешного действия
                    location.reload();
                } else {
                    throw new Error(data.message || 'Ошибка сервера');
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                window.location.href = "/error?message=" + encodeURIComponent(error.message);
            })
            .finally(() => {
                button.disabled = false;
            });
    });
});
// Применение функции ко всем кнопкам Follow/Unfollow/Cancel Request
// document.querySelectorAll('.follow-button').forEach(button => handleFollowUnfollowButton(button));

// Функция для навигации на другой URL
function navigateTo(url) {
    window.location.href = url;
}
