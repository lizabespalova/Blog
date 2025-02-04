<?php

namespace controllers\authorized_users_controllers;

use models\Articles;

require_once 'app/services/helpers/session_check.php';
class UserArticleController
{
    private $articleModel;


    public function __construct($conn) {
        $this->articleModel = new Articles($conn);
    }
    public function showUsersArticles(){
        require_once 'app/services/helpers/switch_language.php';

        // Получаем ID пользователя из сессии
        $userLogin = $_SESSION['user']['user_login'];

        // Получаем список избранных статей с деталями
        $article_cards = $this->articleModel->getUserArticles($userLogin);
        include __DIR__ . '/../../views/authorized_users/users_articles/users_articles.php';
    }
    public function filterUsersArticles() {
        $title = $_GET['title'] ?? null;
        $author = $_GET['author'] ?? null;
        $category = $_GET['category'] ?? null;
        $date_from = $_GET['date_from'] ?? null;
        $date_to = $_GET['date_to'] ?? null;
        $userLogin = $_SESSION['user']['user_login'];

        if (!$title && !$author && !$category && !$date_from && !$date_to) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'No filters applied']);
            exit;
        }

        try {
            $results = $this->articleModel->getFilteredArticles($userLogin,$title,$author,
                $category, $date_from, $date_to);

//            // Проверяем корректность результатов
//            if (!$results) {
//                throw new Exception('No data found.');
//            }

            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'data' => $results]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
//    public function handleUserInterest($userId,$category){
//
//    }

}