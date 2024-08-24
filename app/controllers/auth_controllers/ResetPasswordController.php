<?php
namespace controllers\auth_controllers;

require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ .'/../../../config/config.php';
session_start();

class ResetPasswordController {
    private $userModel;

    public function __construct($dbConnection) {
        $this->userModel = new \models\User($dbConnection);
    }

    public function handleRequest() {
        // Получаем данные из GET-запроса
        $data = $_GET;

        // Сохраняем данные в сессии для использования на форме
        $_SESSION['data'] = $data;

        // Проверяем наличие ключа в GET данных
        if (empty($data['key'])) {
            header('Location: /');
            exit;
        }

        $user = $this->userModel->getUserByKey($data['key']);

        // Проверка наличия пользователя с данным ключом
        if (!$user) {
            header('Location: /');
            exit;
        }

        // Обработка запроса на установку нового пароля
        if (isset($_POST['set_new_password'])) {
            $this->updatePassword($user);
        }
    }

    private function updatePassword($user) {
        $login = $user['user_login'];
        $newPassword = $_POST['password'];

        // Хеширование нового пароля
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Очищаем ключ для сброса пароля, чтобы он больше не использовался
        $this->userModel->setKey($login, NULL);

        // Обновляем пароль в базе данных
        $this->userModel->updatePassword($login, $hashedPassword);

        // Перенаправляем пользователя на страницу логина после успешного сброса пароля
        header('Location: /form_login.php');
        exit();
    }
}

//// Соединение с БД
//$conn = getDbConnection();
//
//// Создаем экземпляр контроллера и вызываем метод обработки запроса на сброс пароля
//$resetPasswordController = new ResetPasswordController($conn);
//$resetPasswordController->handleRequest();

?>
