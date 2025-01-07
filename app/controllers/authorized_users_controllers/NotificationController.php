<?php

namespace controllers\authorized_users_controllers;



use models\Notifications;
require_once 'app/services/helpers/session_check.php';


class NotificationController
{
    private $notificationModel;

    public function __construct($dbConnection) {
        $this->notificationModel = new Notifications($dbConnection);

    }
    public function showNotifications() {
//        session_start();
//        if (!isset($_SESSION['user'])) {
//            header('Location: /login'); // Перенаправление на страницу входа для неавторизованных пользователей
//            exit();
//        }
        // Получение уведомлений из модели
        $userId = $_SESSION['user']['user_id'];
        $notifications = $this->notificationModel->getNotificationsByUser($userId);

        // Подключение представления
        include __DIR__ . '/../../views/authorized_users/notification_template.php';
    }
    public function deleteOldNotifications() {
        $deletedCount = $this->notificationModel->deleteNotificationsOlderThan(14); // Удаление старше 14 дней
        echo json_encode(['success' => true, 'deleted' => $deletedCount]);
    }

}