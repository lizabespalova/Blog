<?php


namespace models;

use Exception;

class Articles
{
    private $conn;

    public function __construct($conn)
    {
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
            $inputData['is_published'] ?? '',
            $inputData['user_id'] ?? '',
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
            'INSERT INTO articles (title, content, author, cover_image, youtube_link, category, difficulty, read_time, tags, is_published, user_id) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
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
    public function update_article_cover($articleId, $coverImagePath)
    {
        $query = "UPDATE articles SET cover_image = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            die('Prepare failed: ' . $this->conn->error);
        }

        $stmt->bind_param("si", $coverImagePath, $articleId);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            error_log("Execute failed: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }
    public function update_article($articleId, $inputData, $coverImagePath)
    {
        $stmt = $this->conn->prepare(
            'UPDATE articles SET title = ?, content = ?, author = ?, cover_image = ?, 
                    youtube_link = ?, category = ?, difficulty = ?, read_time = ?, 
                    tags = ? , is_published = ?, user_id = ? 
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

    public function update_content($articleId, $content)
    {
        $stmt = $this->conn->prepare("UPDATE articles SET content = ? WHERE id = ?");
        $stmt->bind_param("si", $content, $articleId); // "si" - строка и целое число
        if (!$stmt->execute()) {
            die('Update failed: ' . $stmt->error);
        }
        $stmt->close();
    }

    public function update_article_slug($articleId, $slug)
    {
        $query = "UPDATE articles SET slug = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('si', $slug, $articleId);
        $stmt->execute();
    }

    public function get_article_by_slug($slug)
    {
        $query = "SELECT * FROM articles WHERE slug = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function get_cover_image_by_slug($slug)
    {
        $query = "SELECT cover_image FROM articles WHERE slug = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['cover_image'] ?? null; // Возвращаем значение или null, если не найдено
    }

    public function delete_article($slug)
    {
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
    public function increment_like_count($slug)
    {
        $sql = "UPDATE articles SET likes = likes + 1 WHERE slug = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $stmt->close();
    }

    // Метод для уменьшения счетчика лайков
    public function decrement_like_count($slug)
    {
        $sql = "UPDATE articles SET likes = GREATEST(likes - 1, 0) WHERE slug = ?"; // Не допускаем отрицательных значений
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $stmt->close();
    }

    // Метод для увеличения счетчика дизлайков
    public function increment_dislike_count($slug)
    {
        $sql = "UPDATE articles SET dislikes = dislikes + 1 WHERE slug = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $stmt->close();
    }

    // Метод для уменьшения счетчика дизлайков
    public function decrement_dislike_count($slug)
    {
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

    public function getUserArticles($userLogin, $title = '', $author = '', $category = '', $dateFrom = '', $dateTo = '')
    {
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


    public function getFilteredArticles($userLogin, $title, $author, $category, $dateFrom, $dateTo)
    {
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

    public function getTitleByArticleId($articleId)
    {

        $stmt = $this->conn->prepare("SELECT title FROM articles WHERE id = ?");
        if (!$stmt) {
            die("Error during making query: " . $this->conn->error);
        }

        $stmt->bind_param("i", $articleId);
        $stmt->execute();
        $result = $stmt->get_result();

        $title = $result->num_rows > 0 ? $result->fetch_assoc()['title'] : null;
        $stmt->close();

        return $title;
    }
    public function getAuthorId($articleId){  // Получаем автора статьи
        $stmt2 = $this->conn->prepare("SELECT user_id FROM articles WHERE id = ?");
        $stmt2->bind_param("i", $articleId);
        $stmt2->execute();
        $stmt2->bind_result($authorId);
        $stmt2->fetch();
        $stmt2->close();

        if (empty($authorId)) {
            // Если автор не найден, вернуть ошибку
            return false;
        }
        return $authorId;
    }
    function getArticleById($id) {
        $query = "SELECT * FROM articles WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    /**
     * Увеличивает количество просмотров статьи.
     *
     * @param int $articleId ID статьи.
     * @return int Новое количество просмотров.
     */
    public function incrementViews($articleId)
    {
        // Увеличиваем количество просмотров
        $stmt = $this->conn->prepare("UPDATE articles SET views = views + 1 WHERE id = ?");
        $stmt->bind_param('i', $articleId);
        $stmt->execute();
        $stmt->close();

        // Возвращаем обновленное количество просмотров
        return $this->getViews($articleId);
    }

    /**
     * Получает текущее количество просмотров статьи.
     *
     * @param int $articleId ID статьи.
     * @return int Количество просмотров.
     */
    public function getViews($articleId)
    {
        $stmt = $this->conn->prepare("SELECT views FROM articles WHERE id = ?");
        $stmt->bind_param('i', $articleId);
        $stmt->execute();

        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();

        return $data ? (int)$data['views'] : 0;
    }
    public function deleteArticleByUserId($user_id){
        $stmt = $this->conn->prepare("DELETE articles, article_images FROM articles 
                                  LEFT JOIN article_images ON articles.id = article_images.article_id
                                  WHERE articles.user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }
    // Функция получения статей для ленты
    public function getArticlesForFeed($user_id, $startIndex, $articlesPerPage)
    {
        $startIndex = intval($startIndex);
        $articlesPerPage = intval($articlesPerPage);

        // Подготовка запроса для получения статей
        $stmt = $this->conn->prepare("
        SELECT a.*, u.user_login, u.user_avatar 
        FROM articles a
        JOIN followers f ON a.user_id = f.following_id
        JOIN users u ON a.user_id = u.user_id
        WHERE f.follower_id = ?
        AND a.is_published = 1
        ORDER BY a.created_at DESC
        LIMIT ?, ?
    ");

        // Связывание параметров
        $stmt->bind_param("iii", $user_id, $startIndex, $articlesPerPage);

        // Выполнение запроса
        if (!$stmt->execute()) {
            return ['error' => 'Ошибка при получении статей: ' . $stmt->error];
        }

        // Получение результатов
        $result = $stmt->get_result();
        $articles = $result->fetch_all(MYSQLI_ASSOC);

        // Закрытие соединения
        $stmt->close();

        return $articles;
    }

// Функция для подсчета общего количества статей
    public function getTotalArticlesCountForFeed($user_id)
    {
        $stmt = $this->conn->prepare("
        SELECT COUNT(*) 
        FROM articles a
        JOIN followers f ON a.user_id = f.following_id
        WHERE f.follower_id = ?
        AND a.is_published = 1
    ");

        $stmt->bind_param("i", $user_id);

        // Выполнение запроса
        if (!$stmt->execute()) {
            return ['error' => 'Ошибка при подсчете статей: ' . $stmt->error];
        }

        // Получение общего количества статей
        $stmt->bind_result($totalArticles);
        $stmt->fetch();

        $stmt->close();

        return $totalArticles;
    }
    public function getArticlesFilteredByTags($tag) {
        $stmt = $this->conn->prepare("
        SELECT DISTINCT a.id, a.slug, a.title, a.created_at, 
               a.content, 
               u.user_login, u.user_avatar
        FROM articles a
        JOIN users u ON a.user_id = u.user_id
        WHERE a.is_published = 1 
        AND LOWER(a.tags) LIKE LOWER(CONCAT('%', ?, '%')) 
        ORDER BY a.created_at DESC
    ");
        $stmt->bind_param("s", $tag);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }


    // Получение популярных статей по лайкам
    public function getPopularArticlesByCourseID(int $courseId): array
    {
        $popularArticles = [];

        $stmt = $this->conn->prepare("
        SELECT a.id, a.title,a.views, a.author,a.created_at, a.user_id,a.cover_image, a.slug, COUNT(ar.id) AS likes
        FROM course_articles ca
        INNER JOIN articles a ON ca.article_id = a.id
        LEFT JOIN article_reactions ar ON a.slug = ar.article_slug AND ar.reaction_type = 'like'
        WHERE ca.course_id = ?
        GROUP BY a.id
        ORDER BY likes DESC
        LIMIT 5
    ");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $res = $stmt->get_result();

        while ($row = $res->fetch_assoc()) {
            $popularArticles[] = $row;
        }

        $stmt->close();
        return $popularArticles;
    }
    public function getUserPublicatedArticles($userLogin) {
        // Подготавливаем SQL-запрос
        $sql = "SELECT a.id, a.title, a.likes, 
                   a.content, 
                   a.slug, a.created_at, u.user_avatar, u.user_login
            FROM articles a
            LEFT JOIN users u ON a.user_id = u.user_id
            WHERE a.author = ? AND a.is_published = 1
            ORDER BY a.created_at DESC";

        // Подготовка запроса
        if ($stmt = $this->conn->prepare($sql)) {
            // Привязываем параметры
            $stmt->bind_param('s', $userLogin); // Привязываем параметр типа строка

            // Выполняем запрос
            $stmt->execute();

            // Получаем результат
            $result = $stmt->get_result();
            $articles = $result->fetch_all(MYSQLI_ASSOC); // Получаем данные в виде ассоциативного массива

            // Закрываем подготовленный запрос
            $stmt->close();

            // Возвращаем массив статей
            return $articles;
        } else {
            // В случае ошибки возвращаем пустой массив
            return [];
        }
    }
    public function showPopularAiArticles() {
        $query = "
        SELECT DISTINCT a.id, a.slug, a.title, a.content, a.created_at, 
               a.link, a.views, a.cover_image, a.youtube_link, a.likes, 
               u.user_login, u.user_avatar
        FROM articles a
        JOIN users u ON a.user_id = u.user_id
        WHERE a.category = 'ai' 
        AND a.is_published = 1 
        ORDER BY (a.likes * 2 + a.views) DESC 
        LIMIT 10
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function showPopularItNewsArticles() {
        $query = "
        SELECT DISTINCT a.id, a.slug, a.title, a.content, a.created_at, 
               a.link, a.views, a.cover_image, a.youtube_link, a.likes, 
               u.user_login, u.user_avatar
        FROM articles a
        JOIN users u ON a.user_id = u.user_id
        WHERE a.category = 'it_news' 
        AND a.is_published = 1 
        ORDER BY (a.likes * 2 + a.views) DESC 
        LIMIT 10
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function showPopularWebDevelopmentArticles() {
        $query = "
        SELECT DISTINCT a.id, a.slug, a.title, a.content, a.created_at, 
               a.link, a.views, a.cover_image, a.youtube_link, a.likes, 
               u.user_login, u.user_avatar
        FROM articles a
        JOIN users u ON a.user_id = u.user_id
        WHERE a.category = 'web_development' 
        AND a.is_published = 1 
        ORDER BY (a.likes * 2 + a.views) DESC 
        LIMIT 10
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function showPopularCyberSecurityArticles() {
        $query = "
        SELECT DISTINCT a.id, a.slug, a.title, a.content, a.created_at, 
               a.link, a.views, a.cover_image, a.youtube_link, a.likes, 
               u.user_login, u.user_avatar
        FROM articles a
        JOIN users u ON a.user_id = u.user_id
        WHERE a.category = 'cyber_security' 
        AND a.is_published = 1 
        ORDER BY (a.likes * 2 + a.views) DESC 
        LIMIT 10
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}