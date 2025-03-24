// Открытие модального окна с подписчиками
function openSubscribersModal(courseId) {
    // Открываем модальное окно
    document.getElementById('subscribersModal').style.display = 'block';

    // AJAX-запрос для получения подписчиков курса
    fetch(`/courses/get-subscribers/${courseId}`)
        .then(response => response.json())
        .then(data => {
            const subscribersList = document.getElementById('subscribersList');
            subscribersList.innerHTML = ''; // Очищаем список перед добавлением новых подписчиков

            if (data && Array.isArray(data.subscribers) && data.subscribers.length > 0) {
                // Добавляем подписчиков в список
                data.subscribers.forEach(subscriber => {
                    const li = document.createElement('li');
                    li.textContent = `${subscriber.user_login} (${subscriber.user_email})`;
                    subscribersList.appendChild(li);
                });
            } else {
                // Если подписчиков нет, выводим сообщение
                subscribersList.innerHTML = '<li>No subscribers found</li>';
            }
        })
        .catch(error => {
            console.error('Error fetching subscribers:', error);
        });
}

// Закрытие модального окна
function closeSubscribersModal() {
    document.getElementById('subscribersModal').style.display = 'none';
}
