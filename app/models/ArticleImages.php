<?php

namespace models;

use Exception;
class ArticleImages
{
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
    public function save_image_path_to_db($article_id, $image_path, $slug)
    {
        $sql = "INSERT INTO article_images (article_id, image_path, slug) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die('Ошибка подготовки запроса: ' . $this->conn->error);
        }

        // Привязываем параметры и выполняем запрос
        $stmt->bind_param('iss', $article_id, $image_path, $slug);
        if (!$stmt->execute()) {
            die('Prepare failed: ' . $stmt->error);
        }

        // Закрываем подготовленный запрос
        $stmt->close();
    }
    public function delete_images_from_db($slug) {
        // Подготавливаем SQL запрос для удаления всех строк с указанным слагом
        $stmt = $this->conn->prepare('DELETE FROM article_images WHERE slug = ?');

        // Проверяем, успешно ли была подготовка запроса
        if ($stmt === false) {
            die('Prepare failed: ' . $this->conn->error);
        }

        // Привязываем параметр slug к запросу
        $stmt->bind_param('s', $slug);

        // Выполняем запрос
        if ($stmt->execute()) {
            // Закрываем подготовленный запрос
            $stmt->close();
            return true; // Возвращаем true, если удаление прошло успешно
        } else {
            // Логируем ошибку и возвращаем false в случае неудачи
            error_log("Delete failed: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }


}