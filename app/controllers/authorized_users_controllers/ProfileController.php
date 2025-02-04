<?php

namespace controllers\authorized_users_controllers;
require_once 'app/services/helpers/update_status.php';

use models\Articles;
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
        $this->notificationModel = new Notifications($dbConnection);
        $this->markdownService = new MarkdownService();
        $this->statusService = new StatusService();
    }

    public function showProfile($profileUserLogin)
    {
        require_once 'app/services/helpers/switch_language.php';
        try {
            // Проверка, что данные пользователя есть в сессии
            if (isset($_SESSION['user'])) {
                $user = $_SESSION['user']; // Получаем данные из сессии
                /*                print_r($user);*/
            } else {
                // Если пользователь не аутентифицирован
                header('Location: /login');
                exit();
            }
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
            $isFollowing = $this->followModel->isFollowing($_SESSION['user']['user_id'], $profileUserId);
            $user['is_online'] = $this->statusService->isUserOnline($user['last_active_at']);
            $profileStatus = $this->settingModel->getShowLastSeen($profileUserId);
            $profileVisibility = $this->settingModel->getProfileVisibility($profileUserId);
            $followStatus = $this->followModel->getFollowRequestStatus($profileUserId, $_SESSION['user']['user_id']); // Проверка активного запроса
//            var_dump($profileVisibility);
//            var_dump($followStatus);
//            var_dump($isFollowing);
//            var_dump($followStatus);


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
}
