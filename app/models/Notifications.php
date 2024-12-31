<?php

namespace models;

class Notifications
{
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Метод для добавления уведомления
    public function addNotification($userId, $type, $message, $relatedId = null) {
        $query = "INSERT INTO notifications (user_id, type, message, related_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('issi', $userId, $type, $message, $relatedId);
        $stmt->execute();
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

}