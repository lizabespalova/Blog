<?php

namespace models;

use Exception;

class Comment
{
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
    public function increment_dislike_count($comment_id) {
        $sql = "UPDATE comments SET dislikes = dislikes + 1 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $comment_id);
        $stmt->execute();
        $stmt->close();
    }

    // Метод для уменьшения счетчика дизлайков
    public function decrement_dislike_count($comment_id) {
        $sql = "UPDATE comments SET dislikes = GREATEST(dislikes - 1, 0) WHERE id = ?"; // Не допускаем отрицательных значений
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $comment_id);
        $stmt->execute();
        $stmt->close();
    }
    // Метод для увеличения счетчика лайков
    public function increment_like_count($comment_id) {
        $sql = "UPDATE comments SET likes = likes + 1 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $comment_id);
        $stmt->execute();
        $stmt->close();
    }

    // Метод для уменьшения счетчика лайков
    public function decrement_like_count($comment_id) {
        $sql = "UPDATE comments SET likes = GREATEST(likes - 1, 0) WHERE id = ?"; // Не допускаем отрицательных значений
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $comment_id);
        $stmt->execute();
        $stmt->close();
    }

    public function get_likes_count($comment_id): int
    {
        $comment_id = $this->conn->real_escape_string($comment_id); // Экранируем строку для безопасности
        $sql = "SELECT likes FROM comments WHERE id = '$comment_id'";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return (int)$row['likes'];
        }
        return 0; // Если запись не найдена
    }

    // Метод для получения количества дизлайков
    public function get_dislikes_count($comment_id): int
    {
        $comment_id = $this->conn->real_escape_string($comment_id); // Экранируем строку для безопасности
        $sql = "SELECT dislikes FROM comments WHERE id = '$comment_id'";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return (int)$row['dislikes'];
        }
        return 0; // Если запись не найдена
    }
    public function delete_comment($commentId) {
        $this->conn->begin_transaction();

        try {
            // Получаем информацию о комментарии
            $checkCommentQuery = $this->conn->prepare("SELECT parent_id FROM comments WHERE id = ?");
            $checkCommentQuery->bind_param('i', $commentId);
            $checkCommentQuery->execute();
            $result = $checkCommentQuery->get_result();
            $comment = $result->fetch_assoc();

            if (!$comment) {
                throw new Exception("Comment not found.");
            }

            // Если комментарий основной (parent_id IS NULL), то удаляем все ответы на него
            if (is_null($comment['parent_id'])) {
                // Удаляем реакции для основного комментария
                $deleteReactionsQuery = $this->conn->prepare("DELETE FROM comment_reactions WHERE comment_id = ?");
                $deleteReactionsQuery->bind_param('i', $commentId);
                $deleteReactionsQuery->execute();

                // Удаляем все ответы на основной комментарий
                $deleteRepliesQuery = $this->conn->prepare("DELETE FROM comments WHERE parent_id = ?");
                $deleteRepliesQuery->bind_param('i', $commentId);
                $deleteRepliesQuery->execute();
            }

            // Удаляем реакции для данного комментария (основного или ответа)
            $deleteReactionsQuery = $this->conn->prepare("DELETE FROM comment_reactions WHERE comment_id = ?");
            $deleteReactionsQuery->bind_param('i', $commentId);
            $deleteReactionsQuery->execute();

            // Удаляем сам комментарий
            $deleteCommentQuery = $this->conn->prepare("DELETE FROM comments WHERE id = ?");
            $deleteCommentQuery->bind_param('i', $commentId);
            $deleteCommentQuery->execute();

            $this->conn->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    public function deleteCommentByUserId($userId) {
        $stmt = $this->conn->prepare("DELETE FROM comments WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
    }

}