<?php
require_once __DIR__ . '/config/routes/authorization_routes.php';
require_once __DIR__ . '/config/routes/profile_routes.php';
require_once __DIR__ . '/config/routes/settings_routes.php';
require_once __DIR__ . '/config/routes/article_routes.php';
require_once __DIR__ . '/config/routes/comments_routes.php';
require_once __DIR__ . '/config/routes/favourites_routes.php';
require_once __DIR__ . '/config/routes/users_articles_routes.php';
require_once __DIR__ . '/config/routes/notifications_routes.php';
require_once __DIR__ . '/config/routes/courses_routes.php';


// Подключение к базе данных
$dbConnection = getDbConnection();

// Получаем URI и метод запроса
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Запускаем маршрутизатор
authorization_route($uri, $method);
profile_route($uri, $method);
session_start(); // Включаем сессию

// Проверяем, авторизован ли пользователь
$isAuthenticated = isset($_SESSION['user']['user_id']);

require_once 'app/services/helpers/switch_language.php';

// Рендерим основную страницу
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lisas Blog</title>
</head>
<body>

<?php include('app/views/search/form_search.php'); ?>


</body>
</html>
