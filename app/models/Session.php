<?php

namespace models;

class Session
{
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }
    public function getSessionsByUserId($userId){
        $query = "SELECT * FROM user_sessions WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $sessions = [];

        while ($session = $result->fetch_assoc()) {
            $sessions[] = $session; // Добавляем каждую сессию в массив
        }

        return $sessions; // Возвращаем массив с сессиями
    }
    // Добавить новую сессию
    public function addSession($userId, $sessionId, $userAgent, $ipAddress, $location){
        $query = "INSERT INTO user_sessions (user_id, session_id, user_agent, ip_address, last_activity, location) 
              VALUES (?, ?, ?, ?, NOW(), ?)
              ON DUPLICATE KEY UPDATE user_agent = VALUES(user_agent), ip_address = VALUES(ip_address), last_activity = NOW(), location = VALUES(location)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("issss", $userId, $sessionId, $userAgent, $ipAddress, $location);
        return $stmt->execute();
    }

    // Удалить сессию
    public function deleteSession($sessionId){
        $query = "DELETE FROM user_sessions WHERE session_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $sessionId);
        return $stmt->execute();
    }
    public function deleteSessionByUserId($user_id){
        $stmt = $this->conn->prepare("DELETE FROM user_sessions WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }
    // Обновить время последней активности сессии
    public function updateLastActivity($sessionId){
        $query = "UPDATE user_sessions SET last_activity = NOW() WHERE session_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $sessionId);
        return $stmt->execute();
    }
}