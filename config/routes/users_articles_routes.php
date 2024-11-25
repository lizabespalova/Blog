<?php


use controllers\authorized_users_controllers\UserArticleController;

require_once __DIR__ . '/../../config/config.php';

function users_article_route($uri, $method) {
    $dbConnection = getDbConnection();

    switch ($uri) {
        case (preg_match('/^\/users-articles\/filter$/', $uri)? true : false):
            $controller = new UserArticleController($dbConnection);
            if ($method === 'GET') {
                $controller->filterUsersArticles();
            }
            exit();
        case (preg_match('/^\/users-articles\/([\w-]+)$/', $uri) ? true : false):
            $controller = new UserArticleController($dbConnection);
            if ($method === 'GET') {
                $controller->showUsersArticles();
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
users_article_route($uri, $method);
?>
