<?php


namespace models;

use Exception;

class Articles
{
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
    public function prepare_article_params($inputData, $coverImagePath)
    {
        // Массив данных для статьи
        $params = [
            $inputData['title'] ?? '',
            $inputData['content'] ?? '',
            $inputData['author'] ?? '',
            $coverImagePath,
            $inputData['youtube_link'] ?? '',
            $inputData['category'] ?? '',
            $inputData['difficulty'] ?? '',
            $inputData['read_time'] ?? '',
            $inputData['tags'] ?? '',
        ];

        // Устанавливаем NULL для пустых значений
        foreach ($params as &$param) {
            if ($param === '') {
                $param = null;
            }
        }
        return $params;
    }

    public function add_article($inputData, $coverImagePath)
    {
        $stmt = $this->conn->prepare(
            'INSERT INTO articles (title, content, author, cover_image, youtube_link, category, difficulty, read_time, tags) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        if ($stmt === false) {
            die('Prepare failed: ' . $this->conn->error);
        }

        // Получаем параметры
        $params = $this->prepare_article_params($inputData, $coverImagePath);
        $types = str_repeat('s', count($params));

        // Привязка параметров
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $article_id = $stmt->insert_id;
            $stmt->close();
            return $article_id;
        } else {
            error_log("Execute failed: " . $stmt->error);
            echo "Execute failed: " . $stmt->error;
            $stmt->close();
            return false;
        }
    }

    public function update_article($articleId, $inputData, $coverImagePath)
    {
        $stmt = $this->conn->prepare(
            'UPDATE articles SET title = ?, content = ?, author = ?, cover_image = ?, youtube_link = ?, category = ?, difficulty = ?, read_time = ?, tags = ? 
         WHERE id = ?'
        );

        if ($stmt === false) {
            die('Prepare failed: ' . $this->conn->error);
        }

        // Получаем параметры и добавляем $articleId в конце
        $params = array_merge($this->prepare_article_params($inputData, $coverImagePath), [$articleId]);
        $types = str_repeat('s', count($params) - 1) . 'i';

        // Привязка параметров
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            error_log("Execute failed: " . $stmt->error);
            echo "Execute failed: " . $stmt->error;
            $stmt->close();
            return false;
        }
    }

    public function update_content($articleId, $content){
        $stmt = $this->conn->prepare("UPDATE articles SET content = ? WHERE id = ?");
        $stmt->bind_param("si", $content, $articleId); // "si" - строка и целое число
        if (!$stmt->execute()) {
            die('Update failed: ' . $stmt->error);
        }
        $stmt->close();
    }
    public function update_article_slug($articleId, $slug){
        $query = "UPDATE articles SET slug = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('si', $slug, $articleId);
        $stmt->execute();
    }
    public function get_article_by_slug($slug){
        $query = "SELECT * FROM articles WHERE slug = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function get_cover_image_by_slug($slug) {
        $query = "SELECT cover_image FROM articles WHERE slug = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['cover_image'] ?? null; // Возвращаем значение или null, если не найдено
    }

    public function delete_article($slug) {
        $stmt = $this->conn->prepare('DELETE FROM articles WHERE slug = ?');
        if ($stmt === false) {
            die('Prepare failed: ' . $this->conn->error);
        }
        $stmt->bind_param('s', $slug);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            error_log("Delete failed: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }
    // Метод для увеличения счетчика лайков
    public function increment_like_count($slug) {
        $sql = "UPDATE articles SET likes = likes + 1 WHERE slug = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $stmt->close();
    }

    // Метод для уменьшения счетчика лайков
    public function decrement_like_count($slug) {
        $sql = "UPDATE articles SET likes = GREATEST(likes - 1, 0) WHERE slug = ?"; // Не допускаем отрицательных значений
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $stmt->close();
    }

    // Метод для увеличения счетчика дизлайков
    public function increment_dislike_count($slug) {
        $sql = "UPDATE articles SET dislikes = dislikes + 1 WHERE slug = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $stmt->close();
    }

    // Метод для уменьшения счетчика дизлайков
    public function decrement_dislike_count($slug) {
        $sql = "UPDATE articles SET dislikes = GREATEST(dislikes - 1, 0) WHERE slug = ?"; // Не допускаем отрицательных значений
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $stmt->close();
    }
    // Метод для получения количества лайков
    public function get_likes_count($slug): int
    {
        $slug = $this->conn->real_escape_string($slug); // Экранируем строку для безопасности
        $sql = "SELECT likes FROM articles WHERE slug = '$slug'";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return (int)$row['likes'];
        }
        return 0; // Если запись не найдена
    }

    // Метод для получения количества дизлайков
    public function get_dislikes_count($slug): int
    {
        $slug = $this->conn->real_escape_string($slug); // Экранируем строку для безопасности
        $sql = "SELECT dislikes FROM articles WHERE slug = '$slug'";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return (int)$row['dislikes'];
        }
        return 0; // Если запись не найдена
    }

    public function getUserArticles($userLogin, $title = '', $author = '', $category = '', $dateFrom = '', $dateTo = '') {
        // Базовый SQL-запрос
        $query = "SELECT * FROM articles WHERE author = ?";

        $params = [$userLogin];
        $types = 's'; // Тип параметра для user_login
        // Подготовка запроса
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            throw new Exception("SQL error: " . $this->conn->error);
        }

        // Привязка параметров
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        // Выполнение запроса
        $stmt->execute();

        // Получение результатов
        $result = $stmt->get_result();
        $articles = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $articles;
    }
    public function getFilteredArticles($userLogin, $title = '', $author = '', $category = '', $dateFrom = '', $dateTo = '') {
        // Базовый SQL-запрос
        $query = "SELECT * FROM articles WHERE author = ?";

        $params = [$userLogin];
        $types = 's'; // Тип параметра для user_id

        // Фильтр по названию статьи
        if (!empty($title)) {
            $query .= " AND title LIKE ?";
            $params[] = '%' . $title . '%';
            $types .= 's'; // Строковый параметр
        }

        // Фильтр по автору
        if (!empty($author)) {
            $query .= " AND author LIKE ?";
            $params[] = '%' . $author . '%';
            $types .= 's'; // Строковый параметр
        }

        // Фильтр по категории
        if (!empty($category)) {
            $query .= " AND category = ?";
            $params[] = $category;
            $types .= 's'; // Строковый параметр
        }

        // Фильтр по дате "от"
        if (!empty($dateFrom)) {
            $query .= " AND created_at >= ?";
            $params[] = $dateFrom;
            $types .= 's'; // Строковый параметр (дата)
        }

        // Фильтр по дате "до"
        if (!empty($dateTo)) {
            $query .= " AND created_at <= ?";
            $params[] = $dateTo;
            $types .= 's'; // Строковый параметр (дата)
        }

        // Подготовка запроса
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            throw new Exception("SQL error: " . $this->conn->error);
        }

        // Привязка параметров
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        // Выполнение запроса
        $stmt->execute();

        // Получение результатов
        $result = $stmt->get_result();
        $articles = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $articles;
    }
}