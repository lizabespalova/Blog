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


}