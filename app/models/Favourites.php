<?php

namespace models;

use Exception;

class Favourites
{
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Добавить в избранное
    public function add($userId, $articleId) {
        $stmt = $this->conn->prepare("INSERT INTO favorites (user_id, article_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $articleId);
        return $stmt->execute();
    }

    // Удалить из избранного
    public function remove($userId, $articleId) {
        $stmt = $this->conn->prepare("DELETE FROM favorites WHERE user_id = ? AND article_id = ?");
        $stmt->bind_param("ii", $userId, $articleId);
        return $stmt->execute();
    }

    // Проверить, находится ли статья в избранном
    public function exists($userId, $articleId) {
        $stmt = $this->conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND article_id = ?");
        $stmt->bind_param("ii", $userId, $articleId);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
    // Получить список избранных статей пользователя
    public function getAll($userId) {
        $stmt = $this->conn->prepare("SELECT article_id FROM favorites WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $favorites = [];
        while ($row = $result->fetch_assoc()) {
            $favorites[] = $row['article_id'];
        }
        return $favorites;
    }

}