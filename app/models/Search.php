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
               u.user_login, 
               u.user_avatar, 
               (COALESCE(ui.interest_level, 0) * (a.views + a.likes)) AS weighted_score
        FROM articles a
        LEFT JOIN user_interests ui
            ON a.category LIKE CONCAT('%', ui.category, '%') 
            AND ui.user_id = ?  -- Здесь передается ID пользователя
        LEFT JOIN users u
            ON a.user_id = u.user_id  -- Соединение с таблицей пользователей, чтобы получать информацию о пользователях
        WHERE a.is_published = 1
        ORDER BY weighted_score DESC, a.created_at DESC
        LIMIT ?, ?  -- Параметры для лимита и смещения
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
        SELECT a.*, 
               u.user_login, 
               u.user_avatar, 
               (a.views + a.likes) AS popularity
        FROM articles a
        LEFT JOIN users u
            ON a.user_id = u.user_id  -- Соединение с таблицей пользователей
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
    public function getPopularCourses($limit = 10, $offset = 0) {
        // Запрос для получения популярных курсов по количеству лайков
        $query = "SELECT 
                  c.*, 
                  u.user_login, 
                  COUNT(cr.id) AS likes_count  -- Подсчитаем количество лайков
              FROM courses c
              LEFT JOIN users u ON c.user_id = u.user_id  -- Автор курса
              LEFT JOIN course_reactions cr ON c.course_id = cr.course_id AND cr.reaction = 'like'  -- Только лайки
              GROUP BY c.course_id  -- Группируем по ID курса
              ORDER BY likes_count DESC  -- Сортируем по количеству лайков
              LIMIT ? OFFSET ?";  // Пагинация

        // Подготавливаем запрос
        $stmt = $this->conn->prepare($query);

        // Привязываем параметры для пагинации
        $stmt->bind_param("ii", $limit, $offset);

        // Выполняем запрос
        $stmt->execute();

        // Получаем результаты
        $result = $stmt->get_result();

        // Массив для хранения курсов
        $courses = [];

        // Извлекаем данные из результата
        while ($row = $result->fetch_assoc()) {
            // Добавляем курс в массив
            $courses[] = $row;
        }

        // Возвращаем список популярных курсов
        return $courses;
    }

    public function searchAll($query) {
        return [
            "articles" => $this->searchArticles($query),
            "courses" => $this->searchCourses($query),
            "writers" => $this->searchWriters($query)
        ];
    }

    public function searchArticles($query) {
        $sql = "SELECT 
                a.*, 
                u.user_login, 
                u.user_avatar 
            FROM articles a
            JOIN users u ON a.user_id = u.user_id
            WHERE (a.title LIKE ? OR a.content LIKE ?) 
            AND a.is_published = 1 
            LIMIT 10";

        $stmt = $this->conn->prepare($sql);
        $likeQuery = "%$query%";
        $stmt->bind_param("ss", $likeQuery, $likeQuery);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    public function searchCourses($query) {
        $sql = "SELECT c.*, 
                   s.hide_email 
            FROM courses c
            LEFT JOIN settings s ON c.user_id = s.user_id
            WHERE (c.title LIKE ? OR c.description LIKE ?)
            LIMIT 10";

        return $this->executeQuery($sql, "%$query%");
    }



    public function searchWriters($query) {
        $sql = "SELECT user_id, user_login, user_avatar, user_specialisation 
                FROM users 
                WHERE user_login LIKE ? OR user_specialisation LIKE ? 
                LIMIT 10";
        return $this->executeQuery($sql, $query);
    }

    private function executeQuery($sql, $query) {
        $stmt = $this->conn->prepare($sql);
        $searchTerm = "%$query%";
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}