<?php

use controllers\authorized_users_controllers\ArticleController;
use controllers\authorized_users_controllers\ArticleImagesController;
use controllers\authorized_users_controllers\EditProfileController;
use controllers\search_controllers\SearchController;

require_once __DIR__ . '/../../config/config.php';

function comment_route($uri, $method) {
    $dbConnection = getDbConnection();

    switch ($uri) {
        case $method === 'POST' && $uri === '/articles/add_comment':
            $controller = new ArticleController($dbConnection);
            $controller->handle_add_comment();
            exit();
        case $method === 'POST' && $uri === '/delete-comment':
            $controller = new ArticleController($dbConnection);
            $controller->delete_comment();
            exit();


        case $method === 'POST' && $uri === '/comments/react':
            $controller = new ArticleController($dbConnection);
            $controller->handle_reaction('comment');
            exit();

        default:
            return false;
    }
}

// Получаем URI и метод запроса
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Запускаем маршрутизатор до вывода содержимого
comment_route($uri, $method);
?>
