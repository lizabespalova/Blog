<?php

use controllers\authorized_users_controllers\ProfileController;

require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ .'/../../../config/config.php';

class RegisterController {

    private $userModel;

    public function __construct($dbConnection) {
        $this->userModel = new \models\User($dbConnection);
    }

    public function register() {
        if (isset($_POST['register'])) {
            $errors = $this->validateRegistration($_POST['login'], $_POST['password'], $_POST['email']);

            if (empty($errors)) {
                $this->createNewUser($_POST['login'], $_POST['email'], $_POST['password']);

                // После успешной регистрации вызываем метод showProfile
                $profileController = new ProfileController(getDbConnection());
                $profileController->showProfile();

                // Не используем header, так как мы уже показали профиль
                exit();
            } else {
                $this->showErrors($errors);
            }
        }
    }

    private function validateRegistration($login, $password, $email): array
    {
        $errors = [];

        // Проверяем логин
        if (!preg_match("/^[a-zA-Z0-9\-_]+$/", $login)) {
            $errors[] = "The login can consist only of Latin letters, numbers, hyphens, and underscores.";
        }

        if (strlen($login) < 3 || strlen($login) > 30) {
            $errors[] = "The login must be no less than 3 characters and no more than 30.";
        }

        // Проверяем, существует ли пользователь с таким логином
        if ($this->userModel->getUserByLogin($login)) {
            $errors[] = "A user with this login already exists in the database.";
        }

        // Дополнительная проверка на email и другие параметры можно добавить здесь

        return $errors;
    }

    private function createNewUser($login, $email, $password) {
        // Хешируем пароль
        $hashedPassword = password_hash(trim($password), PASSWORD_DEFAULT);

        // Создаем пользователя в базе данных
        $this->userModel->createUser($login, $email, $hashedPassword, 0);
    }

    private function showErrors($errors) {
        $errorMessages = implode("\\n", $errors); // Объединяем все ошибки в одну строку, разделяя их символом новой строки
        echo "<script>
            alert('The following errors occurred during registration:\\n{$errorMessages}');
            window.location.href = '/app/views/auth/form_register.php'; 
          </script>";
    }

}

// Соединение с БД
$conn = getDbConnection();

// Создаем экземпляр контроллера и вызываем метод регистрации
$registerController = new RegisterController($conn);
$registerController->register();

?>
