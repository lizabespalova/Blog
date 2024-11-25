<?php

namespace models;

use Exception;

class Favourites
{
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Добавить в избранное
    public function add($userId, $articleId) {
        $stmt = $this->conn->prepare("INSERT INTO favorites (user_id, article_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $articleId);
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

}