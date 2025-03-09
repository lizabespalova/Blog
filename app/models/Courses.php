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
    public function updateCourseTitel($newTitle, $courseId){
        // Обновляем заголовок
        $stmt = $this->conn->prepare("UPDATE courses SET title = ? WHERE course_id = ?");
        $stmt->bind_param("si", $newTitle, $courseId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
    public function updateCourseDescription($newDescription, $courseId){
        // Обновляем заголовок
        $stmt = $this->conn->prepare("UPDATE courses SET description = ? WHERE course_id = ?");
        $stmt->bind_param("si", $newDescription, $courseId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
    public function saveProgress($user_id, $course_id, $article_id, $video_link, $is_completed) {
        // Проверка, существует ли уже такая запись
        $stmt = $this->conn->prepare("SELECT id FROM course_progress WHERE user_id=? AND course_id=? AND article_id=?");
        $stmt->bind_param("iii", $user_id, $course_id, $article_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Обновляем запись
            $update = $this->conn->prepare("UPDATE course_progress SET is_completed=?, video_link=?, completed_at=NOW() WHERE user_id=? AND course_id=? AND article_id=?");
            $update->bind_param("isiii", $is_completed, $video_link, $user_id, $course_id, $article_id); // Обратите внимание на типы
            $update->execute();
        } else {
            // Вставляем новую запись
            $insert = $this->conn->prepare("INSERT INTO course_progress (user_id, course_id, article_id, video_link, is_completed) VALUES (?, ?, ?, ?, ?)");
            $insert->bind_param("iiisi", $user_id, $course_id, $article_id, $video_link, $is_completed);
            $insert->execute();
        }

    }



    // Метод для получения прогресса пользователя по курсу
    public function getCourseProgress($userId, $courseId) {
        // Считаем общее количество элементов (статьи и видео) в курсе
        $sql_total = "SELECT 
                    COUNT(DISTINCT ca.article_id) + 
                    (SELECT COUNT(DISTINCT cp.video_link) 
                     FROM course_progress cp 
                     WHERE cp.course_id = ? AND cp.video_link IS NOT NULL) 
                 FROM course_articles ca 
                 WHERE ca.course_id = ?";

        // Подсчитываем завершенные элементы (статьи и видео) для пользователя
        $sql_completed = "SELECT 
                        COUNT(DISTINCT CASE WHEN cp.article_id IS NOT NULL THEN cp.article_id END) + 
                        COUNT(DISTINCT CASE WHEN cp.video_link IS NOT NULL THEN cp.video_link END)
                      FROM course_progress cp
                      WHERE cp.user_id = ? AND cp.course_id = ? AND cp.is_completed = 1";

        // Получаем общее количество элементов
        $stmt_total = $this->conn->prepare($sql_total);
        $stmt_total->bind_param("ii", $courseId, $courseId);
        $stmt_total->execute();
        $stmt_total->bind_result($totalItems);
        $stmt_total->fetch();
        $stmt_total->close();

        // Получаем количество завершенных элементов
        $stmt_completed = $this->conn->prepare($sql_completed);
        $stmt_completed->bind_param("ii", $userId, $courseId);
        $stmt_completed->execute();
        $stmt_completed->bind_result($completedItems);
        $stmt_completed->fetch();
        $stmt_completed->close();

        // Вычисляем прогресс в процентах
        if ($totalItems > 0) {
            $progress = ($completedItems / $totalItems) * 100;
        } else {
            $progress = 0;
        }

        return $progress ?? 0; // Возвращаем прогресс (в процентах), если не найдено - 0
    }


    public function getCompletedArticlesForUser($userId, $courseId) {
        $sql = "SELECT article_id FROM course_progress 
            WHERE user_id = ? AND course_id = ? AND is_completed = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $userId, $courseId);
        $stmt->execute();
        $result = $stmt->get_result();

        $completedArticles = [];
        while ($row = $result->fetch_assoc()) {
            $completedArticles[] = $row['article_id'];
        }
        $stmt->close();

        return $completedArticles;
    }


}