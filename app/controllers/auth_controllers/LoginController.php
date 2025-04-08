<?php
namespace controllers\auth_controllers;

use models\User;
use services\AuthService;
use services\ErrorService;

require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ .'/../../../config/config.php';

class LoginController {

    private $userModel;
    private $errorService;
    private $authService;
    public function __construct($dbConnection) {
        $this->userModel = new User($dbConnection);
        $this->errorService = new ErrorService();
        $this->authService = new AuthService($dbConnection);
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
                    $hash = $this->authService->generate_code(10);

                    // Если пользователь выбрал привязку к IP
                    $attach_ip = !empty($_POST['not_attach_ip']);

                    // Обновляем хеш авторизации и IP в базе данных
                    $this->userModel->update_user_hash($data['user_id'], $hash, $attach_ip);

                    // Устанавливаем куки
//                    setcookie("id", $data['user_id'], time() + 60 * 60 * 24 * 30, "/");
//                    setcookie("hash", md5($hash), time() + 60 * 60 * 24 * 30, "/", null, null, true); // httponly !!!
                    setcookie('id', $data['user_id'], [
                        'expires' => time() + 3600,
                        'path' => '/',
                        'domain' => 'league-of-code.up.railway.app', // Используй именно этот домен
                        'secure' => true,  // Для HTTPS должно быть true
                        'samesite' => 'Lax',
                    ]);

                    setcookie('hash', md5($hash), [
                        'expires' => time() + 3600,
                        'path' => '/',
                        'domain' => 'league-of-code.up.railway.app', // Используй именно этот домен
                        'secure' => true,  // Для HTTPS должно быть true
                        'samesite' => 'Lax',
                    ]);

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

    public function show_login_form(){
        include __DIR__ . '/../../views/auth/form_login.php';
    }

}


?>
