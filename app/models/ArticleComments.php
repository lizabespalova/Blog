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
        $stmt->execute([$article_slug, $user_id, $comment_text, $parent_id]);
        return $this->conn->lastInsertId(); // Возвращает ID добавленного комментария
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
    public function get_reaction($userId, $comment_id) {
        $stmt =  $this->conn->prepare("SELECT reaction_type FROM comment_reactions WHERE user_id = ? AND comment_id = ?");
        $stmt->bind_param("ii", $userId, $comment_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function add_reaction($userId, $comment_id, $reactionType) {
        $stmt = $this->conn->prepare("INSERT INTO comment_reactions (comment_id, user_id, reaction_type) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $comment_id, $userId, $reactionType);
        $stmt->execute();
    }

    public function update_reaction($userId, $comment_id, $reactionType) {
        $stmt = $this->conn->prepare("UPDATE comment_reactions SET reaction_type = ? WHERE user_id = ? AND comment_id = ?");
        $stmt->bind_param("sii", $reactionType, $userId, $comment_id);
        $stmt->execute();
    }
    public function remove_reaction($userId, $comment_id) {
        $stmt = $this->conn->prepare("DELETE FROM comment_reactions WHERE comment_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $comment_id, $userId);
        $stmt->execute();
    }
}