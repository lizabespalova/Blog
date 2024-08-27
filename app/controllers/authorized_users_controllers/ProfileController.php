<?php

namespace controllers\authorized_users_controllers;

use models\User;
use Exception;

class ProfileController
{
    private $userModel;

    public function __construct($dbConnection) {
        $this->userModel = new \models\User($dbConnection);
    }

    public function showProfile()
    {
        try {
            // Проверка, установлен ли cookie с идентификатором пользователя
            if (!isset($_COOKIE['id'])) {
                throw new Exception("User not authenticated.");
            }

            $user_id = intval($_COOKIE['id']);

            // Получение данных пользователя из базы данных
            $user = $this->userModel->get_user_by_id($user_id);

            if (!$user) {
                throw new Exception("User not found.");
            }

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
