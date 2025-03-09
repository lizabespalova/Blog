<?php

use controllers\authorized_users_controllers\CourseController;


require_once __DIR__ . '/../../config/config.php';

function courses_route($uri, $method) {
    $dbConnection = getDbConnection();

 switch (true) { // Используем true, чтобы удобно проверять условия
    case $method === 'GET' && preg_match('#^/course-form/([a-zA-Z0-9_-]+)$#', $uri, $matches):
        $username = $matches[1];
        // Подключаем контроллер курсов
        $controller = new CourseController(getDbConnection());
        $controller->showUserCoursesForm($username);
        exit();
     case $method === 'POST' && $uri === '/create-course':
         $controller = new CourseController(getDbConnection());
         $controller->createCourse();  // Создаем новый курс
         exit();
     case $method === 'GET' && preg_match('#^/my-courses/([a-zA-Z0-9_-]+)$#', $uri, $matches):
         $controller = new CourseController(getDbConnection());
         $controller->showUserCourses();  // Показываем список курсов
         exit();
     case preg_match('/^\/course\/(\d+)$/', $uri, $matches):
         $courseId = $matches[1];
         $controller = new CourseController(getDbConnection());
         $controller->showCourse($courseId);
         exit();
     case $method === 'POST' && $uri === '/update-course':
         $controller = new CourseController(getDbConnection());
         $controller->updateCourse();  // Создаем новый курс
         exit();
     case $method === 'POST' && $uri === '/delete-course':
         $controller = new CourseController(getDbConnection());
         $controller->deleteCourse();  // Создаем новый курс
         exit();
     case $method === 'POST' && $uri === '/update_cover':
         $controller = new CourseController(getDbConnection());
         $controller->updateCoverCourse();  // Создаем новый курс
         exit();
     case $method === 'POST' && $uri === '/update_course_title':
         $controller = new CourseController(getDbConnection());
         $controller->updateTitleCourse();  // Создаем новый курс
         exit();
     case $method === 'POST' && $uri === '/update_course_description':
         $controller = new CourseController(getDbConnection());
         $controller->updateDescriptionCourse();  // Создаем новый курс
         exit();
     case $method === 'POST' && $uri === '/progress/save':
         $controller = new CourseController(getDbConnection());
         $controller->saveProgress();  // Создаем новый курс
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
