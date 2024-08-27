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
        // Получаем данные из POST-запроса
        $data = $_POST;
        // Сохраняем данные в сессии для использования на форме
        $_SESSION['data'] = $data;

        // Проверяем наличие ключа в GET данных
        if (empty($data['key'])) {
            header('Location: /');
            exit;
        }

        $user = $this->userModel->get_user_by_key($data['key']);

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
        $this->userModel->set_key($login, NULL);

        // Обновляем пароль в базе данных
        $this->userModel->update_password($login, $hashedPassword);

        // Перенаправляем пользователя на страницу логина после успешного сброса пароля
        echo '<script>
        alert("Password successfully reset. You will be redirected to the login page.");
        window.location.href = "/login";
    </script>';
    }
}

//// Соединение с БД
//$conn = getDbConnection();
//
//// Создаем экземпляр контроллера и вызываем метод обработки запроса на сброс пароля
//$resetPasswordController = new ResetPasswordController($conn);
//$resetPasswordController->handleRequest();

?>
