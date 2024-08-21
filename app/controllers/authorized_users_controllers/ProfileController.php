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
            $user = $this->userModel->getUserById($user_id);

            if (!$user) {
                throw new Exception("User not found.");
            }

            // Подключение шаблона и передача данных пользователя
            include __DIR__ . '/../../views/authorized_users/profile_template.php';
        } catch (Exception $e) {
            // Обработка ошибок
            echo $e->getMessage();  // В реальной среде лучше использовать логирование или редирект на страницу с ошибкой
            // header("Location: /error.php");  // Например, перенаправление на страницу с ошибкой
            exit();
        }
    }
}
