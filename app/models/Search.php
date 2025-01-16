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
    public function getArticlesByUserInterests(int $userId, int $limit = 10): array {
        $query = "
        SELECT a.*,
               (COALESCE(ui.interest_level, 0) * (a.views + a.likes)) AS weighted_score
        FROM articles a
        LEFT JOIN user_interests ui
            ON a.category LIKE CONCAT('%', ui.category, '%') AND ui.user_id = ?
        WHERE a.is_published = 1
        ORDER BY weighted_score DESC, a.created_at DESC
        LIMIT ?
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $userId, $limit);
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
    public function getMostPopularArticles(int $limit = 10): array {
        $query = "
            SELECT a.*, (a.views + a.likes) AS popularity
            FROM articles a
            WHERE a.is_published = 1
            ORDER BY popularity DESC
            LIMIT ?
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}