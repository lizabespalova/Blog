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

function footer_route($uri, $method) {

    $dbConnection = getDbConnection();
    switch ($uri) {
        case '/about':
            if ($method === 'GET') {
                $controller = new FooterController($dbConnection);
                $controller->showAboutPage();
                exit();
            }
        case '/contact':
            if ($method === 'GET') {
                $controller = new FooterController($dbConnection);
                $controller->showContactPage();
                exit();
            }
        default:
            return false;
    }
}

// Получаем URI и метод запроса
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Запускаем маршрутизатор до вывода содержимого
footer_route($uri, $method);
?>
