<?php

require_once __DIR__ . '/../app/models/Notifications.php';
require __DIR__ . '/../config/config.php';

use models\Notifications;

session_start();

if (!isset($_SESSION['user'])) {
    // Возврат JSON вместо перенаправления
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Session expired']);
    exit();
}

$user = $_SESSION['user'];
$userId = $user['user_id']; // ID текущего пользователя
$notification = new Notifications(getDbConnection());

// Получение непрочитанных уведомлений
$notifications = $notification->getUnreadNotifications($userId);

// Сразу помечаем уведомления как прочитанные
if (!empty($notifications)) {
    $notificationIds = array_column($notifications, 'id'); // Получаем массив ID уведомлений
    $notification->markAsRead($notificationIds);
}

echo json_encode(['success' => true, 'notifications' => $notifications]);

?>