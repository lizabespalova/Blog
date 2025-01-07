async function cleanupOldNotifications() {
    try {
        const response = await fetch('/notifications/cleanup', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
        });
        const data = await response.json();
        console.log('Old notifications were removed:', data.deleted, 'notifications');
    } catch (error) {
        console.error('Error while deleting old notifications:', error);
    }
}

// Запускаем очистку раз в сутки
setInterval(cleanupOldNotifications, 24 * 60 * 60 * 1000); // 1 раз в день
