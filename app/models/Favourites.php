<?php

namespace models;

use Exception;

class Favourites
{
    private $conn;
    private $notifications;
    private $user;
    private $article;



    public function __construct($conn) {
        $this->conn = $conn;
        $this->notifications = new Notifications($conn);
        $this->user = new User($conn);
        $this->article = new Articles($conn);
    }

    // Добавить в избранное
    public function add($reactionerId, $articleId) {
        // Добавляем статью в избранное
        $stmt = $this->conn->prepare("INSERT INTO favorites (user_id, article_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $reactionerId, $articleId);

        // Получаем логин пользователя, который добавил в избранное
        $userLogin = $this->user->getLoginById($reactionerId);

        // Получаем заголовок статьи
        $articleTitle = $this->article->getTitleByArticleId($articleId);

        $authorId = $this->article->getAuthorId($articleId);

        // Формируем уведомление
        $message = "User '{$userLogin}' has added your article '{$articleTitle}' to favourites!";

        // Добавляем уведомление
        $this->notifications->addNotification($authorId, $reactionerId, 'favorite', $message, $articleId);

        return $stmt->execute();
    }


    // Удалить из избранного
    public function remove($userId, $articleId) {
        $stmt = $this->conn->prepare("DELETE FROM favorites WHERE user_id = ? AND article_id = ?");
        $stmt->bind_param("ii", $userId, $articleId);
        return $stmt->execute();
    }

    // Проверить, находится ли статья в избранном
    public function exists($userId, $articleId) {
        $stmt = $this->conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND article_id = ?");
        $stmt->bind_param("ii", $userId, $articleId);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }


    // Получить список избранных статей пользователя
    public function getUserFavorites($userId) {
        $stmt = $this->conn->prepare("SELECT article_id FROM favorites WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $favorites = [];
        while ($row = $result->fetch_assoc()) {
            $favorites[] = $row['article_id'];
        }
        return $favorites;
    }
    public function getUserFavoriteArticles($userId)
    {
        $stmt = $this->conn->prepare("
        SELECT articles.id, articles.title, articles.slug, articles.cover_image, articles.created_at, articles.author
        FROM favorites
        JOIN articles ON favorites.article_id = articles.id
        WHERE favorites.user_id = ?
    ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $articles = [];
        while ($row = $result->fetch_assoc()) {
            $articles[] = $row;
        }
        return $articles;
    }

    public function getFilteredFavourites($title,$author, $dateFrom, $dateTo, $category) {
        // Базовый SQL-запрос
        $query = "SELECT f.*, a.title, a.author, a.cover_image, a.slug
              FROM favorites f
              INNER JOIN articles a ON f.article_id = a.id
              WHERE 1=1";

        $params = [];
        $types = ''; // Типы параметров для mysqli (s, i, d, etc.)

        // Фильтр по названию статьи
        if (!empty($title)) {
            $query .= " AND a.title LIKE ?";
            $params[] = '%' . $title . '%';
            $types .= 's'; // Строковый параметр
        }

        // Фильтр по автору
        if (!empty($author)) {
            $query .= " AND a.author LIKE ?";
            $params[] = '%' . $author . '%';
            $types .= 's'; // Строковый параметр
        }

        // Фильтр по категории
        if (!empty($category)) {
            $query .= " AND a.category = ?"; // Пример, если у вас есть поле category в таблице articles
            $params[] = $category;
            $types .= 's'; // Строковый параметр
        }

        // Фильтр по дате "от"
        if (!empty($dateFrom)) {
            $query .= " AND f.created_at >= ?";
            $params[] = $dateFrom;
            $types .= 's'; // Строковый параметр (дата)
        }

        // Фильтр по дате "до"
        if (!empty($dateTo)) {
            $query .= " AND f.created_at <= ?";
            $params[] = $dateTo;
            $types .= 's'; // Строковый параметр (дата)
        }

        // Подготовка запроса
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            throw new Exception("Sql error: " . $this->conn->error);
        }

        // Привязка параметров
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        // Выполнение запроса
        $stmt->execute();

        // Получение результатов
        $result = $stmt->get_result();
        $favourites = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $favourites;
    }
    public function deleteFavouriteByUserId($userId) {
        $stmt = $this->conn->prepare("DELETE FROM favorites WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
    }
    public function deleteFavouriteCourses($user_id) {
        $query = "DELETE FROM favorite_courses WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }
    public function addCourseToFavourites($userId, $course_id){
        $stmt = $this->conn->prepare("INSERT IGNORE INTO favorite_courses (user_id, course_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $course_id);
        $stmt->execute();
        $stmt->close();
    }
    public function deleteCourseFromFavourites($userId, $course_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM favorite_courses WHERE user_id = ? AND course_id = ?");
        $stmt->bind_param("ii", $userId, $course_id);
        $stmt->execute();
        $stmt->close();
    }
    public function checkIfFavorite($user_id, $course_id) {
        $stmt = $this->conn->prepare("SELECT 1 FROM favorite_courses WHERE user_id = ? AND course_id = ?");
        $stmt->bind_param("ii", $user_id, $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $isFavorite = $result->num_rows > 0;
        $stmt->close();
        return $isFavorite;
    }
    public function getFavoriteCourses($userId) {
        $stmt = $this->conn->prepare("
        SELECT c.course_id, c.title, c.description, c.cover_image, c.visibility_type, c.user_id 
        FROM courses c
        JOIN favorite_courses f ON c.course_id = f.course_id
        WHERE f.user_id = ?
    ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $courses = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $courses;
    }

}