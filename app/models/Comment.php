<?php

namespace models;

class Comment
{
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
    public function increment_dislike_count($comment_id) {
        $sql = "UPDATE comments SET dislikes = dislikes + 1 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $comment_id);
        $stmt->execute();
        $stmt->close();
    }

    // Метод для уменьшения счетчика дизлайков
    public function decrement_dislike_count($comment_id) {
        $sql = "UPDATE comments SET dislikes = GREATEST(dislikes - 1, 0) WHERE id = ?"; // Не допускаем отрицательных значений
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $comment_id);
        $stmt->execute();
        $stmt->close();
    }
    // Метод для увеличения счетчика лайков
    public function increment_like_count($comment_id) {
        $sql = "UPDATE comments SET likes = likes + 1 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $comment_id);
        $stmt->execute();
        $stmt->close();
    }

    // Метод для уменьшения счетчика лайков
    public function decrement_like_count($comment_id) {
        $sql = "UPDATE comments SET likes = GREATEST(likes - 1, 0) WHERE id = ?"; // Не допускаем отрицательных значений
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $comment_id);
        $stmt->execute();
        $stmt->close();
    }

    public function get_likes_count($comment_id): int
    {
        $comment_id = $this->conn->real_escape_string($comment_id); // Экранируем строку для безопасности
        $sql = "SELECT likes FROM comments WHERE id = '$comment_id'";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return (int)$row['likes'];
        }
        return 0; // Если запись не найдена
    }

    // Метод для получения количества дизлайков
    public function get_dislikes_count($comment_id): int
    {
        $comment_id = $this->conn->real_escape_string($comment_id); // Экранируем строку для безопасности
        $sql = "SELECT dislikes FROM comments WHERE id = '$comment_id'";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return (int)$row['dislikes'];
        }
        return 0; // Если запись не найдена
    }
}