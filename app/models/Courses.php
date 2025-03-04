<?php

namespace models;

class Courses
{
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function save($userId, $title, $description, $coverImage, $articleIds = []) {
        // Сначала вставляем курс в таблицу `courses`
        $query = "INSERT INTO courses (user_id, title, description, cover_image) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isss", $userId, $title, $description, $coverImage);

        if ($stmt->execute()) {
            $courseId = $stmt->insert_id; // Получаем ID вставленного курса

            // Если передан массив с articleIds, добавляем статьи в таблицу `course_articles`
            $this->insertIntoCourseArticles($articleIds, $courseId);

            return $courseId; // Возвращаем ID курса
        } else {
            return false; // Ошибка при сохранении курса
        }
    }
    public function updateCoverImage($courseId, $coverImagePath) {
        $stmt = $this->conn->prepare("UPDATE courses SET cover_image = ?, updated_at = NOW() WHERE course_id = ?");
        if (!$stmt) {
            return false; // Ошибка подготовки запроса
        }

        $stmt->bind_param("si", $coverImagePath, $courseId);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }
    public function getUserCourses($userId) {
        $query = "SELECT * FROM courses WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function getCourseById($courseId) {
        $query = "SELECT * FROM courses WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getArticlesByCourseId($courseId) {
        $query = "SELECT *
              FROM articles a
              JOIN course_articles ca ON a.id = ca.article_id
              WHERE ca.course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function deleteArticlesFromCoursesArticles($courseId){
        $query = "DELETE FROM course_articles WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
    }
    public function insertIntoCourseArticles($articleIds = [], $courseId){
        //  Добавляем новые статьи в таблицу `course_articles`
        if (!empty($articleIds)) {
            foreach ($articleIds as $articleId) {
                $articleQuery = "INSERT INTO course_articles (course_id, article_id) VALUES (?, ?)";
                $articleStmt = $this->conn->prepare($articleQuery);
                $articleStmt->bind_param("ii", $courseId, $articleId);
                if (!$articleStmt->execute()) {
                    return false; // Ошибка при добавлении статьи
                }
            }
        }
    }
    public function updateCourse($courseId, $userId, $title, $description, $coverImage, $articleIds = []){
        // Шаг 1: Обновляем курс в таблице `courses`
        $query = "UPDATE courses SET user_id = ?, title = ?, description = ?, cover_image = ? WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isssi", $userId, $title, $description, $coverImage, $courseId);

        if (!$stmt->execute()) {
            return false; // Ошибка при обновлении курса
        }

        $this->insertIntoCourseArticles($articleIds, $courseId);

        return true; // Обновление прошло успешно
    }

    public function deleteCourseById($courseId) {
        // Начинаем транзакцию, чтобы удалить данные безопасно
        $this->conn->begin_transaction();

        try {
            // Удаляем сам курс из таблицы courses
            $deleteCourseQuery = "DELETE FROM courses WHERE course_id = ?";
            $stmt = $this->conn->prepare($deleteCourseQuery);
            $stmt->bind_param("i", $courseId);
            $stmt->execute();

            // Фиксируем транзакцию
            $this->conn->commit();

            return true; // Успешное удаление
        } catch (Exception $e) {
            // В случае ошибки откатываем транзакцию
            $this->conn->rollback();
            return false; // Ошибка удаления
        }
    }
}