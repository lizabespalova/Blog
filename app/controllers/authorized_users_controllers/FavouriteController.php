<?php

namespace controllers\authorized_users_controllers;
use Exception;
use models\Articles;
use models\Courses;
use models\Favourites;
use models\Follows;
use models\Notifications;
use models\Settings;
use models\User;

require_once 'app/services/helpers/session_check.php';
class FavouriteController
{
    private $favouriteModel;
    private $userModel;
    private $articleModel;
    private $followModel;
    private $notificationModel;

    private $courseModel;
    private $settingModel;
    public function __construct($conn) {
        $this->favouriteModel = new Favourites($conn);
        $this->userModel = new User($conn);
        $this->articleModel = new Articles($conn);
        $this->followModel = new Follows($conn);
        $this->settingModel = new Settings($conn);
        $this->courseModel = new Courses($conn);
        $this->notificationModel = new Notifications($conn);
    }
    public function showFavourites(){
        require_once 'app/services/helpers/switch_language.php';
        // Получаем ID пользователя из сессии
        $userId = $_SESSION['user']['user_id'];
        // Получаем список избранных статей с деталями
        $article_cards = $this->favouriteModel->getUserFavoriteArticles($userId);
        include __DIR__ . '/../../views/authorized_users/favourites/favourite_template.php';
    }

    public function toggle($userId, $articleId): string
    {
        if ($this->favouriteModel->exists($userId, $articleId)) {
            $this->favouriteModel->remove($userId, $articleId);
            return 'removed';
        } else {
            $this->favouriteModel->add($userId, $articleId);
            // Если избранное, то добавить как интересы пользователя
            $article = $this->articleModel->getArticleById($articleId);
            $this->userModel->trackUserInterest($userId, $article['category']);
            return 'added';
        }
    }

    private function get_favourites_input(): array
    {
        return [
            'article_id' => $_POST['article_id'],
            'user_id' => $_SESSION['user']['user_id']
        ];
    }

    public function toggleFavourites() {

        header('Content-Type: application/json'); // Указываем, что возвращаем JSON

        try {
            $inputFavouriteData = $this->get_favourites_input(); // Получаем входные данные

            // Проверяем и переключаем состояние избранного
            $action = $this->toggle($inputFavouriteData['user_id'], $inputFavouriteData['article_id']);

            // Возвращаем JSON
            echo json_encode(['success' => true, 'action' => $action]);
        } catch (Exception $e) {
            // Возвращаем ошибку, если что-то пошло не так
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }

        exit; // Обязательно завершаем выполнение
    }

    public function filterFavourites() {
        $title = $_GET['title'] ?? null;
        $author = $_GET['author'] ?? null;
        $category = $_GET['category'] ?? null;
        $date_from = $_GET['date_from'] ?? null;
        $date_to = $_GET['date_to'] ?? null;

        if (!$title && !$author && !$category && !$date_from && !$date_to) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'No filters applied']);
            exit;
        }

        try {
            $results = $this->favouriteModel->getFilteredFavourites($title, $author, $date_from, $date_to, $category);

            // Если $results является объектом, преобразуем его в массив
            if (is_object($results)) {
                $results = json_decode(json_encode($results), true);
            }

            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'data' => $results]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function toggleFavoriteCourse() {
        require_once 'app/services/helpers/session_check.php';
        $userId = $_SESSION['user']['user_id'] ?? null;
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['course_id'], $data['action'])) {
            echo json_encode(["success" => false, "message" => "Invalid request"]);
            return;
        }

        $course_id = intval($data['course_id']);
        $action = $data['action'];
        $user = $this->userModel->get_user_by_id($userId);
        $course = $this->courseModel->getCourseById($course_id);
        if ($action === "add") {
            $this->favouriteModel->addCourseToFavourites($userId, $course_id);
            $message = 'User '.$user['user_login'].' saved your course';

            $this->notificationModel->addNotification($course['user_id'], $userId, 'favorite', $message, $course_id);

            echo json_encode(["success" => true, "action" => "added"]);
        } elseif ($action === "remove") {
            $this->favouriteModel->deleteCourseFromFavourites($userId, $course_id);
            echo json_encode(["success" => true, "action" => "removed"]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid action"]);
        }
    }
    public function showFavoriteCourses() {
        require_once 'app/services/helpers/session_check.php';
        require_once 'app/services/helpers/switch_language.php';
        $userId = $_SESSION['user']['user_id'] ?? null;
        $currentUser = $this->userModel->get_user_by_id($userId);
        if (!$userId) {
            header("Location: /login");
            exit;
        }

        $courses = $this->favouriteModel->getFavoriteCourses($userId);
        foreach ($courses as &$course) { // Добавили &
            $course['email'] = $this->userModel->getUserEmail($course['user_id']);
            $course['hideEmail'] = $this->settingModel->getHideEmail($course['user_id']);
            $course['favourites'] = $this->courseModel->getFavoritesCount($course['course_id']);
            if ($course['visibility_type'] === 'subscribers') {
                $course['isSubscriber'] =  $this->followModel->isFollowing($userId, $course['user_id']);
            } else {
                $course['isSubscriber'] = true; // Если курс не только для подписчиков, разрешаем доступ
            }
            $course['rating'] =$this->courseModel->getCourseRating($course['course_id']);
            $isAccessible = ($course['visibility_type'] === 'public' || // Курс публичный
                $course['user_id'] == ($_SESSION['user']['user_id'] ?? null) || // Владелец курса
                ($course['visibility_type'] === 'subscribers' && !empty($course['isSubscriber'])));
            if (!$isAccessible) {
                // Удаляем курс из избранного
                $this->favouriteModel->deleteCourseFromFavourites($userId, $course['course_id']);
            }
        }
        unset($course); // Разрываем ссылку после использования

        include __DIR__ . '/../../views/courses/favorite_courses.php';
    }

}