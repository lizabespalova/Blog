let notificationsQueue = []; // Массив для хранения уведомлений

document.addEventListener('DOMContentLoaded', () => {
    // Проверяем поддержку уведомлений
    if ('Notification' in window) {
        if (Notification.permission === 'default') {
            Notification.requestPermission().then(permission => {
                console.log(`Permission: ${permission}`);
            });
        }
    } else {
        console.error("Уведомления не поддерживаются этим браузером.");
    }

    const notificationId = getNotificationIdFromURL();
    if (notificationId) {
        highlightNotification(notificationId);
    }
});

function getNotificationIdFromURL() {
    const params = new URLSearchParams(window.location.search);
    return params.get('id'); // Вернет значение параметра id
}

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
        const notification = notificationsQueue.shift(); // Извлекаем первое уведомление из очереди

        // Проверяем разрешение
        if (Notification.permission === 'granted') {
            const notificationOptions = {
                body: notification.message,
                icon: '/templates/images/profile.jpg',
                tag: notification.id, // Для предотвращения дублирования уведомлений
            };
            const desktopNotification = new Notification("New notification", notificationOptions);

            // Переход по клику
            desktopNotification.onclick = () => {
                window.open(`/notifications?id=${notification.id}`, '_blank');
            };

            // Автоматическое скрытие через 5 секунд
            setTimeout(() => desktopNotification.close(), 5000);
        } else {
            console.warn("Нет разрешения на показ уведомлений.");
        }
    }
}

function highlightNotification(notificationId) {
    // Найдем уведомление по его ID
    const notification = document.querySelector(`.notification-item[data-id="${notificationId}"]`);

    // Добавим класс для подсветки
    if (notification) {
        notification.classList.add('highlighted');

        // Через 2 секунды удалим подсветку
        setTimeout(() => {
            notification.classList.remove('highlighted');
        }, 2000);
    }
}

// Периодическое обновление
setInterval(fetchNotifications, 5000);
