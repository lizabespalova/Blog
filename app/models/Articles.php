<?php

namespace models;

use Exception;

class Articles
{
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
    public function add_article($title, $content, $author, $coverImagePath, $youtubeLink,$category, $difficulty, $read_time, $tags)
    {
        // SQL запрос для вставки статьи
        $stmt = $this->conn->prepare('INSERT INTO articles (title, content, author, cover_image, youtube_link, category, difficulty, read_time, tags) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        if ($stmt === false) {
            die('Prepare failed: ' . $this->conn->error);
        }

        // Привязываем параметры. Используем 's' для строк и 'b' для NULL.
        $types = 'sssssssss';
        $params = [$title, $content, $author, /*$link*/ $coverImagePath, $youtubeLink, $category, $difficulty, $read_time, $tags];

        // Установить NULL для пустых значений
        foreach ($params as &$param) {
            if ($param === '') {
                $param = null;
            }
        }

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

    public function save_image_path_to_db($article_id, string $image_path)
    {
        $sql = "INSERT INTO article_images (article_id, image_path) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die('Ошибка подготовки запроса: ' . $this->conn->error);
        }

        // Привязываем параметры и выполняем запрос
        $stmt->bind_param('is', $article_id, $image_path);
        if (!$stmt->execute()) {
            die('Ошибка выполнения запроса: ' . $stmt->error);
        }

        // Закрываем подготовленный запрос
        $stmt->close();
    }
    public function update_content($articleId, $content){
        $stmt = $this->conn->prepare("UPDATE articles SET content = ? WHERE id = ?");
        $stmt->bind_param("si", $content, $articleId); // "si" - строка и целое число
        if (!$stmt->execute()) {
            die('Update failed: ' . $stmt->error);
        }
        $stmt->close();
    }
    public function  update_article_slug($articleId, $slug){
        $query = "UPDATE articles SET slug = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('si', $slug, $articleId);
        $stmt->execute();
    }
    public function  get_article_by_slug($slug){
        $query = "SELECT * FROM articles WHERE slug = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}