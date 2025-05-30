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
            $this->insertIntoCourseArticles($courseId,$articleIds);

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
    public function insertIntoCourseArticles($courseId, $articleIds = []) {
        // Если пришло одно число, делаем из него массив
        if (is_int($articleIds)) {
            $articleIds = [$articleIds];
        }

        // Убедимся, что это массив и не пустой
        if (!empty($articleIds) && is_array($articleIds)) {
            foreach ($articleIds as $articleId) {
                $articleQuery = "INSERT INTO course_articles (course_id, article_id) VALUES (?, ?)";
                $articleStmt = $this->conn->prepare($articleQuery);
                $articleStmt->bind_param("ii", $courseId, $articleId);
                if (!$articleStmt->execute()) {
                    return false; // Ошибка при добавлении статьи
                }
            }
        }
        return true;
    }

    public function updateCourse($courseId, $userId, $title, $description, $coverImage, $articleIds = []){
        // Шаг 1: Обновляем курс в таблице `courses`
        $query = "UPDATE courses SET user_id = ?, title = ?, description = ?, cover_image = ? WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isssi", $userId, $title, $description, $coverImage, $courseId);

        if (!$stmt->execute()) {
            return false; // Ошибка при обновлении курса
        }

        $this->insertIntoCourseArticles($courseId,$articleIds);

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
    public function getFavoritesCount($courseId)
    {
        $query = "SELECT COUNT(*) as total FROM favorite_courses WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['total'] ?? 0; // Возвращает количество или 0, если записей нет
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
    public function updateVisibilityType($visibility_type, $course_id, $user_ids){
        // Обновляем курс
        $stmt = $this->conn->prepare("UPDATE courses SET visibility_type = ? WHERE course_id = ?");
        $stmt->bind_param("si", $visibility_type, $course_id);
        $stmt->execute();
        $stmt->close();

        if ($visibility_type === 'custom') {
            // Удаляем старые записи
            $stmt = $this->conn->prepare("DELETE FROM course_custom_access WHERE course_id = ?");
            $stmt->bind_param("i", $course_id);
            $stmt->execute();
            $stmt->close();

            // Добавляем новых пользователей
            if (!empty($user_ids)) {
                $stmt = $this->conn->prepare("INSERT INTO course_custom_access (course_id, user_id) VALUES (?, ?)");
                foreach ($user_ids as $user_id) {
                    $stmt->bind_param("ii", $course_id, $user_id);
                    $stmt->execute();
                }
                $stmt->close();
            }
        }

    }
    public function updateIsPublishedType($status, $courseId){
        // Обновляем статус курса в базе данных
        $stmt = $this->conn->prepare("UPDATE courses SET is_published = ? WHERE course_id = ?");
        $stmt->bind_param('ii', $status, $courseId);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка при обновлении статуса']);
        }
    }
    public function isUserSubscribedToCourse($userId, $courseId) {
        // SQL-запрос для проверки подписки
        $query = "SELECT COUNT(*) FROM subscriptions WHERE user_id = ? AND course_id = ?";

        // Подготовка и выполнение запроса
        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param("ii", $userId, $courseId); // Привязка параметров
            $stmt->execute();
            $stmt->bind_result($count); // Получаем результат
            $stmt->fetch(); // Извлекаем результат

            $stmt->close();

            // Если количество записей больше 0, значит, пользователь подписан на курс
            return $count > 0;
        } else {
            // Ошибка выполнения запроса
            return false;
        }
    }
    public function isUserHasCustomAccess($userId, $courseId) {
        // SQL-запрос для проверки наличия доступа
        $query = "SELECT COUNT(*) FROM course_custom_access WHERE user_id = ? AND course_id = ?";

        // Подготовка и выполнение запроса
        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param("ii", $userId, $courseId); // Привязка параметров
            $stmt->execute();
            $stmt->bind_result($count); // Получаем результат
            $stmt->fetch(); // Извлекаем результат

            $stmt->close();

            // Если количество записей больше 0, значит, у пользователя есть доступ
            return $count > 0;
        } else {
            // Ошибка выполнения запроса
            return false;
        }
    }
    public function getSubscribersByCourseId($courseId) {
        // Подготовка SQL-запроса для получения подписчиков курса
        $sql = "
        SELECT u.user_id, u.user_login, u.user_email, u.user_avatar
        FROM users u
        INNER JOIN course_custom_access cca ON u.user_id = cca.user_id
        WHERE cca.course_id = ?
    ";
        // Подготовка запроса
        if ($stmt = $this->conn->prepare($sql)) {
            // Привязываем параметр courseId к запросу
            $stmt->bind_param("i", $courseId);
            // Выполняем запрос
            $stmt->execute();
            // Получаем результат
            $result = $stmt->get_result();
            // Массив для хранения подписчиков
            $subscribers = [];
            // Проходим по всем строкам результата и сохраняем их в массив
            while ($row = $result->fetch_assoc()) {
                $subscribers[] = $row;
            }
            // Закрытие подготовленного запроса
            $stmt->close();
            // Возвращаем список подписчиков
            return $subscribers;
        } else {
            // В случае ошибки запроса
            return ["error" => "Unable to prepare the SQL query"];
        }
    }
    public function removeSubscriber($userId, $courseId) {
        $stmt = $this->conn->prepare("DELETE FROM course_custom_access WHERE user_id = ? AND course_id = ?");
        $stmt->bind_param("ii", $userId, $courseId);  // связываем параметры
        $stmt->execute();

        // Используем affected_rows для mysqli
        return $stmt->affected_rows > 0;
    }

    public function removeCustomAccess($course_id) {
        $sql = "DELETE FROM course_custom_access WHERE course_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$course_id]);
    }
    public function getCourseVisibility($courseId) {
        $sql = "SELECT visibility_type FROM courses WHERE course_id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return 'public'; // Возвращаем значение по умолчанию при ошибке
        }

        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['visibility_type'] ?? 'public'; // Если значение не найдено, используем 'public'
    }

    public function getCourseRating($courseId) {
        // Подготовка SQL-запроса для получения лайков и дизлайков курса
        $stmt = $this->conn->prepare("
        SELECT 
            SUM(CASE WHEN reaction = 'like' THEN 1 WHEN reaction = 'dislike' THEN -1 ELSE 0 END) AS rating,
            COUNT(*) AS total_reactions
        FROM course_reactions 
        WHERE course_id = ?
    ");
        $stmt->bind_param("i", $courseId); // Подставляем courseId в запрос
        $stmt->execute(); // Выполняем запрос
        $result = $stmt->get_result(); // Получаем результат запроса
        $row = $result->fetch_assoc(); // Извлекаем ассоциативный массив с результатами

        // Если есть реакции, рассчитываем рейтинг
        if ($row['total_reactions'] > 0) {
            // Средний рейтинг: Если реакции +1 (лайк) и -1 (дизлайк), то это дает рейтинг 0.
            // Вычисляем нормализованный рейтинг в пределах от 0 до 5
            $rating = ($row['rating'] / $row['total_reactions'] + 1) * 2.5; // +1 для сдвига от 0 до 1, умножаем на 2.5, чтобы результат был в диапазоне от 0 до 5.

            // Ограничиваем рейтинг в пределах от 0 до 5
            $rating = max(0, min($rating, 5));

            // Возвращаем рейтинг с точностью до 2 знаков после запятой
            return round($rating, 2);
        } else {
            // Если нет реакций, возвращаем 0
            return 0;
        }
    }
    public function getTotalReadTime($courseId) {
        $query = "
        SELECT SUM(a.read_time) AS total_time
        FROM articles a
        JOIN course_articles ca ON a.id = ca.article_id
        WHERE ca.course_id = ?
    ";

        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param("i", $courseId);
            $stmt->execute();
            $stmt->bind_result($total_time);
            $stmt->fetch();
            $stmt->close();
            return $total_time ?? 0; // Если NULL, вернём 0
        }

        return 0; // В случае ошибки
    }
    public function getMaterialsCount($courseId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM course_materials WHERE course_id = ?");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] ?? 0;
    }

    public function getAverageDifficulty($courseId) {
        $stmt = $this->conn->prepare("
        SELECT difficulty FROM articles 
        WHERE id IN (SELECT article_id FROM course_articles WHERE course_id = ?)
    ");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();

        $difficultyLevels = ['beginner' => 1, 'intermediate' => 2, 'advanced' => 3];
        $totalDifficulty = 0;
        $count = 0;

        while ($row = $result->fetch_assoc()) {
            if (isset($difficultyLevels[$row['difficulty']])) {
                $totalDifficulty += $difficultyLevels[$row['difficulty']];
                $count++;
            }
        }

        if ($count === 0) return 'unknown';

        $avgDifficulty = round($totalDifficulty / $count);

        return array_search($avgDifficulty, $difficultyLevels) ?: 'unknown';
    }
    public function getSimilarCourses($courseId, $title, $limit = 10) {
        $sql = "SELECT c.*, u.user_login, 
                   (SELECT COUNT(*) FROM course_reactions cr 
                    WHERE cr.course_id = c.course_id AND cr.reaction = 'like') AS likes
            FROM courses c
            JOIN users u ON c.user_id = u.user_id
            WHERE c.course_id != ? 
              AND (c.title LIKE ? OR c.title LIKE ?)
            ORDER BY likes DESC, c.created_at DESC
            LIMIT ?";

        $searchTitle1 = "%{$title}%"; // Совпадения в любом месте названия
        $searchTitle2 = "{$title}%";  // Совпадения в начале названия

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issi", $courseId, $searchTitle1, $searchTitle2, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function deleteUserCourses($user_id) {
        $query = "DELETE FROM courses WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }
    public function deleteCustomAccess($user_id) {
        $query = "DELETE FROM course_custom_access WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }

    public function deleteReactions($user_id) {
        $query = "DELETE FROM course_reactions WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }

    public function deleteMaterials($user_id) {
        $query = "DELETE FROM course_materials WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }

    public function deleteProgress($user_id) {
        $query = "DELETE FROM course_progress WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }

}