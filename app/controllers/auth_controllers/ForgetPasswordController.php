<?php

namespace controllers\auth_controllers;

use models\User;
use PHPMailer;

require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ .'/../../../config/config.php';
require_once __DIR__ . '/../../views/auth/phpmailer/PHPMailerAutoload.php';



class ForgetPasswordController {

    private $userModel;
    private $mailer;

    public function __construct($dbConnection) {
        $this->userModel = new User($dbConnection);
        $this->mailer = new PHPMailer(true);
        $this->configureMailer();
    }
    public function show_forget_form(){
        include __DIR__ . '/../../views/auth/form_forget.php';
    }
    public function handleResetRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
            $email = $_POST['email'];
            $user = $this->userModel->getUserByEmail($email);

            if ($user) {
                $this->sendResetEmail($user, $email);
            } else {
                echo "Email is not registered!";
            }
        }
    }

    private function configureMailer() {
        // Настройки сервера
        $this->mailer->isSMTP();
        $this->mailer->Host = getHost();
        $this->mailer->Port = getPort(); // Используйте 465 для SSL
        $this->mailer->SMTPSecure = getSmptSecure(); // Используйте 'ssl' для порта 465
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = getEmail(); // Ваш адрес электронной почты
        $this->mailer->Password = getEmailPassword(); // Ваш пароль электронной почты
    }

    private function generateResetKey($login) {
        return md5($login . mt_rand(1000, 9999));
    }

    private function sendResetEmail($user, $email) {
        try {
            $login = $user['user_login'];
            $resetKey = $this->generateResetKey($login);
            $resetUrl = '/reset?key=' . $resetKey;

            // Настройка письма
            $this->mailer->setFrom(getEmail());
            $this->mailer->addAddress($email);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Change password';
            $this->mailer->Body = $login . ', a request to change your password has been completed.<br><br>Follow the link to change: ' . $resetUrl . '<br><br>If it was not you, it is recommended to change the password!';

            // Обновление ключа в базе данных
            $this->userModel->setKey($login, $resetKey);

            // Отправка письма
            $this->mailer->send();
            echo 'Email sent successfully!';
            header('Location: /');
            exit();
        } catch (Exception $e) {
            echo 'Error: ', $this->mailer->ErrorInfo;
        }
    }
}
