<?php

use controllers\authorized_users_controllers\ArticleController;
use controllers\authorized_users_controllers\ArticleImagesController;
use controllers\authorized_users_controllers\EditProfileController;
use controllers\authorized_users_controllers\FollowController;
use controllers\authorized_users_controllers\NotificationController;
use controllers\authorized_users_controllers\ProfileController;
use controllers\authorized_users_controllers\SettingsController;
use controllers\search_controllers\SearchController;

require_once __DIR__ . '/../../config/config.php';

function notification_route($uri, $method) {

    $dbConnection = getDbConnection();
    switch ($uri) {
        case '/notifications/reject':
            if ($method === 'POST') {
                $notificationId = $_POST['notification_id'];
                $followerId = $_POST['follower_id'];
                $controller = new NotificationController($dbConnection);
                $controller->rejectRequest($notificationId, $followerId);
            }
            break;
        case '/notifications':
            $controller = new NotificationController($dbConnection);
            if ($method === 'GET') {
                $controller->showNotifications(); // Отображение уведомлений
            }
            exit();  // Остановка выполнения после маршрута

        case '/notifications/cleanup':
            $controller = new NotificationController($dbConnection);
            if ($method === 'POST') {
                $controller->deleteOldNotifications(); // Удаление старых уведомлений
            }
            exit(); // Остановка выполнения после маршрута
        case '/notifications/approve':
            if ($method === 'POST') {
                $notificationId = $_POST['notification_id'];
                $followerId = $_POST['follower_id'];

                $controller = new NotificationController($dbConnection);
                $controller->approveRequest($notificationId, $followerId);
            }
            exit();
        default:
            return false;
    }
}

// Получаем URI и метод запроса
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Запускаем маршрутизатор до вывода содержимого
notification_route($uri, $method);
?>
