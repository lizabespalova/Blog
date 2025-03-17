<?php

namespace controllers\authorized_users_controllers;

use models\Articles;
use models\Courses;
use models\User;
use services\CoverImagesService;

require_once 'app/services/helpers/session_check.php';

class CourseController
{
    private $courseModel;
    private $userModel;

    private $articleModel;
    private $coverImagesService;


    public function __construct($conn) {
        $this->courseModel = new Courses($conn);
        $this->articleModel = new Articles($conn);
        $this->coverImagesService = new CoverImagesService();
        $this->userModel = new User($conn);
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
        $userLogin = $this->userModel->getLoginById($userId);
        // Загружаем шаблон
        require_once 'app/views/courses/my_courses.php';
    }
    public function showCourse($courseId) {
        require_once 'app/services/helpers/switch_language.php';
        $userId = $_SESSION['user']['user_id'] ?? null;
        $userlogin = $this->userModel->getLoginById($userId);
        $course = $this->courseModel->getCourseById($courseId);
        $articlesInCourses = $this->courseModel->getArticlesByCourseId($courseId);
        $articles = $this->articleModel->getUserArticles($userlogin);
        if (!$course) {
            echo "Course wasn`t find";
            return;
        }
        // Получаем прогресс курса для пользователя
        $progress = $this->courseModel->getCourseProgress($userId, $courseId);
        // Получаем информацию о завершении статей
        $completedArticles = $this->courseModel->getCompletedArticlesForUser($userId, $courseId);
        $materials = $this->courseModel->getMaterials($courseId);
//        var_dump($completedArticles);
        require_once 'app/views/courses/course_view.php';
    }
    public function updateCourse() {
        // Получаем данные из POST-запроса
        $userId = $_SESSION['user']['user_id'] ?? null;

        $courseId = $_POST['course_id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $coverImage = $_POST['cover_image'];
        $articles = $_POST['articles'];

        // Проверка на валидность данных
        if (empty($courseId) || empty($title) || empty($description) || empty($coverImage) || empty($articles)) {
            echo json_encode(['success' => false, 'error' => 'Missing course data']);
            return;
        }

        // Преобразуем список статей из строки в массив
        $articlesArray = $articles ? explode(',', $articles) : [];

        // Удаляем старые статьи из курса
        $this->courseModel->deleteArticlesFromCoursesArticles($courseId);

        // Обновляем курс с новыми данными
        $result = $this->courseModel->updateCourse($courseId,$userId, $title, $description, $coverImage, $articlesArray);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Course updated successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update course']);
        }
    }
    public function deleteCourse() {
        // Проверяем, передан ли course_id через POST-запрос
        if (isset($_POST['course_id'])) {
            $courseId = $_POST['course_id'];

            // Создаем экземпляр модели и вызываем метод для удаления курса
            $this->courseModel->deleteArticlesFromCoursesArticles($courseId);
            $result = $this->courseModel->deleteCourseById($courseId);

            // Если курс был успешно удален
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Course deleted successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete course.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Course ID is missing.']);
        }
    }


    public function updateCoverCourse() {
        $userId = $_SESSION['user']['user_id'] ?? null;


        if (!isset($_FILES['cover_image']) || !isset($_POST['course_id'])) {
            echo json_encode(['success' => false, 'error' => 'Нет данных']);
            return;
        }

        $courseId = (int) $_POST['course_id'];

        // Проверяем, принадлежит ли курс пользователю
        $course = $this->courseModel->getCourseById($courseId);

        if (!$course || $course['user_id'] !== $userId) {
            echo json_encode(['success' => false, 'error' => 'Курс не найден или нет доступа']);
            return;
        }

        // Сохраняем файл
        if (!empty($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $cover_image_path = $this->coverImagesService->upload_cover_image($_FILES['cover_image'], 'uploads/' . $userId . '/courses/' . $courseId);
        }

        $this->courseModel->updateCoverImage($courseId, $cover_image_path);
        // Обновляем базу
        echo json_encode(['success' => true, 'message' => 'Курс успешно создан!', 'course_id' => $courseId]);

    }

    public function updateTitleCourse() {
        $courseId = $_POST['course_id'] ?? null;
        $newTitle = trim($_POST['title'] ?? '');
        if (!$courseId || !$newTitle) {
            echo json_encode(["success" => false, "error" => "Invalid data"]);
            return;
        }

        // Обновляем заголовок
        if ( $this->courseModel->updateCourseTitel($newTitle, $courseId)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Failed to update title", "title" => $newTitle, "course_id" => $courseId
            ]);
        }
    }
    public function updateDescriptionCourse() {
        $courseId = $_POST['course_id'] ?? null;
        $newDesc = trim($_POST['description'] ?? '');

        if (!$courseId || !$newDesc) {
            echo json_encode(["success" => false, "error" => "Invalid data"]);
            return;
        }

        // Обновляем описание в базе
        if ($this->courseModel->updateCourseDescription($newDesc, $courseId)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Failed to update description"]);
        }
    }
    public function saveProgress(){
        $data = json_decode(file_get_contents('php://input'), true);

        $userId = $_SESSION['user']['user_id'] ?? null;


        $course_id = $data['course_id'] ?? null;
        $article_id = $data['article_id'] ?? null;
        $video_link = $data['video_link'] ?? null;
        $is_completed = $data['is_completed'] ?? 0;

        $this->courseModel->saveProgress($userId, $course_id, $article_id, $video_link, $is_completed);

        echo json_encode(['message' => "Прогресс сохранён {$article_id}"]);
    }
}