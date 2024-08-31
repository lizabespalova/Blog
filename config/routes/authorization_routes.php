<?php

use controllers\auth_controllers\ForgetPasswordController;
use controllers\auth_controllers\LoginController;
use controllers\auth_controllers\RegisterController;
use controllers\auth_controllers\ResetPasswordController;
use controllers\authorized_users_controllers\LogoutController;
use controllers\authorized_users_controllers\ProfileController;

require_once __DIR__ . '/../../config/config.php';

function authorization_route($uri, $method) {
    $dbConnection = getDbConnection();

    switch ($uri) {
        case '/profile':
            $controller = new ProfileController($dbConnection);
            if ($method === 'GET') {
                $controller->showProfile();
            }
            exit();  // Остановка выполнения после маршрута

        case '/login':
            $controller = new LoginController($dbConnection);
            if ($method === 'GET') {
                $controller->show_login_form();
            } elseif ($method === 'POST') {
                $controller->login();
            }
            exit();  // Остановка выполнения после маршрута

        case '/register':
            $controller = new RegisterController($dbConnection);
            if ($method === 'GET') {
                $controller->show_register_form();
            }
            else if ($method === 'POST') {
                $controller->register();
            }
            exit();  // Остановка выполнения после маршрута

        case '/forget':
            $controller = new ForgetPasswordController($dbConnection);
            if ($method === 'GET') {
                $controller->show_forget_form();
            }
            else if ($method === 'POST') {
                $controller->handleResetRequest();
            }
            exit();  // Остановка выполнения после маршрута

        case '/reset':
            $controller = new ResetPasswordController($dbConnection);
            if ($method === 'POST') {
                $controller->handleRequest();
            }

            exit();  // Остановка выполнения после маршрута

        case '/confirm':
            $controller = new RegisterController(getDbConnection());
            $controller->confirmRegistration();
            exit();

        case '/confirmation_pending':
            $controller = new RegisterController(getDbConnection());
            $controller->registration_pending();
            exit();

        case '/logout':
            $controller = new LogoutController(getDbConnection());
            $controller->logout();
            exit();
        default:
            return false;
    }
}

// Получаем URI и метод запроса
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Запускаем маршрутизатор до вывода содержимого
authorization_route($uri, $method);
?>
