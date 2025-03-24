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

        // Фильтруем курсы по видимости
        $filteredCourses = $this->getFilteredCourses($courses, $userId);

        // Загружаем шаблон с отфильтрованными курсами
        require_once 'app/views/courses/my_courses.php';
    }

    public function getFilteredCourses($courses, $userId){
         return array_filter($courses, function($course) use ($userId) {
             $currentUserId = $userId;

             // Хозяин курса всегда может его видеть
             if ($course['user_id'] == $currentUserId) {
                 return true;
             }

             // Проверка на тип видимости
             switch ($course['visibility_type']) {
                 case 'public':
                     return true;
                 case 'subscribers':
                     return $this->courseModel->isUserSubscribedToCourse($currentUserId, $course['course_id']); // Функция для проверки подписки
                 case 'custom':
                     return $this->courseModel->isUserHasCustomAccess($currentUserId, $course['course_id']); // Функция для проверки доступа через course_custom_access
                 default:
                     return false;
             }
         });
    }
    public function showCourse($courseId) {
        require_once 'app/services/helpers/switch_language.php';
        $userId = $_SESSION['user']['user_id'] ?? null;
        $course = $this->courseModel->getCourseById($courseId);
        $userlogin = $this->userModel->getLoginById($course['user_id']);

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

        // Добавим данные автора в массив $course
        $author = $this->userModel->get_author_avatar($userlogin);
        // Получаем количество лайков и дизлайков
        $likes = $this->courseModel->getLikesForCourse($courseId);
        $dislikes = $this->courseModel->getDislikesForCourse($courseId);

        // Добавляем данные о лайках и дизлайках в курс
        $course['likes'] = $likes;
        $course['dislikes'] = $dislikes;

        $course['author_avatar'] = $author['user_avatar'] ?? '/templates/images/profile.jpg';
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
    public function saveMaterials()
    {
        $courseId = $_POST['course_id'] ?? null;
        $userId = $_SESSION['user']['user_id'] ?? null;
        $description = trim($_POST['description'] ?? '');

        if (!$courseId || !$userId || empty($_FILES['material_files'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Недостаточно данных для загрузки']);
            return;
        }
        $uploadDir =  'uploads/' . $userId . '/courses/' . $courseId . '/materials/';
        $this->coverImagesService->create_user_directory($uploadDir);

        $uploadedFiles = $_FILES['material_files'];
        $successFiles = [];

        for ($i = 0; $i < count($uploadedFiles['name']); $i++) {
            $originalName = basename($uploadedFiles['name'][$i]);
            $tmpPath = $uploadedFiles['tmp_name'][$i];
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $safeFileName = uniqid('material_', true) . '.' . $extension;
            $targetPath = $uploadDir . $safeFileName;

            if (move_uploaded_file($tmpPath, $targetPath)) {
                $successFiles = $this->courseModel->createMaterials($courseId, $userId, $safeFileName, $originalName, $description);
            }
        }

        if (count($successFiles)) {
            echo json_encode(['success' => true, 'files' => $successFiles]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Не удалось сохранить материалы']);
        }
    }

    public function deleteMaterials()
    {
        $materialId = $_POST['material_id'] ?? null;
        $userId = $_SESSION['user']['user_id'] ?? null;

        if (!$materialId || !$userId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Недостаточно данных']);
            return;
        }

        // Получаем материал
        $material = $this->courseModel->getMaterialById($materialId);
        if (!$material) {
            echo json_encode(['success' => false, 'error' => 'Материал не найден']);
            return;
        }

        // Проверка владельца
        if ($material['user_id'] != $userId) {
            echo json_encode(['success' => false, 'error' => 'У вас нет прав на удаление этого материала']);
            return;
        }

        // Удаляем файл
        $filePath = 'uploads/' . $userId . '/courses/' . $material['course_id'] . '/materials/' . $material['file_name'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Удаляем запись из базы
        $this->courseModel->deleteMaterialById($materialId);

        echo json_encode(['success' => true]);
    }

    public function reactToCourse() {
        // Получаем данные из запроса
        $input = json_decode(file_get_contents('php://input'), true);

        $reactionType = $input['reaction_type'] ?? null;
        $courseId = $input['course_id'] ?? null;
        $userId = $input['user_id'] ?? null;

        // Проверка существующей реакции
        $existingReaction = $this->courseModel->getReaction($courseId, $userId);

        if ($existingReaction !== null) {
            if ($existingReaction['reaction'] === $reactionType) {
                // Если нажали повторно — удаляем реакцию
                $this->courseModel->removeReaction($courseId, $userId);
            } else {
                // Обновляем реакцию на другую
                $this->courseModel->updateReaction($courseId, $userId, $reactionType);
            }
        } else {
            // Если реакции нет — создаём
            $this->courseModel->addReaction($courseId, $userId, $reactionType);
        }

        // Получаем количество лайков и дизлайков
        $likes = $this->courseModel->getLikesForCourse($courseId);
        $dislikes = $this->courseModel->getDislikesForCourse($courseId);

        // Отправляем успешный ответ с обновленными значениями лайков и дизлайков
        echo json_encode([
            'success' => true,
            'likes' => $likes,
            'dislikes' => $dislikes
        ]);
        exit();
    }
    public function showStatistics(int $courseId): void
    {
        require_once 'app/services/helpers/switch_language.php';

        $course = $this->courseModel->getCourseById($courseId);
        if (!$course) {
            http_response_code(404);
            echo "Course not found";
            return;
        }
        $user = $this->userModel->get_user_by_id($course['user_id']);
        $statistics = [
            'likes' => $this->courseModel->getCourseLikes($courseId),
            'dislikes' => $this->courseModel->getCourseDislikes($courseId),
            'popular_articles' => $this->articleModel->getPopularArticlesByCourseID($courseId),

        ];

        require_once 'app/views/courses/statistic_courses_template.php';
    }
    public function getReactions($courseId){
        // Получаем данные из модели
        $likes = $this->courseModel->getLikesById($courseId);
        $dislikes = $this->courseModel->getDislikesById($courseId);

        // Возвращаем данные в формате JSON
        header('Content-Type: application/json');
        echo json_encode([
            'likes' => $likes,
            'dislikes' => $dislikes
        ]);
    }
    // Сохранение настроек видимости курса
    public function saveVisibility($data) {
        $course_id = intval($data['course_id'] ?? 0);
        $visibility_type = $data['visibility'] ?? 'public';
        $user_ids = json_decode($data['user_ids'] ?? '[]', true);

        if (!$course_id) {
            echo json_encode(['success' => false, 'error' => 'Отсутствует course_id']);
            return;
        }

        // Обновляем тип видимости
        $this->courseModel->updateVisibilityType($visibility_type, $course_id, $user_ids);

        echo json_encode(['success' => true]);
    }


    // Метод для обновления статуса курса
    public function updateCourseStatus($data) {
        $courseId = intval($data['course_id'] ?? 0);
        $status = intval($data['status'] ?? 0); // 0 - закрыт, 1 - открыт

        if ($courseId > 0) {
            $this->courseModel->updateIsPublishedType($status, $courseId);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $courseId]);
        }
    }
    public function getCourseSubscribers($courseId) {
        // Получаем подписчиков курса из базы данных
        $subscribers = $this->courseModel->getSubscribersByCourseId($courseId);
        // Возвращаем результат, например, в формате JSON
        echo json_encode($subscribers);
    }

}