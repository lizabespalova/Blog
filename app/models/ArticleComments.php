<?php

namespace models;

class ArticleComments
{
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
    public function get_comments_by_slug($slug) {
        $query = "SELECT comments.*, users.user_login, users.user_avatar, users.link FROM comments 
              JOIN users ON comments.user_id = users.user_id 
              WHERE article_slug = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        $comments = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $comments;
    }
    // Метод для добавления комментария
    public function add_comment($article_slug, $user_id, $comment_text, $parent_id ) {
        $query = "INSERT INTO comments (article_slug, user_id, comment_text, parent_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('siss', $article_slug, $user_id, $comment_text, $parent_id);

        $result = $stmt->execute(); // Выполнение запроса

        $stmt->close();
        return $result;
    }
    public function get_comments_amount($slug){
        $query = "SELECT COUNT(*) as comment_count FROM comments WHERE article_slug = ?";
        $stmt = $this->conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param('s', $slug);
            $stmt->execute();
            $stmt->bind_result($commentCount);
            $stmt->fetch();
            $stmt->close();
            return $commentCount;
        } else {
            // Обработка ошибки, если запрос не удался
            return 0;
        }

    }
    public function get_reaction($userId, $slug) {
        $stmt =  $this->conn->prepare("SELECT reaction_type FROM comments WHERE user_id = ? AND article_slug = ?");
        $stmt->bind_param("is", $userId, $slug);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function add_reaction($userId, $slug, $reactionType) {
        $stmt = $this->conn->prepare("INSERT INTO comments (user_id, article_slug, reaction_type) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userId, $slug, $reactionType);
        $stmt->execute();
    }
    public function update_reaction($userId, $slug, $reactionType) {
        $stmt = $this->conn->prepare("UPDATE comments SET reaction_type = ? WHERE user_id = ? AND article_slug = ?");
        $stmt->bind_param("sis", $reactionType, $userId, $slug);
        $stmt->execute();
    }
    public function remove_reaction($userId, $slug) {
        $stmt = $this->conn->prepare("DELETE FROM comments WHERE user_id = ? AND article_slug = ?");
        $stmt->bind_param("is", $userId, $slug);
        $stmt->execute();
    }
    public function increment_dislike_count($slug) {
        $sql = "UPDATE comments SET dislikes = dislikes + 1 WHERE slug = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $stmt->close();
    }

    // Метод для уменьшения счетчика дизлайков
    public function decrement_dislike_count($slug) {
        $sql = "UPDATE comments SET dislikes = GREATEST(dislikes - 1, 0) WHERE slug = ?"; // Не допускаем отрицательных значений
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $stmt->close();
    }
    // Метод для увеличения счетчика лайков
    public function increment_like_count($slug) {
        $sql = "UPDATE comments SET likes = likes + 1 WHERE slug = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $stmt->close();
    }

    // Метод для уменьшения счетчика лайков
    public function decrement_like_count($slug) {
        $sql = "UPDATE comments SET likes = GREATEST(likes - 1, 0) WHERE slug = ?"; // Не допускаем отрицательных значений
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $stmt->close();
    }
}