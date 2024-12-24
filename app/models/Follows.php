<?php

namespace models;

class Follows
{
    public $follower_id;
    public $followed_id;
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }
    // Метод для поиска подписки по follower_id и followed_id
    public function findByFollowerAndFollowed($follower_id, $followed_id) {
        $query = "SELECT * FROM follows WHERE follower_id = ? AND followed_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $follower_id, $followed_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_object(__CLASS__); // Возвращаем объект модели, если есть результат
    }
    // Метод для сохранения новой подписки в базе данных
    public function save() {
        $query = "INSERT INTO follows (follower_id, followed_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $this->follower_id, $this->followed_id);

        return $stmt->execute();
    }
    // Метод для удаления подписки
    public function delete() {
        $query = "DELETE FROM follows WHERE follower_id = ? AND followed_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $this->follower_id, $this->followed_id);

        return $stmt->execute();
    }
    // Метод для получения количества подписчиков
    public function getFollowersCount($user_id) {
        $query = "SELECT COUNT(*) as followers_count FROM follows WHERE followed_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['followers_count'];
    }

    // Метод для получения количества подписок
    public function getFollowingCount($user_id) {
        $query = "SELECT COUNT(*) as following_count FROM follows WHERE follower_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['following_count'];
    }
}