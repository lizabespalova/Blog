<?php

namespace models;

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
//            die('Ошибка подготовки запроса: ' . $this->conn->error);
            error_log('Ошибка подготовки запроса: ' . $this->conn->error);

        }

        // Привязываем параметры и выполняем запрос
        $stmt->bind_param('iss', $article_id, $image_path, $slug);
        if (!$stmt->execute()) {
//            die('Prepare failed: ' . $stmt->error);
            error_log('Ошибка подготовки запроса: ' . $this->conn->error);

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
    public function get_images_by_article_slug($slug){
        // SQL-запрос для получения изображений по slug статьи
        $query = "
        SELECT ai.id, ai.image_path
        FROM article_images ai
        INNER JOIN articles a ON ai.article_id = a.id
        WHERE a.slug = ?
    ";
        // Подготовка запроса
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            die("Ошибка подготовки запроса: " . $this->conn->error);
        }

        // Привязываем параметр
        $stmt->bind_param('s', $slug);

        // Выполняем запрос
        $stmt->execute();

        // Получаем результат
        $result = $stmt->get_result();

        // Проверяем, есть ли данные
        if ($result->num_rows > 0) {
            $images = [];
            while ($row = $result->fetch_assoc()) {
                $images[] = $row;
            }
            return $images;
        }

        // Если данных нет, возвращаем пустой массив
        return [];
    }
    public function delete_image($id)
    {
        // Подключение к базе данных (замените $this->db на ваше подключение)

        // SQL-запрос для удаления изображения
        $query = "DELETE FROM article_images WHERE id = ?";

        // Подготовка запроса
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            die("Ошибка подготовки запроса: " .  $this->conn->error);
        }

        // Привязываем параметр
        $stmt->bind_param('i', $id);

        // Выполняем запрос
        if ($stmt->execute()) {
            // Успешное удаление
            return true;
        } else {
            // Ошибка при удалении
            return false;
        }
    }
    // Удаление изображений статей


}