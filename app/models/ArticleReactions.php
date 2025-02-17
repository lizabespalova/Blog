<?php

namespace models;

class ArticleReactions
{
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
    public function get_reaction($userId, $slug) {
        $stmt =  $this->conn->prepare("SELECT reaction_type FROM article_reactions WHERE user_id = ? AND article_slug = ?");
        $stmt->bind_param("is", $userId, $slug);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function add_reaction($userId, $slug, $reactionType) {
        $stmt = $this->conn->prepare("INSERT INTO article_reactions (user_id, article_slug, reaction_type) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userId, $slug, $reactionType);
        $stmt->execute();
    }
    public function update_reaction($userId, $slug, $reactionType) {
        $stmt = $this->conn->prepare("UPDATE article_reactions SET reaction_type = ? WHERE user_id = ? AND article_slug = ?");
        $stmt->bind_param("sis", $reactionType, $userId, $slug);
        $stmt->execute();
    }
    public function remove_reaction($userId, $slug) {
        $stmt = $this->conn->prepare("DELETE FROM article_reactions WHERE user_id = ? AND article_slug = ?");
        $stmt->bind_param("is", $userId, $slug);
        $stmt->execute();
    }
    // Метод для получения пользователей, поставивших лайк
    public function getLikesBySlug($slug)
    {
        $stmt = $this->conn->prepare("
            SELECT users.user_id, users.user_login, users.user_email, users.link, users.user_avatar
            FROM article_reactions
            JOIN users ON article_reactions.user_id = users.user_id
            WHERE article_reactions.article_slug = ?
            AND article_reactions.reaction_type = 'like'
        ");
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Метод для получения пользователей, поставивших дизлайк
    public function getDislikesBySlug($slug)
    {
        $stmt = $this->conn->prepare("
            SELECT users.user_id, users.user_login, users.user_email, users.link, users.user_avatar
            FROM article_reactions
            JOIN users ON article_reactions.user_id = users.user_id
            WHERE article_reactions.article_slug = ?
            AND article_reactions.reaction_type = 'dislike'
        ");
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function deleteReactionByUserId($user_id){
        // Удаляем реакции пользователя на статьи
        $stmt = $this->conn->prepare("DELETE FROM article_reactions WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

    }
}