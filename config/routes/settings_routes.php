<?php

use controllers\authorized_users_controllers\ArticleController;
use controllers\authorized_users_controllers\ArticleImagesController;
use controllers\authorized_users_controllers\EditProfileController;
use controllers\authorized_users_controllers\FollowController;
use controllers\authorized_users_controllers\NotificationController;
use controllers\authorized_users_controllers\ProfileController;
use controllers\authorized_users_controllers\SettingsController;
use controllers\search_controllers\SearchController;

require_once __DIR__ . '/../../config/config.php';

function setting_route($uri, $method) {

    $dbConnection = getDbConnection();
//    var_dump($_SESSION['user']);

//    var_dump($uri);
    switch ($uri) {
        case '/settings':
            $controller = new SettingsController(getDbConnection());
            if ($method === 'GET') {
                $controller->showSettingsTemplate(); // Удаление старых уведомлений
            }
            exit(); // Остановка выполнения после маршрута
        case '/settings/save':
            $controller = new SettingsController(getDbConnection());
            if ($method === 'POST') {
                $controller->saveTheme();
            }
            exit(); // Остановка выполнения после маршрута
        case '/settings/font-size':
            $controller = new SettingsController(getDbConnection());
            if ($method === 'POST') {
                $controller->saveFontSize();
            }
            exit();
        case '/settings/font-style':
            $controller = new SettingsController(getDbConnection());
            if ($method === 'POST') {
                $controller->saveFontStyle();
            }
            exit();
        case '/settings/update-user':
            $controller = new SettingsController(getDbConnection());
            if ($method === 'POST') {
                $controller->updateUser();
            }
            exit();
        case '/settings/privacy':
            $controller = new SettingsController(getDbConnection());
            if ($method === 'POST') {
                $controller->updatePrivacySettings();
            }
            exit();
        default:
            return false;
    }
}

// Получаем URI и метод запроса
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Запускаем маршрутизатор до вывода содержимого
setting_route($uri, $method);
?>
