<?php
namespace controllers\auth_controllers;

use GuzzleHttp\Client;
use Exception;
use models\Settings;
use models\User;
use PHPMailer;
use services\AuthService;
use services\EmailService;
use services\ErrorService;

require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ .'/../../../config/config.php';
require_once __DIR__ . '/../../../vendor/autoload.php';

//require_once __DIR__ . '/../../../views/auth/phpmailer/PHPMailerAutoload.php';
//require_once 'app/services/helpers/session_check.php';

class RegisterController {

    private $userModel;
    private $settingModel;
//    private $mailer;
    private $authService;
    private $errorService;
    private $emailService;
    public function __construct($dbConnection) {
        $this->userModel = new User($dbConnection);
        $this->settingModel = new Settings($dbConnection);
        $this->authService = new AuthService($dbConnection);
        $this->errorService = new ErrorService();
        $this->emailService = new EmailService();
        $this->emailService->configure_mailer();
    }

    public function show_register_form() {
        include __DIR__ . '/../../views/auth/form_register.php';
    }

    public function register() {
        if (isset($_POST['register'])) {
            $login = $_POST['login'];
            $password = $_POST['password'];
            $email = $_POST['email'];

            $errors = $this->validate_registration($login, $password, $email);

            if (empty($errors)) {
                // Генерируем токен для подтверждения
                $token = $this->emailService->generateResetKey($login);

                // Создаем временного пользователя
                $this->userModel->create_temporary_user($login, $email, password_hash($password, PASSWORD_DEFAULT), $token);

                // Отправляем письмо с подтверждением
                $confirmationUrl = getConfirmationUrl() . $token;
                $body = 'Please click the link below to confirm your registration:<br><a href="' . $confirmationUrl . '">Confirm Registration</a>';
                $subject = 'Confirm Your Registration';
                $this->emailService->sendConfirmationEmail($email, $subject, $body);

                // Перенаправляем на страницу ожидания подтверждения
                header('Location: /confirmation_pending');
                exit();
            } else {
                $this->errorService->show_error($errors);
            }
        }
    }
    public function registration_pending(){
        include __DIR__ . '/../../views/auth/confirmation_pending.php';
    }
    private function validate_registration($login, $password, $email): array {
        $errors = [];

        // Проверяем логин
        if (!preg_match("/^[a-zA-Z0-9\-_]+$/", $login)) {
            $errors[] = "The login can consist only of Latin letters, numbers, hyphens, and underscores.";
        }

        if (strlen($login) < 3 || strlen($login) > 30) {
            $errors[] = "The login must be no less than 3 characters and no more than 30.";
        }

        // Проверяем, существует ли пользователь с таким логином
        if ($this->userModel->get_user_by_login($login)) {
            $errors[] = "User with this login already exists.";
        }

        // Проверяем email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }

        // Проверяем, существует ли пользователь с таким email
        if ($this->userModel->get_user_by_email($email)) {
            $errors[] = "User with this email already exists";
        }

        // Проверяем пароль
        if (strlen($password) < 8) {  // Обновленное ограничение на длину пароля
            $errors[] = "The password must be at least 8 characters long.";
        }

        return $errors;
    }

    public function setPassword() {
        session_start();

        // Убедитесь, что пользователь авторизован
        if (!isset($_SESSION['user_id'])) {
//        $this->show_errors("User wasn`t found");
          $this->errorService->show_error("User wasn`t found");
            exit();
        }
        $user = $this->userModel->get_temporary_user_by_id($_SESSION['user_id']);
        // Получаем данные из формы
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';

        // Проверяем, что пароли совпадают
        if ($password !== $passwordConfirmation) {
//            $this->show_errors("Passwords don`t match");
            $this->errorService->show_error("Passwords don`t match");
        }

        // Хэшируем пароль
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        // Перемещаем пользователя в основную таблицу без пароля
        if (isset($user)) {
            $link = '/profile/' . $user['user_login'];
            $userId = $this->userModel->move_to_main_table($user['user_login'], $user['user_email'], $hashedPassword, $link, $user['created_at']);

            // Удаляем временного пользователя
            $this->userModel->delete_temporary_user($user['id']);
            $this->settingModel->createDefaultSettings($userId);
            // Перенаправляем на профиль или другую страницу
            header('Location:'.$link);
            exit();
        }else{
//            $this->show_errors("User wasn`t found");
            $this->errorService->show_error("User wasn`t found");
        }
    }

    public function getClient($flag) {
        $client = new \Google_Client();
        $client->setClientId(getGoogleClientId());
        $client->setClientSecret(getGoogleClientSecret());

        if ($flag === "register") {
            $client->setRedirectUri(getRedirectUriRegister());
        } else {
            $client->setRedirectUri(getRedirectUriLogin());
        }

        $client->setDefer(true);
        $client->setHttpClient(new Client(['verify' => false]));
        $client->addScope(['email', 'profile']);
        $client->setPrompt('select_account');
        // Применение prompt=select_account
        $authUrl = $client->createAuthUrl() ;

        return $authUrl;
    }


    public function redirectToGoogle($flag) {
        // Логика для редиректа на Google
        $client = $this->getClient($flag);

//        $authUrl = $client->createAuthUrl();
        header('Location: ' . $client);
        exit();
    }

    public function googleAuthorization(){
        try {
            $authCode = $_GET['code'];

            // Получаем токен доступа
            $token = $this->getAccessToken($authCode, "login");

            if (!$token) {
                throw new Exception('Error fetching access token!');
            }

            // Получаем данные пользователя
            $userInfo = $this->getUserInfo($token['access_token']);
            if (!$userInfo || !isset($userInfo['email'])) {
                throw new Exception('Error fetching user info!');
            }

            // Обрабатываем авторизацию или регистрацию пользователя
            $email = $userInfo['email'];
            $existingUser = $this->userModel->get_user_by_email($email);

            if (!$existingUser) {
                // Если пользователя нет, говорим, что нет такого пользователя
                $this->errorService->show_error("This user doesn`t exist ");

//                $this->show_errors("This user doesn`t exist ");
            } else {
                // Если пользователь уже существует, выполняем авторизацию
                $this->loginUser($existingUser);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            header("Location: /error");
            exit();
        }
    }
    public function handleGoogleCallback() {
        try {
            if (isset($_GET['code']) && !empty($_GET['code'])) {
                $authCode = $_GET['code'];

                // Получаем токен доступа
                $token = $this->getAccessToken($authCode, "register");
                if (!$token) {
                    throw new Exception('Error fetching access token!');
                }

                // Получаем данные пользователя
                $userInfo = $this->getUserInfo($token['access_token']);
                if (!$userInfo || !isset($userInfo['email'])) {
                    throw new Exception('Error fetching user info!');
                }

                $this->handleUserLogin($userInfo);
            } else {
                throw new Exception('Authorization code is missing!');
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            header("Location: /error");
            exit();
        }
    }

    private function getAccessToken($authCode, $flag) {
        $clientId = getGoogleClientId();
        $clientSecret = getGoogleClientSecret();
        if ($flag === "register") {
            $redirectUri = getRedirectUriRegister();
        }else{
            $redirectUri = getRedirectUriLogin();
        }
        $postData = [
            'code' => $authCode,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code'
        ];

        // Инициализация cURL для получения токена
        $response = $this->curlRequest("https://oauth2.googleapis.com/token", $postData);
        if ($response) {
            return json_decode($response, true);
        }
        return null;
    }

    private function getUserInfo($accessToken) {
        $url = "https://www.googleapis.com/oauth2/v3/userinfo?access_token=$accessToken";

        // Инициализация cURL для запроса информации о пользователе
        $response = $this->curlRequest($url);
        if ($response) {
            return json_decode($response, true);
        }
        return null;
    }
    //Переделать в реальной среде!!!
    private function curlRequest($url, $postData = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Отключить проверку SSL (если необходимо)

        if ($postData) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        }

        // Получаем ответ
        $response = curl_exec($ch);

        // Проверка на ошибки
        if (curl_errno($ch)) {
            error_log('cURL Error: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }

        curl_close($ch);
        return $response;
    }

    // Этот метод можно использовать для регистрации с Google
    private function handleUserLogin($userInfo) {
        $email = $userInfo['email'];
        $existingUser = $this->userModel->get_user_by_email($email);

        if (!$existingUser) {
            // Если пользователя нет, выполняем регистрацию
            $this->registerUserFromGoogle($userInfo);
        } else {
            // Если пользователь уже существует, выполняем авторизацию
            $this->loginUser($existingUser);
        }
    }

    // Регистрация нового пользователя через Google
    private function registerUserFromGoogle($userInfo) {
        $email = $userInfo['email'];
        $login = explode('@', $email)[0]; // Логин до '@'

        // Фильтрация логина
        $login = preg_replace('/[^a-zA-Zа-яА-Я0-9_-]/u', '', str_replace(' ', '_', $login));
        if (empty($login)) {
            $login = explode('@', $email)[0];
        }

        // Генерация временного токена
        $token = $this->emailService->generateResetKey($login);

        // Создаем временного пользователя
        $this->userModel->create_temporary_user($login, $email, "", $token); // Пароль будет null, потому что его нет в случае регистрации через Google

        // Отправляем письмо с подтверждением
        $confirmationUrl = getConfirmationUrl() . $token;
        $body = 'Please click the link below to confirm your registration:<br><a href="' . $confirmationUrl . '">Confirm Registration</a>';
        $subject = 'Confirm Your Registration';
        $this->emailService->sendConfirmationEmail($email, $subject, $body);

        // Перенаправляем на страницу ожидания подтверждения
        header('Location: /confirmation_pending');
        exit();
    }

    // Авторизация существующего пользователя
    private function loginUser($existingUser) {

//        $_SESSION['user_id'] = $existingUser['user_id'];
//        $_SESSION['user_email'] = $existingUser['user_email'];
//        $_SESSION['user_login'] = $existingUser['user_login'];
//        $this->show_errors($existingUser['user_id']);
        $hash = $this->authService->generate_code(10);
        // Обновляем хеш авторизации и IP в базе данных
        $this->userModel->update_user_hash($existingUser['user_id'], $hash);

        setcookie('id', $existingUser['user_id'], time() + 3600, "/", "", true, true);  // cookie действует 1 час
        setcookie("hash", md5($hash), time() + 3600, "/", null, null, true); // httponly !!!

        header("Location: app/services/helpers/check.php");

//        header('Location: /profile/' . $existingUser['user_login']);
        exit();
    }

}
?>
