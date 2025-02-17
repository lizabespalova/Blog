<?php

namespace models;

class Notifications
{
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Метод для добавления уведомления
    public function addNotification($userId, $reactionerId, $type, $message, $relatedId = null) {
        if($userId!=$reactionerId) {
            $query = "INSERT INTO notifications (user_id, reactioner_id, type, message, related_id) 
              VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('iisss', $userId, $reactionerId, $type, $message, $relatedId); // 's' для строки
            $stmt->execute();
        }
    }


    public function getUnreadNotifications($userId) {
        $query = "SELECT id, type, message, related_id FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $notifications = [];
        while ($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
        return $notifications;
    }
    public function markAsRead($notificationIds) {
        // Помечаем уведомления как прочитанные
        $query = "UPDATE notifications SET is_read = 1 WHERE id IN (" . implode(',', array_map('intval', $notificationIds)) . ")";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }
    public function getNotificationsByUser($userId) {
        $stmt = $this->conn->prepare("
        SELECT n.id, n.message, n.type, n.reactioner_id, n.status, n.created_at, u.user_login AS reactioner_login, u.user_avatar AS reactioner_avatar
        FROM notifications n
        LEFT JOIN users u ON n.reactioner_id = u.user_id
        WHERE n.user_id = ?
        ORDER BY n.created_at DESC
    ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function deleteNotificationsOlderThan($days) {
        $sql = "DELETE FROM notifications WHERE created_at < NOW() - INTERVAL ? DAY";

        // Подготовка запроса
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Error: " . $this->conn->error);
        }
        // Привязка параметров
        $stmt->bind_param("i", $days);
        // Выполнение запроса
        if (!$stmt->execute()) {
            die("Error: " . $stmt->error);
        }
        // Возвращаем количество удаленных записей
        $deletedRows = $stmt->affected_rows;
        // Закрываем запрос
        $stmt->close();

        return $deletedRows;
    }
    // Обновление статуса уведомления
    public function updateStatus($notificationId, $status)
    {
        $query = "UPDATE notifications SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('si', $status, $notificationId);
        return $stmt->execute();
    }

    function getFollowType( $userId, $followerId) {
        $query = "
        SELECT status 
        FROM notifications 
        WHERE user_id = ? 
          AND reactioner_id = ? 
          AND type = 'follow_request'
        LIMIT 1
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $userId, $followerId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['status']; // Возвращает статус: 'pending', 'approved', или 'rejected'
        }

        return null; // Если записи нет
    }
    public function deleteNotificationsByUserId($user_id){
        $stmt = $this->conn->prepare("DELETE FROM notifications WHERE user_id = ? OR reactioner_id = ?");
        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();
        $stmt->close();

    }
}