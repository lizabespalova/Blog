<?php

namespace controllers\authorized_users_controllers;

use models\Articles;
use models\Reposts;
use models\User;
use Exception;
use services\MarkdownService;

class ProfileController
{
    private $userModel;
    private $articleModel;
    private $repostModel;

    private $markdownService;
    public function __construct($dbConnection) {
        $this->userModel = new User($dbConnection);
        $this->articleModel = new Articles($dbConnection);
        $this->repostModel = new Reposts($dbConnection);
        $this->markdownService = new MarkdownService();
    }

    public function showProfile($profileUserLogin)
    {
        try {
            // Проверка, установлен ли cookie с идентификатором пользователя
            if (!isset($_COOKIE['id'])) {
                throw new Exception("User not authenticated.");
            }

//            $user_id = intval($_COOKIE['id']);

            // Получение данных пользователя из базы данных
            $user = $this->userModel->get_user_by_login($profileUserLogin);
            $user_id = $user['user_id'];
            if (!$user) {
                throw new Exception($profileUserLogin );
            }
            $userArticlesCount = $this->userModel->getUserArticlesCount($user_id);

            $article_cards =  $this->repostModel->getReposts($user_id);
            $reposts = $article_cards;
            // Подключение шаблона и передача данных пользователя
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
