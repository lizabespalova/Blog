<?php

use controllers\auth_controllers\ForgetPasswordController;
use controllers\auth_controllers\LoginController;
use controllers\auth_controllers\RegisterController;
use controllers\auth_controllers\ResetPasswordController;
use controllers\authorized_users_controllers\ArticleController;
use controllers\authorized_users_controllers\LogoutController;
use controllers\authorized_users_controllers\ProfileController;
use controllers\ErrorController;
use services\EmailService;

require_once __DIR__ . '/../../config/config.php';

function authorization_route($uri, $method) {
    $dbConnection = getDbConnection();

    switch ($uri) {

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
            $service = new EmailService();
            $service->confirmRegistration();
            exit();
        case '/update-email':
            $service = new EmailService();
            $service->confirmEmailUpdate();
            exit();
        case '/confirmation_pending':
            $controller = new RegisterController(getDbConnection());
            $controller->registration_pending();
            exit();
        case '/error':
            $controller = new ErrorController();
            $controller->show_error();
            exit();
        case '/success':
            $controller = new ErrorController();
            $controller->show_success();
            exit();
        case '/logout':
            $controller = new LogoutController();
            $controller->logout();
            exit();
        case '/google-register':
            $controller = new RegisterController($dbConnection);
            if ($method === 'GET' && isset($_GET['code'])) {
             $controller->handleGoogleCallback();
            } elseif ($method === 'GET') {
                $controller->redirectToGoogle("register");
            }
            exit(); // Остановка выполнения после маршрута
        case '/google-login':
            $controller = new RegisterController($dbConnection);
            if ($method === 'GET' && isset($_GET['code'])) {
                $controller->googleAuthorization();
            } elseif ($method === 'GET') {
                $controller->redirectToGoogle("login");
            }

            exit(); // Остановка выполнения после маршрута
        case '/set_password':
            $controller = new RegisterController($dbConnection);
            $controller->setPassword();
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
