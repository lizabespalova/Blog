<?php

namespace models;

use Exception;

class Articles
{
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
    public function add_article($title, $content, $author, $coverImagePath, $youtubeLink)
    {
//        // Генерация ссылки на статью
//        $link = '/articles/' . urlencode(strtolower(str_replace(' ', '-', $title)));

        // SQL запрос для вставки статьи
        $stmt = $this->conn->prepare('INSERT INTO articles (title, content, author, cover_image, youtube_link) VALUES (?, ?, ?, ?, ?)');
        if ($stmt === false) {
            die('Prepare failed: ' . $this->conn->error);
        }

        // Привязываем параметры. Используем 's' для строк и 'b' для NULL.
        $types = 'sssss';
        $params = [$title, $content, $author, /*$link*/ $coverImagePath, $youtubeLink];

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
    public function update_article_link($article_id, $article_link){
        $stmt = $this->conn->prepare('UPDATE articles SET link = ? WHERE id = ?');
        if ($stmt === false) {
            die('Prepare failed: ' . $this->conn->error);
        }
        $stmt->bind_param('si', $article_link, $article_id);
        if (!$stmt->execute()) {
            error_log("Update failed: " . $stmt->error);
        }
        $stmt->close();
    }

    // Метод для добавления изображений статьи
    public function add_article_images($article_id, $images)
    {
        // Подготовка SQL-запроса для вставки изображений
        $stmt = $this->conn->prepare("INSERT INTO article_images (article_id, image_path) VALUES (?, ?)");
        if ($stmt === false) {
            die('Prepare failed: ' . $this->conn->error);
        }

        // Цикл для вставки всех изображений
        foreach ($images as $image_path) {
            // Привязываем параметры
            // Устанавливаем значение параметра $image_path
            $stmt->bind_param("is", $article_id, $image_path);
            // Выполняем запрос для текущего изображения
            $stmt->execute();
        }

        // Закрытие statement
        $stmt->close();
    }

    // Метод для получения изображений статьи
    public function get_article_by_id($article_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM articles WHERE id = ?");
        $stmt->bind_param("i", $article_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $article = $result->fetch_assoc();
        $stmt->close();
        return $article;
    }

    public function get_article_images($article_id)
    {
        $stmt = $this->conn->prepare("SELECT image_path FROM article_images WHERE article_id = ?");
        $stmt->bind_param("i", $article_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $images = [];
        while ($row = $result->fetch_assoc()) {
            $images[] = $row['image_path'];
        }
        $stmt->close();
        return $images;
    }
}