<?php

namespace controllers\authorized_users_controllers;
require_once 'app/services/helpers/update_status.php';

use Google\Service\Classroom\Course;
use models\Articles;
use models\Courses;
use models\Follows;
use models\Notifications;
use models\Reposts;
use models\User;
use Exception;
use services\MarkdownService;
use services\StatusService;
use models\Settings;
class ProfileController
{
    private $userModel;
    private $articleModel;
    private $repostModel;
    private $followModel;
    private $courseModel;
    private $settingModel;
    private $notificationModel;
    private $statusService;
    private $courseController;

    private $markdownService;
    public function __construct($dbConnection) {
        $this->userModel = new User($dbConnection);
        $this->articleModel = new Articles($dbConnection);
        $this->repostModel = new Reposts($dbConnection);
        $this->followModel = new Follows($dbConnection);
        $this->settingModel = new Settings($dbConnection);
        $this->courseModel = new Courses($dbConnection);
        $this->notificationModel = new Notifications($dbConnection);
        $this->markdownService = new MarkdownService();
        $this->statusService = new StatusService();
        $this->courseController = new CourseController($dbConnection);
    }

    public function showProfile($profileUserLogin)
    {
        require_once 'app/services/helpers/switch_language.php';
        try {
            // Проверка, что данные пользователя есть в сессии
            $userId = $_SESSION['user']['user_id'] ?? null;
            $currentUser = $this->userModel->get_user_by_id($userId);
            // Получение данных пользователя из базы данных
            $user = $this->userModel->get_user_by_login($profileUserLogin);
            $profileUserId = $user['user_id'];
            $avatar = $this->userModel->get_author_avatar($profileUserLogin);
//            $email = $this->userModel->getUserEmail($profileUserId);

            if (!$user) {
                throw new Exception($profileUserLogin );
            }
            $userArticlesCount = $this->userModel->getUserArticlesCount($profileUserId);

            $reposts =  $this->repostModel->getReposts($profileUserId);

            $articles = $this->articleModel->getUserPublicatedArticles($profileUserLogin);
            if (!empty($articles)) {
                foreach ($articles as $key => $article) {
                    $articles[$key]['parsed_content'] = $this->markdownService->parseMarkdown($article['content']);
                }
            }
            $followersCount = $this->followModel->getFollowersCount($profileUserId);
            $followingCount = $this->followModel->getFollowingCount($profileUserId);
            $isFollowing = false; // По умолчанию считаем, что пользователь не подписан

            if (!empty($currentUser) && isset($currentUser['user_id'])) {
                $isFollowing = $this->followModel->isFollowing($currentUser['user_id'], $profileUserId);
            }

            $user['is_online'] = $this->statusService->isUserOnline($user['last_active_at']);
            $profileStatus = $this->settingModel->getShowLastSeen($profileUserId);
            $profileVisibility = $this->settingModel->getProfileVisibility($profileUserId);
            $followStatus = 'none'; // По умолчанию нет запроса на подписку

            if (!empty($currentUser) && isset($currentUser['user_id'])) {
                $followStatus = $this->followModel->getFollowRequestStatus($profileUserId, $currentUser['user_id']);
            }
//            var_dump($profileVisibility);
//            var_dump($followStatus);
//            var_dump($isFollowing);
//            var_dump($followStatus);

            $courses = $this->courseModel->getUserCourses($profileUserId);
            // Для каждого курса получаем email и настройки скрытия email
            foreach ($courses as &$course) { // Добавили &
                $course['email'] = $this->userModel->getUserEmail($course['user_id']);
                $course['hideEmail'] = $this->settingModel->getHideEmail($course['user_id']);
                if ($course['visibility_type'] === 'subscribers') {
                    $course['isSubscriber'] =  $this->followModel->isFollowing($userId, $course['user_id']);
                } else {
                    $course['isSubscriber'] = true; // Если курс не только для подписчиков, разрешаем доступ
                }
                $course['rating'] =$this->courseModel->getCourseRating($course['course_id']);
                $course['favourites'] = $this->courseModel->getFavoritesCount($course['course_id']);
            }
            unset($course); // Разрываем ссылку после использования
            $userLocation = $this->settingModel->getLocation($profileUserId); // Предполагаем, что эта функция извлекает страну и город

            include __DIR__ . '/../../views/authorized_users/profile_template.php';
        } catch (Exception $e) {
            // Безопасно экранируем сообщение
            $errorMessage = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
            echo "<script>
            alert('$errorMessage');
            window.location.href = '/login';
        </script>";
            exit();
        }
    }
    public function deleteAccount(){
        $user = $_SESSION['user'] ?? null;
        $user_id = $user['user_id'];

        $password = $_POST['password'] ?? '';
        $userData = $this->userModel->get_user_by_id($user_id);

        if (empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'Password is required.']);
            exit;
        }

        if (password_verify($password, $userData['user_password'])) {
            $this->userModel->deleteAccount($user_id);
            $this->deleteUploads($user_id);
            session_destroy();

            // Отправляем JSON и завершаем выполнение
            echo json_encode([
                'status' => 'success',
                'message' => 'Your account deleted successfully'
            ]);
            exit;  // <== Завершаем выполнение, чтобы PHP не выполнял дальше код
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Incorrect password'
            ]);
            exit;
        }
    }
    public function deleteUploads($user_id) {
        $userFolder = 'uploads/' . $user_id;

        if (is_dir($userFolder)) {
            $this->deleteFolder($userFolder);
        }
    }

    private function deleteFolder($folder) {
        if (!is_dir($folder)) {
            return;
        }

        $files = array_diff(scandir($folder), array('.', '..'));

        foreach ($files as $file) {
            $filePath = "$folder/$file";
            if (is_dir($filePath)) {
                $this->deleteFolder($filePath); // Рекурсивное удаление вложенных папок
            } else {
                unlink($filePath); // Удаление файла
            }
        }

        rmdir($folder); // Удаление пустой папки
    }
}
