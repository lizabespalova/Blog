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
        // Шаг 1: Получаем интересы пользователя
        $query = "
            SELECT category, interest_level 
            FROM user_interests 
            WHERE user_id = ? 
            ORDER BY interest_level DESC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $interests = $result->fetch_all(MYSQLI_ASSOC);

        if (empty($interests)) {
            return []; // Если нет интересов, возвращаем пустой массив
        }

        // Шаг 2: Собираем статьи по категориям интересов
        $articles = [];
        foreach ($interests as $interest) {
            $category = $interest['category'];
            $query = "
                SELECT a.*, (a.views + a.likes) AS popularity
                FROM articles a
                WHERE a.category LIKE ? 
                AND a.is_published = 1
                ORDER BY popularity DESC
                LIMIT ?
            ";
            $stmt = $this->conn->prepare($query);
            $likeParam = '%' . $category . '%';
            $stmt->bind_param("si", $likeParam, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            $articlesByCategory = $result->fetch_all(MYSQLI_ASSOC);

            $articles = array_merge($articles, $articlesByCategory);

            // Уменьшаем лимит
            $limit -= count($articlesByCategory);
            if ($limit <= 0) {
                break; // Достаточно статей
            }
        }

        return $articles;
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