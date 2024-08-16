<?php
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ .'/../../../config/config.php';
require_once __DIR__ . '/phpmailer/PHPMailerAutoload.php';
$mail = new PHPMailer(true);

$conn = getDbConnection();
$customerModel = new \models\User($conn);

$data = $_POST;
if (isset($_POST['reset'])) {
    $email = $_POST['email'];
    $user = $customerModel->getUserByEmail($email);

    if ($user) {
        try {
            // Настройки сервера
            $mail->isSMTP();
            $mail->Host = getHost();
            $mail->Port = getPort(); // Используйте 465 для SSL
            $mail->SMTPSecure = getSmptSecure(); // Используйте 'ssl' для порта 465
            $mail->SMTPAuth = true;
            $mail->Username = getEmail(); // Ваш адрес электронной почты Gmail
            $mail->Password = getEmailPassword(); // Ваш пароль Gmail

            // Отправитель и получатель
            $mail->setFrom(getEmail());
            $mail->addAddress($email);
            $login = $user['user_login'];
            $key = md5($login . mt_rand(1000, 9999));
            $url = '<a href= "http://localhost:8000/app/views/auth/form_reset_password.php?key=' . $key. '">Change password</a>';
            // Содержимое письма
            $mail->isHTML(true);
            $mail->Subject = 'Change password';
            $mail->Body = $login . ', a request to change your password has been completed.<br><br> Follow the link to change: ' . $url . '<br><br> If it was not you, it is recommended to change the password!';

            $customerModel->setKey($login, $key);


            // Отправка письма
            $mail->send();
            echo 'Email sent successfully!';
            header('Location: /');
        } catch (Exception $e) {
            echo 'Error: ', $mail->ErrorInfo;
        }


    }
    else{
        echo "email is not registered!";
    }
}