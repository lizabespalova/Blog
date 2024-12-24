function followUser(followedUserId) {
    // Получаем ID текущего пользователя из скрытого поля
    const followerUserId = document.getElementById('user-id').value;

    // Отправка POST-запроса с данными
    fetch(`/follow/${followedUserId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            follower_id: followerUserId,
            followed_id: followedUserId,
        }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Находим кнопку и меняем текст
                const followButton = document.querySelector(`button[onclick="followUser(${followedUserId})"]`);
                followButton.textContent = 'Unfollow';
            } else {
                alert('Ошибка при подписке');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}
