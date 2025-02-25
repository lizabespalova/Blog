<?php

use controllers\authorized_users_controllers\CourseController;


require_once __DIR__ . '/../../config/config.php';

function courses_route($uri, $method) {
    $dbConnection = getDbConnection();

 switch (true) { // Используем true, чтобы удобно проверять условия
    case $method === 'GET' && preg_match('#^/my-courses/([a-zA-Z0-9_-]+)$#', $uri, $matches):
        $username = $matches[1];

        // Подключаем контроллер курсов
        $controller = new CourseController(getDbConnection());
        $controller->showUserCourses($username);
        exit();


        default:
            return false;
    }
}

// Получаем URI и метод запроса
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Запускаем маршрутизатор до вывода содержимого
courses_route($uri, $method);
?>
