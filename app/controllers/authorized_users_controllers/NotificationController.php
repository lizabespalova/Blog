<?php

namespace controllers\authorized_users_controllers;



use models\Follows;
use models\Notifications;
require_once 'app/services/helpers/session_check.php';


class NotificationController
{
    private $notificationModel;
    private $followModel;

    public function __construct($dbConnection) {
        $this->notificationModel = new Notifications($dbConnection);
        $this->followModel = new Follows(getDbConnection());
    }
    public function showNotifications() {
        require_once 'app/services/helpers/switch_language.php';

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
    public function deleteNotifications() {
        $deletedCount = $this->notificationModel->deleteNotificationsOlderThan(0); // Удаление по нажатию кнопкки
        echo json_encode(['success' => true, 'deleted' => $deletedCount]);
    }
    // Утверждение запроса на подписку
    public function approveRequest($notificationId, $followerId)
    {
        // Обновить статус уведомления
        if ($this->notificationModel->updateStatus($notificationId, 'approved')) {
            // Добавить подписку
            $this->followModel->save($followerId, $_SESSION['user']['user_id']);
            $this->followModel->cancelRequest($followerId, $_SESSION['user']['user_id']);
        }
        $this->showNotifications();
         exit();
    }

    // Отклонение запроса на подписку
    public function rejectRequest($notificationId, $followerId)
    {
        $this->notificationModel->updateStatus($notificationId, 'rejected');
        $this->followModel->cancelRequest($followerId, $_SESSION['user']['user_id']);
//        echo json_encode(['success' => true, 'message' => 'Follow request cancelled successfully']);
        $this->showNotifications();
        exit();
    }
}