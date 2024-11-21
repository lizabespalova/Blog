<?php

use controllers\authorized_users_controllers\ArticleController;
use controllers\authorized_users_controllers\ArticleImagesController;
use controllers\authorized_users_controllers\EditProfileController;
use controllers\authorized_users_controllers\FavouriteController;
use controllers\search_controllers\SearchController;

require_once __DIR__ . '/../../config/config.php';

function favourites_route($uri, $method) {
    $dbConnection = getDbConnection();

    switch ($uri) {

        case (preg_match('/^\/favourites\/toggle$/', $uri) ? true : false):
            $controller = new FavouriteController($dbConnection);
            if ($method === 'POST') {
                $controller->toggleFavourites();
            }
            exit();
        case (preg_match('/^\/favourites\/([\w-]+)$/', $uri) ? true : false):
            $controller = new FavouriteController($dbConnection);
            if ($method === 'GET') {
                $controller->showFavourites();
            }
            exit();  // Остановка выполнения после маршрута

        default:
            return false;
    }
}

// Получаем URI и метод запроса
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Запускаем маршрутизатор до вывода содержимого
favourites_route($uri, $method);
?>
