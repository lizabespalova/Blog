<?php
namespace controllers\authorized_users_controllers;

use models\User;
use Exception;
session_start();

class EditProfileController
{
    private $conn;
    private $userModel;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->userModel = new User($conn);
    }

    public function updateProfile()
    {
        // Проверяем авторизацию пользователя
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_login'])) {
            http_response_code(403); // Доступ запрещен
            exit();
        }

        $user_login = $_SESSION['user']['user_login'];

        // Получаем данные из POST-запроса
        $user_specialisation = $_POST['user_specialisation'] ?? '';
        $user_company = $_POST['user_company'] ?? '';
        $user_experience = $_POST['user_experience'] ?? '';

        // Проводим валидацию данных
        if (empty($user_specialisation) || empty($user_company) || !is_numeric($user_experience) ) {
            http_response_code(400); // Неверный запрос
            exit();
        }

        // Обновляем данные пользователя в базе данных
        $updateSuccess = $this->userModel->updateUserProfile($user_login, [
            'user_specialisation' => $user_specialisation,
            'user_company' => $user_company,
            'user_experience' => $user_experience,
        ]);

        if ($updateSuccess) {
            $_SESSION['user']['user_specialisation'] = $user_specialisation;
            $_SESSION['user']['user_company'] = $user_company;
            $_SESSION['user']['user_experience'] = $user_experience;

            http_response_code(200); // Успешное обновление
        } else {
            http_response_code(500); // Внутренняя ошибка сервера
        }
    }
}