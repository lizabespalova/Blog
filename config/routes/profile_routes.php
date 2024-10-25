<?php

use controllers\authorized_users_controllers\ArticleController;
use controllers\authorized_users_controllers\ArticleImagesController;
use controllers\authorized_users_controllers\EditProfileController;
use controllers\search_controllers\SearchController;

require_once __DIR__ . '/../../config/config.php';

function profile_route($uri, $method) {
    $dbConnection = getDbConnection();
    //error_log("URI: $uri, Method: $method");

    switch ($uri) {
        case '/update-description':
            $controller = new EditProfileController($dbConnection);
            if ($method === 'POST') {
            //    echo "Hello";
                $controller->update_profile();
            }
            exit();  // Остановка выполнения после маршрута

        case '/edit':
            $controller = new EditProfileController($dbConnection);
            if ($method === 'GET') {
                $controller->show_edit_form();
            }
            exit();  // Остановка выполнения после маршрута
        case '/update-main-description':
            $controller = new EditProfileController($dbConnection);
            if ($method === 'POST') {
                $controller->update_main_description();
            }
            exit();  // Остановка выполнения после маршрута
        case '/search':
            $controller = new SearchController($dbConnection);
            if ($method === 'GET') {
                $controller->show_search_form();
            }
            exit();  // Остановка выполнения после маршрута
        case '/create-article':
            $controller = new ArticleController($dbConnection);
            if ($method === 'GET') {
                $controller->show_article_form();
            }
            else if ($method === 'POST') {
            $controller->create_article();
            }
            exit();  // Остановка выполнения после маршрута
        case $_SERVER['REQUEST_METHOD'] === 'GET' && (bool)preg_match('/^\/articles\/([\w-]+)$/', $uri, $matches):
            $slug = $matches[1]; // Извлекаем слаг из URL
            $controller = new ArticleController($dbConnection);
            $controller->show_article($slug);
            exit();
        case (bool)preg_match('/^\/articles\/delete\/([\w-]+)$/', $uri, $matches):
            $slug = $matches[1];
            $controller = new ArticleController(getDbConnection());
            $controller->delete_article($slug);
            $controller = new ArticleImagesController(getDbConnection());
            $controller->delete_article_images($slug);
            exit();
        case $_SERVER['REQUEST_METHOD'] === 'POST' && $uri === '/articles/react':
            $controller = new ArticleController(getDbConnection());
            $controller->handle_reaction();
            exit();
        case $_SERVER['REQUEST_METHOD'] === 'POST' && $uri === '/articles/add_comment':
            $controller = new ArticleController(getDbConnection());
            $controller->handle_add_comment();
            exit();
        default:
            return false;
    }
}

// Получаем URI и метод запроса
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Запускаем маршрутизатор до вывода содержимого
profile_route($uri, $method);
?>
