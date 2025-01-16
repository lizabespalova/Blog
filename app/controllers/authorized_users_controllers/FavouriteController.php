<?php

namespace controllers\authorized_users_controllers;
use Exception;
use models\Articles;
use models\Favourites;
use models\User;

require_once 'app/services/helpers/session_check.php';
class FavouriteController
{
    private $favouriteModel;
    private $userModel;
    private $articleModel;

    public function __construct($conn) {
        $this->favouriteModel = new Favourites($conn);
        $this->userModel = new User($conn);
        $this->articleModel = new Articles($conn);
    }
    public function showFavourites(){
        // Получаем ID пользователя из сессии
        $userId = $_SESSION['user']['user_id'];

        // Получаем список избранных статей с деталями
        $article_cards = $this->favouriteModel->getUserFavoriteArticles($userId);
        include __DIR__ . '/../../views/authorized_users/favourites/favourite_template.php';
    }

    public function toggle($userId, $articleId): string
    {
        if ($this->favouriteModel->exists($userId, $articleId)) {
            $this->favouriteModel->remove($userId, $articleId);
            return 'removed';
        } else {
            $this->favouriteModel->add($userId, $articleId);
            // Если избранное, то добавить как интересы пользователя
            $article = $this->articleModel->getArticleById($articleId);
            $this->userModel->trackUserInterest($userId, $article['category']);
            return 'added';
        }
    }

    private function get_favourites_input(): array
    {
        return [
            'article_id' => $_POST['article_id'],
            'user_id' => $_SESSION['user']['user_id']
        ];
    }

    public function toggleFavourites() {

        header('Content-Type: application/json'); // Указываем, что возвращаем JSON

        try {
            $inputFavouriteData = $this->get_favourites_input(); // Получаем входные данные

            // Проверяем и переключаем состояние избранного
            $action = $this->toggle($inputFavouriteData['user_id'], $inputFavouriteData['article_id']);

            // Возвращаем JSON
            echo json_encode(['success' => true, 'action' => $action]);
        } catch (Exception $e) {
            // Возвращаем ошибку, если что-то пошло не так
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }

        exit; // Обязательно завершаем выполнение
    }

    public function filterFavourites() {
        $title = $_GET['title'] ?? null;
        $author = $_GET['author'] ?? null;
        $category = $_GET['category'] ?? null;
        $date_from = $_GET['date_from'] ?? null;
        $date_to = $_GET['date_to'] ?? null;

        if (!$title && !$author && !$category && !$date_from && !$date_to) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'No filters applied']);
            exit;
        }

        try {
            $results = $this->favouriteModel->getFilteredFavourites($title, $author, $date_from, $date_to, $category);

            // Если $results является объектом, преобразуем его в массив
            if (is_object($results)) {
                $results = json_decode(json_encode($results), true);
            }

            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'data' => $results]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }


}