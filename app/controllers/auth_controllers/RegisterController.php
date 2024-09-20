<?php
namespace controllers\auth_controllers;

use models\User;
use Exception;
use PHPMailer;

require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ .'/../../../config/config.php';
require_once __DIR__ . '/../../views/auth/phpmailer/PHPMailerAutoload.php';

class RegisterController {

    private $userModel;
    private $mailer;

    public function __construct($dbConnection) {
        $this->userModel = new \models\User($dbConnection);
        $this->mailer = new PHPMailer(true);
        $this->configure_mailer();
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
                $token = md5($login . mt_rand(1000, 9999));

                // Создаем временного пользователя
                $this->userModel->create_temporary_user($login, $email, password_hash($password, PASSWORD_DEFAULT), $token);

                // Отправляем письмо с подтверждением
                $this->send_confirmation_email($email, $token);

                // Перенаправляем на страницу ожидания подтверждения
                header('Location: /confirmation_pending');
                exit();
            } else {
                $this->show_errors($errors);
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
            $errors[] = "A user with this login already exists in the database.";
        }

        // Проверяем email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }

        // Проверяем, существует ли пользователь с таким email
        if ($this->userModel->get_user_by_email($email)) {
            $errors[] = "A user with this email already exists in the database.";
        }

        // Проверяем пароль
        if (strlen($password) < 8) {  // Обновленное ограничение на длину пароля
            $errors[] = "The password must be at least 8 characters long.";
        }

        return $errors;
    }

    private function configure_mailer() {
        $this->mailer->isSMTP();
        $this->mailer->Host = getHost();
        $this->mailer->Port = getPort();
        $this->mailer->SMTPSecure = getSmptSecure();
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = getEmail();
        $this->mailer->Password = getEmailPassword();
    }

    private function send_confirmation_email($email, $token) {
        $confirmationUrl = 'http://localhost:8000/confirm?user_key=' . $token;

        $this->mailer->setFrom(getEmail());
        $this->mailer->addAddress($email);
        $this->mailer->isHTML(true);
        $this->mailer->Subject = 'Confirm Your Registration';
        $this->mailer->Body = 'Please click the link below to confirm your registration:<br><a href="' . $confirmationUrl . '">Confirm Registration</a>';

        try {
            $this->mailer->send();
            echo '<script type="text/javascript">
            alert("Confirmation email sent successfully!");
            window.location.href = "/confirmation_pending";
        </script>';
        } catch (Exception $e) {
            echo 'Error: ', $this->mailer->ErrorInfo;
        }
    }


    public function confirmRegistration() {
        $token = $_GET['user_key'] ?? '';

        if ($token) {
            $user = $this->userModel->get_temporary_user_by_token($token);

            if ($user) {
                // Перемещаем пользователя из временной таблицы в основную таблицу с генерацией ссылки на таблицу
                $link = '/profile/' . $user['user_login'];
                $this->userModel->move_to_main_table($user['user_login'], $user['user_email'], $user['user_password'], $link);

                // Удаляем временного пользователя
                $this->userModel->delete_temporary_user($token);

                header('Location: /app/views/authorized_users/thank_to_authorized_user.php');
                exit();
            } else {
                echo 'Invalid or expired token.';
            }
        } else {
            echo 'No token provided.';
        }
    }
    private function isTokenExpired($createdAt): bool {
        $expiration = new \DateTime($createdAt);
        $now = new \DateTime();
        $interval = $now->diff($expiration);

        return $interval->i >= 30; // Проверка на истечение срока в минутах
    }


    private function show_errors($errors) {
        $errorMessages = implode("\\n", $errors);
//        echo "<script>
//            alert('The following errors occurred during registration:\\n{$errorMessages}');
//            window.location.href = '/register';
//          </script>";
        $encodedMessage = urlencode($errorMessages); // Кодируем сообщение для URL
        header("Location: /../../services/error_message.php");
        exit(); // Завершаем выполнение текущего скрипта
    }
}
?>
