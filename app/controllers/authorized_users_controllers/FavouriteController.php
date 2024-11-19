<?php

namespace controllers\authorized_users_controllers;
use models\Favourites;

require_once 'app/services/helpers/session_check.php';
class FavouriteController
{
    private $favouriteModel;
    public function __construct($conn) {
        $this->favouriteModel = new Favourites($conn);

    }
    public function showFavourites(){
////        Массив с данными с запросов и сессий
//        $inputFavouriteData = $this->get_favourites_input();
//
//        // Проверяем, есть ли статья в избранном
//        $action = $this->toggle($inputFavouriteData['user_id'], $inputFavouriteData['article_id']);
//        echo json_encode(['success' => true, 'action' => $action]);
        // Подключение шаблона и передача данных пользователя
        include __DIR__ . '/../../views/authorized_users/favourites/favourite_template.php';
    }
    public function toggle($userId, $articleId) {
        if ($this->favouriteModel->exists($userId, $articleId)) {
            $this->favouriteModel->remove($userId, $articleId);
            return 'removed';
        } else {
            $this->favouriteModel->add($userId, $articleId);
            return 'added';
        }
    }

    private function get_favourites_input(): array
    {
        return [
            'article_id' => $_POST['article_id'],
            'slug' => $_POST['slug'],
            'user_id' => $_SESSION['user']['user_id']
        ];
    }

    public function toggleFavourites() {
        $inputFavouriteData = $this->get_favourites_input();

        // Проверяем, есть ли статья в избранном
        $action = $this->toggle($inputFavouriteData['user_id'], $inputFavouriteData['article_id']);
        echo json_encode(['success' => true, 'action' => $action]);
    }

}