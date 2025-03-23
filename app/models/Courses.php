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
        // Считаем общее количество статей в курсе
        $sql_total = "SELECT COUNT(DISTINCT article_id) FROM course_articles WHERE course_id = ?";
        $stmt_total = $this->conn->prepare($sql_total);
        $stmt_total->bind_param("i", $courseId);
        $stmt_total->execute();
        $stmt_total->bind_result($totalArticles);
        $stmt_total->fetch();
        $stmt_total->close();

        // Считаем количество завершённых пользователем статей
        $sql_completed = "SELECT COUNT(DISTINCT article_id) 
                      FROM course_progress 
                      WHERE user_id = ? AND course_id = ? AND is_completed = 1 AND article_id IS NOT NULL";
        $stmt_completed = $this->conn->prepare($sql_completed);
        $stmt_completed->bind_param("ii", $userId, $courseId);
        $stmt_completed->execute();
        $stmt_completed->bind_result($completedArticles);
        $stmt_completed->fetch();
        $stmt_completed->close();

        // Вычисляем прогресс в процентах
        $progress = ($totalArticles > 0) ? ($completedArticles / $totalArticles) * 100 : 0;

        return round($progress, 2); // округляем до двух знаков
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
    public function getMaterials($courseId)
    {
        $materials = [];

        // Подготовим SQL-запрос
        $stmt = $this->conn->prepare("
        SELECT material_id, file_name, original_name, description, uploaded_at
        FROM course_materials
        WHERE course_id = ?
        ORDER BY uploaded_at DESC
    ");

        if ($stmt) {
            $stmt->bind_param("i", $courseId);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $materials[] = $row;
            }

            $stmt->close();
        }
//        $this->conn->close();
        return $materials;
    }
    public function createMaterials($courseId, $userId, $safeFileName, $originalName, $description){
        // Запись в базу
        $stmt = $this->conn->prepare("
                INSERT INTO course_materials (course_id, user_id, file_name, original_name, description)
                VALUES (?, ?, ?, ?, ?)
            ");
        $stmt->bind_param('iisss', $courseId, $userId, $safeFileName, $originalName, $description);
        if ($stmt->execute()) {
            $successFiles[] = $originalName;
        }
        $stmt->close();
        return $successFiles;
    }
    // Получение информации о материале
    public function getMaterialById($materialId) {
        $stmt = $this->conn->prepare("SELECT * FROM course_materials WHERE material_id = ? LIMIT 1");
        $stmt->bind_param('i', $materialId);
        $stmt->execute();
        $result = $stmt->get_result();
        $material = $result->fetch_assoc();
        $stmt->close();
        return $material;
    }

// Удаление материала из базы
    public function deleteMaterialById($materialId) {
        $stmt = $this->conn->prepare("DELETE FROM course_materials WHERE material_id = ?");
        $stmt->bind_param('i', $materialId);
        $stmt->execute();
        $stmt->close();
    }
// Получить количество лайков для курса
    public function getLikesForCourse($courseId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS likes FROM course_reactions WHERE course_id = ? AND reaction = 'like'");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['likes'];
    }

    // Получить количество дизлайков для курса
    public function getDislikesForCourse($courseId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS dislikes FROM course_reactions WHERE course_id = ? AND reaction = 'dislike'");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['dislikes'];
    }
    // Проверка существующей реакции
    public function getReaction($courseId, $userId) {
        $query = "SELECT * FROM course_reactions WHERE course_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $courseId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Вернёт null, если не найдено
    }


    // Вставка новой реакции
    public function addReaction($courseId, $userId, $reactionType) {
        $query = "INSERT INTO course_reactions (course_id, user_id, reaction) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$courseId, $userId, $reactionType]);
    }

    // Обновление существующей реакции
    public function updateReaction($courseId, $userId, $reactionType) {
        $query = "UPDATE course_reactions SET reaction = ? WHERE course_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$reactionType, $courseId, $userId]);
    }
    public function removeReaction($courseId, $userId) {
        $query = "DELETE FROM course_reactions WHERE course_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $courseId, $userId);
        return $stmt->execute();
    }


// Получение количества лайков
    public function getCourseLikes(int $courseId): int
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS likes FROM course_reactions WHERE course_id = ? AND reaction = 'like'");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return (int)($result['likes'] ?? 0);
    }

// Получение количества дизлайков
    public function getCourseDislikes(int $courseId): int
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS dislikes FROM course_reactions WHERE course_id = ? AND reaction = 'dislike'");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return (int)($result['dislikes'] ?? 0);
    }

    public function getLikesById($courseId){
        $likesQuery = $this->conn->prepare("
        SELECT u.user_id, u.user_login, u.user_email, u.user_avatar, CONCAT('/profile/', u.user_login) AS link
        FROM course_reactions cr
        JOIN users u ON cr.user_id = u.user_id
        WHERE cr.course_id = ? AND cr.reaction = 'like'
    ");
        $likesQuery->bind_param('i', $courseId);
        $likesQuery->execute();
        $likesResult = $likesQuery->get_result();
        $likes = [];
        while ($row = $likesResult->fetch_assoc()) {
            $likes[] = $row;
        }
        $likesQuery->close();
        return $likes;
    }
    public function getDislikesById($courseId){
        // Дизлайки
        $dislikesQuery = $this->conn->prepare("
        SELECT u.user_id, u.user_login, u.user_email, u.user_avatar, CONCAT('/profile/', u.user_login) AS link
        FROM course_reactions cr
        JOIN users u ON cr.user_id = u.user_id
        WHERE cr.course_id = ? AND cr.reaction = 'dislike'
    ");
        $dislikesQuery->bind_param('i', $courseId);
        $dislikesQuery->execute();
        $dislikesResult = $dislikesQuery->get_result();
        $dislikes = [];
        while ($row = $dislikesResult->fetch_assoc()) {
            $dislikes[] = $row;
        }
        $dislikesQuery->close();
        return $dislikes;
    }
}