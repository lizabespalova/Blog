<?php
namespace controllers\authorized_users_controllers;

use models\User;
use Exception;
session_start();

class EditProfileController
{
    private $userModel;

    public function __construct($conn)
    {
        $this->userModel = new User($conn);
    }

    public function update_profile()
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
        $updateSuccess = $this->userModel->update_user_profile($user_login, [
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
    public function show_edit_form(){
        include __DIR__ . '/../../views/authorized_users/edit_description.php';
    }

    public function update_main_description()
    {
        // Проверка наличия description в POST запросе
        if (!isset($_POST['description'])) {
            error_log("No description provided.");
            echo "No description provided.";
            exit();
        }

        $description = trim($_POST['description']);
        $description = substr($description, 0, 500); // Ограничение длины

        // Сохранение в сессию
        $_SESSION['user']['user_description'] = $description;
        $userId = $_SESSION['user']['user_id'];

        // Обновление в базе данных через модель
        if (!$userId) {
            error_log("User ID not found in session.");
            echo "User ID not found in session.";
            exit();
        }


        if ($this->userModel->update_user_description($userId, $description)) {
            // Успешное обновление
            header('Location: /profile');
            exit();
        } else {
            // Логирование ошибки
            error_log("Error updating description.");
            echo "Error updating description.";
        }
    }

}