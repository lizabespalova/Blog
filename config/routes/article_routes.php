<?php

use controllers\authorized_users_controllers\ArticleController;
use controllers\authorized_users_controllers\ArticleImagesController;
use controllers\authorized_users_controllers\EditProfileController;
use controllers\search_controllers\SearchController;

require_once __DIR__ . '/../../config/config.php';

function article_route($uri, $method) {
    $dbConnection = getDbConnection();
    //error_log("URI: $uri, Method: $method");

    switch ($uri) {

        case '/create-article':
            $controller = new ArticleController($dbConnection);
            if ($method === 'GET') {
                $controller->show_article_form(null);
            }
            else if ($method === 'POST') {
                $controller->create_article();
            }
            exit();  // Остановка выполнения после маршрута
        case (bool)preg_match('/^\/articles\/delete\/([\w-]+)$/', $uri, $matches):
            $slug = $matches[1];
            $controller = new ArticleController(getDbConnection());
            $controller->delete_article($slug);
            $controller = new ArticleImagesController(getDbConnection());
            $controller->delete_article_images($slug);
            exit();
        case $method === 'POST' && $uri === '/articles/react':
            $controller = new ArticleController(getDbConnection());
            $controller->handle_reaction('article');
            exit();
            //Стоит тут из-за конфликта со следующим путем. Пока не знаю, как перенести в comments_routes
        case $method === 'GET' && (bool)preg_match('/^\/articles\/get_comments$/', $uri, $matches):
            $controller = new ArticleController($dbConnection);
            $controller->get_comments();
            exit();
        case $method === 'GET' && (bool)preg_match('/^\/articles\/([\w-]+)$/', $uri, $matches):
            $slug = $matches[1]; // Извлекаем слаг из URL
            $controller = new ArticleController($dbConnection);
            $controller->show_article($slug);
            exit();
        case (bool)preg_match('/^\/articles\/edit\/([\w-]+)$/', $uri, $matches):
            $slug = $matches[1];
            $controller = new ArticleController(getDbConnection());
            $controller->show_article_form($slug);
            exit();
        case $method === 'POST' && $uri === '/repost':
            $controller = new ArticleController(getDbConnection());
            $controller->repost();
            exit();
        case $method === 'POST' && $uri === '/repost-delete':
            $controller = new ArticleController(getDbConnection());
            $controller->delete_repost();
            exit();
        case $method === 'GET' && preg_match('~^/articles/statistics/(\d+)$~', $uri, $matches):
            $controller = new ArticleController(getDbConnection());
            $articleId = $matches[1]; // Извлекаем ID статьи из URI
            $controller->getArticleStatistics($articleId); // Передаём ID и соединение с БД
            exit();
        case $method === 'GET' && preg_match('/article\/([^\/]+)\/reactions/', $uri, $matches):
            $slug = $matches[1]; // Извлекаем параметр slug из URI
            $controller = new ArticleController(getDbConnection());
            $controller->getArticleReactioners($slug); // Передаем slug в метод контроллера
            exit();

        default:
            return false;
    }
}

// Получаем URI и метод запроса
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Запускаем маршрутизатор до вывода содержимого
article_route($uri, $method);
?>
