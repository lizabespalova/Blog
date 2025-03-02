<?php

namespace controllers\authorized_users_controllers;

use models\Articles;
use models\Courses;
use services\CoverImagesService;

require_once 'app/services/helpers/session_check.php';

class CourseController
{
    private $courseModel;
    private $articleModel;
    private $coverImagesService;


    public function __construct($conn) {
        $this->courseModel = new Courses($conn);
        $this->articleModel = new Articles($conn);
        $this->coverImagesService = new CoverImagesService();
    }
    public function showUserCoursesForm($username) {
        require_once 'app/services/helpers/switch_language.php';
        // Получаем список избранных статей с деталями
        $articles= $this->articleModel->getUserArticles($username);
        require_once 'app/views/courses/courses_form.php';
    }
    public function createCourse() {
        header('Content-Type: application/json'); // Указываем JSON-ответ
        $courseTitle = $_POST['course_title'] ?? '';
        $courseDescription = $_POST['course_description'] ?? '';
        $articles = $_POST['articles'] ?? '';

        $userId = $_SESSION['user']['user_id'] ?? null;

        if (!$userId || empty($courseTitle) || empty($courseDescription)) {
            echo json_encode(['success' => false, 'message' => 'Заполните все обязательные поля!']);
            return;
        }

        $articlesArray = $articles ? explode(',', $articles) : [];

        $courseId = $this->courseModel->save($userId, $courseTitle, $courseDescription, null, $articlesArray);

        if (!$courseId) {
            echo json_encode(['success' => false, 'message' => 'Ошибка при создании курса.']);
            return;
        }

        $cover_image_path = 'templates/images/article_logo.png';

        if (!empty($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $cover_image_path = $this->coverImagesService->upload_cover_image($_FILES['cover_image'], 'uploads/' . $userId . '/courses/' . $courseId);
        }

        $this->courseModel->updateCoverImage($courseId, $cover_image_path);

        echo json_encode(['success' => true, 'message' => 'Курс успешно создан!', 'course_id' => $courseId]);
    }

    public function showUserCourses(){
        require_once 'app/services/helpers/switch_language.php';

        $userId = $_SESSION['user']['user_id'] ?? null;
        // Получаем список курсов пользователя
        $courses = $this->courseModel->getUserCourses($userId);

        // Загружаем шаблон
        require_once 'app/views/courses/my_courses.php';
    }
    public function showCourse($courseId) {
        require_once 'app/services/helpers/switch_language.php';
        $userId = $_SESSION['user']['user_id'] ?? null;

        $course = $this->courseModel->getCourseById($courseId);
        $articles = $this->courseModel->getArticlesByCourseId($courseId);

        if (!$course) {
            echo "Course wasn`t find";
            return;
        }

        require_once 'app/views/courses/course_view.php';
    }

}