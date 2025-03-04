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
    }

    public function showProfile($profileUserLogin)
    {
        require_once 'app/services/helpers/switch_language.php';
        try {
            // Проверка, что данные пользователя есть в сессии
            $currentUser = $_SESSION['user'] ?? null;

            // Получение данных пользователя из базы данных
            $user = $this->userModel->get_user_by_login($profileUserLogin);
            $profileUserId = $user['user_id'];
            if (!$user) {
                throw new Exception($profileUserLogin );
            }
            $userArticlesCount = $this->userModel->getUserArticlesCount($profileUserId);

            $article_cards =  $this->repostModel->getReposts($profileUserId);
            $reposts = $article_cards;
            $article_cards =  $this->userModel->getPublications($profileUserLogin);
            $publications = $article_cards;
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

            $courses = $this->courseModel->getUserCourses( $currentUser['user_id']);
            include __DIR__ . '/../../views/authorized_users/profile_template.php';
        } catch (Exception $e) {
            // Обработка ошибок
            echo "<script>
                alert('{$e->getMessage()}');
                window.location.href = '/login'; // Перенаправление на страницу логина
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

}
