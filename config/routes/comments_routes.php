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
        case $method === 'GET' && (bool)preg_match('/^\/articles\/get_comments$/', $uri, $matches):
            $controller = new ArticleController($dbConnection);
            $controller->get_comments(); // Создайте метод get_comments
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
