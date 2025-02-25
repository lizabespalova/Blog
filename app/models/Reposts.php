<?php

namespace models;

class Reposts
{
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
    public function create_repost($userId, $articleId, $message) {
        // Экранируем сообщение, чтобы избежать XSS-атак
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        // Запрос на вставку нового репоста в таблицу reposts
        $query = "INSERT INTO reposts (user_id, article_id, message) VALUES (?, ?, ?)";

        // Подготовка запроса с параметрами
        if ($stmt = $this->conn->prepare($query)) {
            // Привязываем параметры к запросу
            $stmt->bind_param('iis', $userId, $articleId, $message);

            // Выполняем запрос
            if ($stmt->execute()) {
                return ['status' => 'success', 'message' => 'Repost successful'];
            } else {
                return ['status' => 'error', 'message' => 'Failed to create repost'];
            }
        } else {
            return ['status' => 'error', 'message' => 'Failed to prepare query'];
        }
    }
    public function getReposts($userId) {
        $query = "
            SELECT 
                r.id AS repost_id,
                r.message AS message,
                r.created_at AS created_at,
                r.user_id AS user_id,
                a.id AS id,
                a.title AS title,
                a.author AS author,
                a.content AS content,
                a.slug AS slug,
                a.cover_image AS cover_image
            FROM reposts r
            JOIN articles a ON r.article_id = a.id
            WHERE r.user_id = ?
            ORDER BY r.created_at DESC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $reposts = [];
        while ($row = $result->fetch_assoc()) {
            $reposts[] = $row;
        }

        return $reposts;
    }
    public function deleteRepost($repostId) {
        $query = $this->conn->prepare('DELETE FROM reposts WHERE id = ?');
        if ($query) {
            $query->bind_param('i', $repostId); // Привязываем ID как целое число
            $success = $query->execute();
            $query->close();
            return $success;
        }
        return false;
    }
    public function deleteRepostByUserId($userId) {
        $stmt = $this->conn->prepare("DELETE FROM reposts WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
    }
    public function updateRepostMessage($repostId, $userId, $message) {
        $stmt = $this->conn->prepare("UPDATE reposts SET message = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $message, $repostId, $userId);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}