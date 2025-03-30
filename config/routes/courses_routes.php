<?php

use controllers\authorized_users_controllers\CourseController;
use controllers\authorized_users_controllers\UserArticleController;


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
     case $method === 'POST' && $uri === '/upload-material-course':
         $controller = new CourseController(getDbConnection());
         $controller->saveMaterials();
         exit();
     case $method === 'POST' && $uri === '/delete_material':
         $controller = new CourseController(getDbConnection());
         $controller->deleteMaterials();
         exit();
     // Маршрут для обработки лайка для курса
     case $method === 'POST' && $uri === '/courses/react':
         // Получаем данные из POST запроса (для лайка или дизлайка)
         $controller = new CourseController(getDbConnection());
         $controller->reactToCourse();
         exit();
         // Статистика по курсам (GET)
     case $method === 'GET' && preg_match('#^/courses/statistics/([^/]+)$#', $uri, $matches):
             $courseId = $matches[1];
             $controller = new CourseController(getDbConnection());
             $controller->showStatistics($courseId);
             exit;
     case $method === 'GET' && preg_match('#^/course/(\d+)/reactions$#', $uri, $matches):
         $courseId = (int)$matches[1];
         $controller = new CourseController(getDbConnection());
         $controller->getReactions($courseId);
         exit;
     case $method === 'GET' && preg_match('#^/search-users$#', $uri):
         $query = $_GET['query'] ?? '';
         $controller = new UserArticleController(getDbConnection());
         $controller->searchUsers($query);
         exit;
     case $method === 'POST' && preg_match('#^/save-visibility$#', $uri):
         $controller = new CourseController(getDbConnection());
         $controller->saveVisibility($_POST);
         exit;
     case $method === 'POST' && preg_match('#^/update-course-status$#', $uri):
         $controller = new CourseController(getDbConnection());
         $controller->updateCourseStatus($_POST); // Параметры передаются через POST
         exit;
     case $method === 'GET' && preg_match('#^/courses/get-subscribers/(\d+)$#', $uri, $matches):
         $courseId = $matches[1];
         $controller = new CourseController(getDbConnection());
         $controller->getCourseSubscribers($courseId);
         exit;
     case $method === 'POST' && $uri === '/courses/remove-subscriber':
         $data = json_decode(file_get_contents('php://input'), true);
         if (!isset($data['user_id'], $data['course_id'])) {
             echo json_encode(['success' => false, 'error' => 'Missing parameters']);
             http_response_code(400);
             exit;
         }
         $controller = new CourseController(getDbConnection());
         $controller->removeSubscriber((int) $data['user_id'], (int) $data['course_id']);
         exit;

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
