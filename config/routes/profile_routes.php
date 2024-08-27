<?php

use controllers\auth_controllers\ForgetPasswordController;
use controllers\auth_controllers\LoginController;
use controllers\auth_controllers\RegisterController;
use controllers\auth_controllers\ResetPasswordController;
use controllers\authorized_users_controllers\EditController;
use controllers\authorized_users_controllers\EditProfileController;
use controllers\authorized_users_controllers\ProfileController;

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

        case '/update-main-description':
            $controller = new EditProfileController($dbConnection);
            if ($method === 'POST') {
                $controller->update_main_description();
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
