let notificationsQueue = []; // Массив для хранения уведомлений

async function fetchNotifications() {
    try {
        const response = await fetch('/api/get_notifications.php');
        const data = await response.json();
        console.log(data);  // Для отладки

        if (data.success && data.notifications.length > 0) {
            // Добавляем новые уведомления в очередь, но не добавляем повторно те, которые уже были отображены
            data.notifications.forEach(notification => {
                // Проверка, если уведомление уже в очереди, не добавлять его снова
                if (!notificationsQueue.some(existingNotification => existingNotification.id === notification.id)) {
                    notificationsQueue.push(notification);
                }
            });
        } else {
            console.log('Нет уведомлений или ошибка:', data);
        }

        // Пытаемся отобразить одно уведомление
        displayNotification();
    } catch (error) {
        console.error("Ошибка при получении уведомлений:", error);
    }
}

function displayNotification() {
    if (notificationsQueue.length > 0) {
        const notificationContainer = document.getElementById('notification-container');
        const notification = notificationsQueue.shift();  // Извлекаем первое уведомление из очереди

        const notificationElement = document.createElement('div');
        notificationElement.className = 'notification';

        let notificationContent = `
            <div class="notification-logo">
                <img src="/templates/images/profile.jpg" alt="Site Logo" class="logo-image">
            </div>
            <div class="notification-text">
                <p>${notification.message}</p>
            </div>
        `;

        // Если есть изображение в уведомлении, добавляем его
        if (notification.image) {
            notificationContent = `
                <div class="notification-logo">
                    <img src="/templates/images/profile.jpg" alt="Site Logo" class="logo-image">
                </div>
                <div class="notification-text">
                    <img src="${notification.image}" alt="Notification image" class="notification-image">
                    <p>${notification.message}</p>
                </div>
            `;
        }

        notificationElement.innerHTML = notificationContent;
        notificationContainer.appendChild(notificationElement);

        // Убираем уведомление через 5 секунд
        setTimeout(() => {
            notificationElement.style.opacity = 0;
            setTimeout(() => notificationElement.remove(), 1000); // Удаляем уведомление после анимации
        }, 5000);
    }
}

// Периодическое обновление
setInterval(fetchNotifications, 5000);
