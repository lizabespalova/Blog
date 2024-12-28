<?php

namespace models;

class Follows
{
    public $follower_id;
    public $following_id;
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }
    // Метод для поиска подписки по follower_id и following_id
    public function findByFollowerAndFollowed($follower_id, $following_id) {
        $query = "SELECT * FROM followers WHERE follower_id = ? AND following_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $follower_id, $following_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Создаем объект Follows вручную, передавая соединение
            $follow = new Follows($this->conn);
            $follow->follower_id = $row['follower_id'];
            $follow->following_id = $row['following_id'];

            return $follow;
        }
        return null; // Если подписки нет, возвращаем null
    }

    // Метод для проверки подписки
    public function isFollowing($follower_id, $following_id) {
        $query = "SELECT 1 FROM followers WHERE follower_id = ? AND following_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $follower_id, $following_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0; // Возвращает true, если запись существует
    }

    // Метод для сохранения новой подписки в базе данных
    public function save() {
        $query = "INSERT INTO followers (follower_id, following_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $this->follower_id, $this->following_id);

        return $stmt->execute();
    }
    // Метод для удаления подписки
    public function delete() {
        $query = "DELETE FROM followers WHERE follower_id = ? AND following_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $this->follower_id, $this->following_id);

        return $stmt->execute();
    }
    // Метод для получения количества подписчиков
    public function getFollowersCount($user_id) {
        $query = "SELECT COUNT(*) as followers_count FROM followers WHERE following_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['followers_count'];
    }

    // Метод для получения количества подписок
    public function getFollowingCount($user_id) {
        $query = "SELECT COUNT(*) as following_count FROM followers WHERE follower_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['following_count'];
    }

    public function getFollowers($userId) {
        $sql = " SELECT 
            u.user_id,
            u.user_login,
            u.user_avatar
        FROM 
            followers f
        INNER JOIN 
            users u ON f.follower_id = u.user_id
        WHERE 
            f.following_id = ?
    ";

        $stmt = $this->conn->prepare($sql); // Подготовка SQL-запроса
        $stmt->bind_param('i', $userId);  // Привязываем параметр $userId как integer
        $stmt->execute();                // Выполняем запрос
        $result = $stmt->get_result();   // Получаем результат

        $followers = [];
        while ($row = $result->fetch_assoc()) {
            $followers[] = $row;         // Преобразуем результат в массив
        }

        $stmt->close();                  // Закрываем подготовленный запрос

        return $followers;
    }

    public function getFollowings($userId)
    {
        $sql = " SELECT 
            u.user_id,
            u.user_login,
            u.user_avatar
        FROM 
            followers f
        INNER JOIN 
            users u ON f.following_id = u.user_id
        WHERE 
            f.follower_id = ?
    ";

        $stmt = $this->conn->prepare($sql); // Подготовка SQL-запроса
        $stmt->bind_param('i', $userId);  // Привязываем параметр $userId как integer
        $stmt->execute();                // Выполняем запрос
        $result = $stmt->get_result();   // Получаем результат

        $followers = [];
        while ($row = $result->fetch_assoc()) {
            $followers[] = $row;         // Преобразуем результат в массив
        }

        $stmt->close();                  // Закрываем подготовленный запрос

        return $followers;
    }
}