<?php

namespace models;

use Exception;
class Search
{
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
    /**
     * Получить статьи по интересам пользователя
     *
     * @param int $userId ID пользователя
     * @param int $limit Максимальное количество статей
     * @return array Статьи по интересам
     */
    public function getArticlesByUserInterests(int $userId, int $limit = 10, int $offset = 0): array {
        $query = "
    SELECT a.*,
           (COALESCE(ui.interest_level, 0) * (a.views + a.likes)) AS weighted_score
    FROM articles a
    LEFT JOIN user_interests ui
        ON a.category LIKE CONCAT('%', ui.category, '%') AND ui.user_id = ?
    WHERE a.is_published = 1
    ORDER BY weighted_score DESC, a.created_at DESC
    LIMIT ?, ?
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $userId, $offset, $limit);  // привязываем параметры
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }




    /**
     * Получить самые популярные статьи (резервный вариант)
     *
     * @param int $limit Ограничение на количество статей
     * @return array Популярные статьи
     */
    public function getMostPopularArticles(int $limit = 10, int $offset = 0): array {
        $query = "
        SELECT a.*, (a.views + a.likes) AS popularity
        FROM articles a
        WHERE a.is_published = 1
        ORDER BY popularity DESC
        LIMIT ?, ?
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $offset, $limit);  // привязываем параметры
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getPopularWriters($limit = 10, $offset = 0) {
        $query = "SELECT 
                  u.user_id, 
                  u.user_login, 
                  u.user_avatar, 
                  u.user_specialisation,
                  COUNT(f.follower_id) AS followers_count
              FROM users u
              LEFT JOIN followers f ON u.user_id = f.following_id
              GROUP BY u.user_id
              ORDER BY followers_count DESC
              LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $writers = [];
        while ($row = $result->fetch_assoc()) {
            $writers[] = $row;
        }
        return $writers;
    }


}