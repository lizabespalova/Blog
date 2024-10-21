<?php


namespace models;

use Exception;

class Articles
{
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
    public function add_article($inputData, $coverImagePath)
    {
        // SQL запрос для вставки статьи
        $stmt = $this->conn->prepare(
            'INSERT INTO articles (title, content, author, cover_image, youtube_link, category, difficulty, read_time, tags) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        if ($stmt === false) {
            die('Prepare failed: ' . $this->conn->error);
        }

        // Массив данных, который мы передаем для вставки
        $params = [
            $inputData['title'] ?? '',         // Заголовок
            $inputData['content'] ?? '',       // Контент
            $inputData['author'] ?? '',        // Автор
            $coverImagePath,                   // Путь к обложке
            $inputData['youtube_link'] ?? '',  // Ссылка на YouTube
            $inputData['category'] ?? '',      // Категория
            $inputData['difficulty'] ?? '',    // Сложность
            $inputData['read_time'] ?? '',     // Время чтения
            $inputData['tags'] ?? ''           // Теги
        ];

        // Установить NULL для пустых значений
        foreach ($params as &$param) {
            if ($param === '') {
                $param = null;
            }
        }

        // Привязываем параметры. Используем 's' для строк.
        $types = str_repeat('s', count($params));

        // Привязка параметров
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            // Получаем ID вставленной статьи
            $article_id = $stmt->insert_id;
            $stmt->close();
            return $article_id; // Возвращаем ID статьи
        } else {
            // Логируем ошибку
            error_log("Execute failed: " . $stmt->error);
            echo "Execute failed: " . $stmt->error; // Для отладки
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
}