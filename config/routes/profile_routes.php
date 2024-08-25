<?php

use controllers\auth_controllers\ForgetPasswordController;
use controllers\auth_controllers\LoginController;
use controllers\auth_controllers\RegisterController;
use controllers\auth_controllers\ResetPasswordController;
use controllers\authorized_users_controllers\EditController;
use controllers\authorized_users_controllers\ProfileController;

require_once __DIR__ . '/../../config/config.php';

function profile_route($uri, $method) {
    $dbConnection = getDbConnection();

    switch ($uri) {
        case '/edit':
            $controller = new EditController($dbConnection);
            if ($method === 'GET') {
                $controller->show_edit();
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
