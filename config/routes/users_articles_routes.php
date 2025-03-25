<?php


use controllers\authorized_users_controllers\UserArticleController;
use controllers\search_controllers\SearchController;

require_once __DIR__ . '/../../config/config.php';

function users_article_route($uri, $method) {
    $dbConnection = getDbConnection();
    // Обработка GET-параметра sections
//    if (isset($_GET['section']) && $_GET['section'] === 'feed') {
//        // Пагинация
//        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
//        $controller = new SearchController($dbConnection);
//        $controller->showFeed($currentPage);  // Передаем страницу
//        exit();
//    }
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
        case '/sections/popular-articles':
            $controller = new SearchController(getDbConnection());
            $controller->showPopularArticles();
            exit();
        case '/sections/popular-courses':
            $controller = new SearchController(getDbConnection());
            $controller->showPopularCourses();
            exit();
        case '/sections/popular-writers':
            $controller = new SearchController(getDbConnection());
            $controller->showPopularWriters();
            exit();
        case '/sections/feed':
            $controller = new SearchController(getDbConnection());
            $controller->showFeed();
            exit();
        case '/sections/tag-search':
            $controller = new SearchController(getDbConnection());
            $controller->showArticlesFilteredByTags();
            exit();
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
