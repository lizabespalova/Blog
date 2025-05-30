<?php

use controllers\authorized_users_controllers\ArticleController;
use controllers\authorized_users_controllers\ArticleImagesController;
use controllers\authorized_users_controllers\EditProfileController;
use controllers\authorized_users_controllers\FollowController;
use controllers\authorized_users_controllers\NotificationController;
use controllers\authorized_users_controllers\ProfileController;
use controllers\authorized_users_controllers\SettingsController;
use controllers\FooterController;
use controllers\search_controllers\SearchController;

require_once __DIR__ . '/../../config/config.php';

function profile_route($uri, $method) {

    $dbConnection = getDbConnection();
//    var_dump($_SESSION['user']);

//    var_dump($uri);
    switch ($uri) {

        case (explode('/', $uri)[1] == 'follow' && is_numeric(explode('/', $uri)[2])):
            session_start();
            header('Content-Type: application/json'); // Установить JSON-заголовок
            $controller = new FollowController($dbConnection);

            if ($method === 'POST') {
                $followerId = $_SESSION['user']['user_id'] ?? null;
                $followedId = explode('/', $uri)[2];
                $controller->follow($followerId, $followedId);
            }
            exit();
        case preg_match('/^\/user\/(\d+)\/followers$/', $uri, $matches) ? true : false:
            $userId = (int)$matches[1];
            $controller = new FollowController($dbConnection);
            $controller->showFollowers($userId);
            exit();

        case preg_match('/^\/user\/(\d+)\/followings$/', $uri, $matches) ? true : false:
            $userId = (int)$matches[1];
            $controller = new FollowController($dbConnection);
            $controller->showFollowings($userId);
            exit();
        case (explode('/', $uri)[1] == 'unfollow' && is_numeric(explode('/', $uri)[2])):
            session_start();
            header('Content-Type: application/json'); // Установить JSON-заголовок
            $controller = new FollowController($dbConnection);
            if ($method === 'POST') {
                $followerId = $_SESSION['user']['user_id'] ?? null;
                $followedId = explode('/', $uri)[2];
                $controller->unfollow($followerId, $followedId);
            }
            exit();


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
        case '/search-item':
            $controller = new SearchController($dbConnection);
            if ($method === 'GET') {
                $controller->search();
            }
            exit();
        case '/remove-follower':
            $controller = new FollowController($dbConnection);
            session_start();
            $userId = $_SESSION['user']['user_id']; // Текущий пользователь (владелец профиля)
            $followerId = $_POST['follower_id']; // Тот, кого мы удаляем из подписчиков
            $controller->unfollow($followerId, $userId); // Вызов метода
            exit();
        case (explode('/', $uri)[1] == 'cancel-follow-request' && is_numeric(explode('/', $uri)[2])):
        $controller = new FollowController($dbConnection);
        if ($method === 'POST') {
            session_start();
            $followerId = $_SESSION['user']['user_id'] ?? null;
            $followedId = explode('/', $uri)[2];
            $controller->cancelFollowRequest($followerId, $followedId);
        }
        exit();
        case '/delete_account':
            $controller = new ProfileController(getDbConnection());
            $controller->deleteAccount();
            exit();
        case '/privacy-policy':
            $controller = new FooterController();
            $controller->showPrivacyPolicy();
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
