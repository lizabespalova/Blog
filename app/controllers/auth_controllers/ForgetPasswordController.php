<?php

require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ .'/../../../config/config.php';
require_once __DIR__ . '/../../views/auth/phpmailer/PHPMailerAutoload.php';

class ForgetPasswordController {

    private $userModel;
    private $mailer;

    public function __construct($dbConnection) {
        $this->userModel = new \models\User($dbConnection);
        $this->mailer = new PHPMailer(true);
    }

    public function handleResetRequest() {
        if (isset($_POST['reset'])) {
            $email = $_POST['email'];
            $user = $this->userModel->getUserByEmail($email);

            if ($user) {
                $this->sendResetEmail($user, $email);
            } else {
                echo "Email is not registered!";
            }
        }
    }

    private function sendResetEmail($user, $email) {
        try {
            // Настройки сервера
            $this->mailer->isSMTP();
            $this->mailer->Host = getHost();
            $this->mailer->Port = getPort(); // Используйте 465 для SSL
            $this->mailer->SMTPSecure = getSmptSecure(); // Используйте 'ssl' для порта 465
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = getEmail(); // Ваш адрес электронной почты Gmail
            $this->mailer->Password = getEmailPassword(); // Ваш пароль Gmail

            // Отправитель и получатель
            $this->mailer->setFrom(getEmail());
            $this->mailer->addAddress($email);

            $login = $user['user_login'];
            $key = md5($login . mt_rand(1000, 9999));
            $url = '<a href="http://localhost:8000/app/views/auth/form_reset_password.php?key=' . $key . '">Change password</a>';

            // Содержимое письма
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Change password';
            $this->mailer->Body = $login . ', a request to change your password has been completed.<br><br>Follow the link to change: ' . $url . '<br><br>If it was not you, it is recommended to change the password!';

            // Обновление ключа в базе данных
            $this->userModel->setKey($login, $key);

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

// Соединение с БД
$conn = getDbConnection();

// Создаем экземпляр контроллера и вызываем метод обработки запроса на сброс пароля
$resetPasswordController = new ForgetPasswordController($conn);
$resetPasswordController->handleResetRequest();

?>
