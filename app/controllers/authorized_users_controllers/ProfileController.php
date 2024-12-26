<?php

namespace controllers\authorized_users_controllers;

use models\Articles;
use models\Follows;
use models\Reposts;
use models\User;
use Exception;
use services\MarkdownService;

class ProfileController
{
    private $userModel;
    private $articleModel;
    private $repostModel;
    private $followModel;

    private $markdownService;
    public function __construct($dbConnection) {
        $this->userModel = new User($dbConnection);
        $this->articleModel = new Articles($dbConnection);
        $this->repostModel = new Reposts($dbConnection);
        $this->followModel = new Follows($dbConnection);
        $this->markdownService = new MarkdownService();
    }

    public function showProfile($profileUserLogin)
    {
        try {
            session_start();
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

//            var_dump($publications);


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
