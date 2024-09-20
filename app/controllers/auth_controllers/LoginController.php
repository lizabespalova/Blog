<?php
namespace controllers\auth_controllers;

use models\User;
use services\ErrorService;

require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ .'/../../../config/config.php';

class LoginController {

    private $userModel;
    private $errorService;

    public function __construct($dbConnection) {
        $this->userModel = new User($dbConnection);
        $this->errorService = new ErrorService();
    }

    public function login() {
        if (isset($_POST['submit'])) {
            // Получаем данные пользователя из БД по логину
            $data = $this->userModel->get_user_by_login($_POST['login']);

            // Если пользователь существует
            if ($data) {
                // Проверяем введенный пароль
                if (password_verify($_POST['password'], $data['user_password'])) {
                    // Генерируем случайный код для хеша авторизации
                    $hash = $this->generate_code(10);

                    // Если пользователь выбрал привязку к IP
                    $attach_ip = !empty($_POST['not_attach_ip']);

                    // Обновляем хеш авторизации и IP в базе данных
                    $this->userModel->update_user_hash($data['user_id'], $hash, $attach_ip);

                    // Устанавливаем куки
                    setcookie("id", $data['user_id'], time() + 60 * 60 * 24 * 30, "/");
                    setcookie("hash", md5($hash), time() + 60 * 60 * 24 * 30, "/", null, null, true); // httponly !!!

                    // Перенаправляем на страницу проверки
//                    $this->checkAuthentication();
                    header("Location: app/services/helpers/check.php");
                    exit();
                } else {
                    // Если пароль неверный
                    $this->errorService->show_error("You entered the wrong password.");

                }
            } else {
                // Если пользователь не найден
                $this->errorService->show_error("The user with this login was not found.");
            }
        }
    }
//    public function checkAuthentication()
//    {
//
//        session_start();
//        error_log("Cookie ID: " . (isset($_COOKIE['id']) ? $_COOKIE['id'] : 'Not set'));
//        error_log("Cookie Hash: " . (isset($_COOKIE['hash']) ? $_COOKIE['hash'] : 'Not set'));
//
//        if (isset($_COOKIE['id'], $_COOKIE['hash'])) {
//            $user_id = intval($_COOKIE['id']);
//            $userdata = $this->userModel->get_user_by_id($user_id);
//
//            if ($userdata && md5($userdata['user_hash']) === $_COOKIE['hash']) {
//                // Хеш совпадает, сохраняем сессию
//                $_SESSION['user'] = $userdata;
//
//                // Лог перед редиректом
//                error_log("Redirecting to profile: /profile/" . urlencode($userdata['user_login']));
//
//                // Редирект на страницу профиля
//                header('Location: /profile/' . urlencode($userdata['user_login']));
//                ob_end_flush(); // Очищаем буфер и отправляем данные
//                exit();
//            } else {
//                error_log("Hash mismatch for user ID: " . $user_id);
//                $this->show_error("Authorization error.");
//            }
//        } else {
//            error_log("Cookies not set");
//            $this->show_error("Enable cookies.");
//        }
//    }

    public function show_login_form(){
        include __DIR__ . '/../../views/auth/form_login.php';
    }

    private function generate_code($length = 6) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0, $clen)];
        }
        return $code;
    }


}

//// Соединение с БД
//$conn = getDbConnection();
//
//// Создаем экземпляр контроллера и вызываем метод логина
//$loginController = new LoginController($conn);
//$loginController->login();

?>
