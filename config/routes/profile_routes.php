<?php

use controllers\authorized_users_controllers\ArticleController;
use controllers\authorized_users_controllers\ArticleImagesController;
use controllers\authorized_users_controllers\EditProfileController;
use controllers\authorized_users_controllers\FollowController;
use controllers\authorized_users_controllers\ProfileController;
use controllers\search_controllers\SearchController;

require_once __DIR__ . '/../../config/config.php';

function profile_route($uri, $method) {
    $dbConnection = getDbConnection();
    //error_log("URI: $uri, Method: $method");

    switch ($uri) {
        case (preg_match('/^\/profile\/([\w-]+)$/', $uri, $matches) ? true : false):
            $controller = new ProfileController($dbConnection);
            if ($method === 'GET') {
                $userLogin = rawurldecode($matches[1]); // Декодируем значение
                $controller->showProfile($userLogin);  // Передаем декодированный логин
            }
            exit();  // Остановка выполнения после маршрута
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
        case '/follow/([\d]+)':  // Путь для подписки на пользователя
            $controller = new FollowController($dbConnection);
            if ($method === 'POST') {
                $followerId = $_SESSION['user_id'];  // ID текущего пользователя
                $followedId = $matches[1];  // ID пользователя, на которого подписываются
                $controller->follow($followerId, $followedId);
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
profile_route($uri, $method);
?>
