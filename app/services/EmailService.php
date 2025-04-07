<?php

namespace services;
require_once __DIR__ . '/../../vendor/autoload.php';

//require_once __DIR__ . '/../views/auth/phpmailer/PHPMailerAutoload.php';
//use PHPMailer;
use SendGrid;
use SendGrid\Mail\Mail;

use Exception;
use models\Settings;
use models\User;
use PHPMailer\PHPMailer\PHPMailer;

class EmailService
{
    private $mailer;
    private $userModel;
    private $settingModel;

    public function __construct()
    {
//        $this->mailer = new PHPMailer(true);
        $this->userModel = new User(getDbConnection());
        $this->settingModel = new Settings(getDbConnection());
    }

//    public function configure_mailer() {
//        $this->mailer->isSMTP();
//        $this->mailer->Host = getHost();
//        $this->mailer->Port = getPort();
//        $this->mailer->SMTPSecure = getSmptSecure();
//        $this->mailer->SMTPAuth = true;
//        $this->mailer->Username = getEmail();
//        $this->mailer->Password = getEmailPassword();
//    }

    public function sendConfirmationEmail($email, $subject, $body) {
        $emailToSend = new Mail();
        $emailToSend->setFrom(getEmail(), "League of code");
        $emailToSend->setSubject($subject);
        $emailToSend->addTo($email);
        $emailToSend->addContent("text/html", $body);

        $sendgrid = new SendGrid(getSengridApiKey());
        try {
            $response = $sendgrid->send($emailToSend);
            if ($response->statusCode() >= 200 && $response->statusCode() < 300) {
                echo '<script type="text/javascript">
                        alert("Confirmation email sent successfully!");
                        window.location.href = "/confirmation_pending";
                    </script>';
            } else {
                echo 'Email not sent. Status: ' . $response->statusCode();
                echo '<pre>' . print_r($response->body(), true) . '</pre>';
            }
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage();
        }
//        $this->mailer->setFrom(getEmail());
//        $this->mailer->addAddress($email);
//        $this->mailer->isHTML(true);
//        $this->mailer->Subject = $subject;
//        $this->mailer->Body = $body;
//
//        try {
//            $this->mailer->send();
//            echo '<script type="text/javascript">
//            alert("Confirmation email sent successfully!");
//            window.location.href = "/confirmation_pending";
//        </script>';
//        } catch (Exception $e) {
//            echo 'Error: ', $this->mailer->ErrorInfo;
//        }
    }
//    public function sendUpdateEmail($email, $subject, $body) {
//        $this->mailer->setFrom(getEmail());
//        $this->mailer->addAddress($email);
//        $this->mailer->isHTML(true);
//        $this->mailer->Subject = $subject;
//        $this->mailer->Body = $body;
//
//        try {
//            $this->mailer->send();
//            return json_encode(['success' => true, 'message' => 'Email sent successfully']);
//        } catch (Exception $e) {
//            // Логируем ошибку для дальнейшего анализа
//            error_log("Email sending error: " . $this->mailer->ErrorInfo);
//
//            // Возвращаем JSON с ошибкой
//            return json_encode([
//                'success' => false,
//                'message' => 'Failed to send email',
//                'error' => $this->mailer->ErrorInfo
//            ]);
//        }
//    }
    public function sendUpdateEmail($email, $subject, $body)
    {
        $emailToSend = new Mail();
        $emailToSend->setFrom(getEmail(), "My App");
        $emailToSend->setSubject($subject);
        $emailToSend->addTo($email);
        $emailToSend->addContent("text/html", $body);

        $sendgrid = new SendGrid(getenv('SENDGRID_API_KEY'));

        try {
            $response = $sendgrid->send($emailToSend);
            if ($response->statusCode() >= 200 && $response->statusCode() < 300) {
                return json_encode(['success' => true, 'message' => 'Email sent successfully']);
            } else {
                return json_encode([
                    'success' => false,
                    'message' => 'Failed to send email',
                    'error' => $response->body()
                ]);
            }
        } catch (Exception $e) {
            error_log("Email sending error: " . $e->getMessage());
            return json_encode([
                'success' => false,
                'message' => 'Failed to send email',
                'error' => $e->getMessage()
            ]);
        }
    }
//    public function sendResetEmail($user, $email) {
//        try {
//            $login = $user['user_login'];
//            $resetKey = $this->generateResetKey($login);
//            $resetUrl = 'http://localhost:8080/app/views/auth/form_reset_password.php?key=' . $resetKey;
//
//            // Настройка письма
//            $this->mailer->setFrom(getEmail());
//            $this->mailer->addAddress($email);
//
//            $this->mailer->isHTML(true);
//            $this->mailer->Subject = 'Change password';
//            $this->mailer->Body = $login . ', a request to change your password has been completed.<br><br>Follow the link to change: ' . $resetUrl . '<br><br>If it was not you, it is recommended to change the password!';
//
//            // Обновление ключа в базе данных
//            $this->userModel->set_key($login, $resetKey);
//
//            // Отправка письма
//            $this->mailer->send();
//            echo 'Email sent successfully!';
//            header('Location: /');
//            exit();
//        } catch (Exception $e) {
//            echo 'Error: ', $this->mailer->ErrorInfo;
//        }
//    }
    // Метод для сброса пароля
    public function sendResetEmail($user, $email)
    {
        try {
            $login = $user['user_login'];
            $resetKey = $this->generateResetKey($login);
            $resetUrl = 'http://localhost:8080/app/views/auth/form_reset_password.php?key=' . $resetKey;

            // Создаем письмо
            $emailToSend = new Mail();
            $emailToSend->setFrom(getEmail(), "My App");
            $emailToSend->addTo($email);
            $emailToSend->setSubject('Change password');
            $emailToSend->addContent("text/html", $login . ', a request to change your password has been completed.<br><br>Follow the link to change: ' . $resetUrl . '<br><br>If it was not you, it is recommended to change the password!');

            // Отправляем письмо через SendGrid API
            $sendgrid = new SendGrid(getenv('SENDGRID_API_KEY'));
            $response = $sendgrid->send($emailToSend);

            if ($response->statusCode() >= 200 && $response->statusCode() < 300) {
                // Обновляем ключ в базе данных
                $this->userModel->set_key($login, $resetKey);
                echo 'Email sent successfully!';
                header('Location: /');
                exit();
            } else {
                echo 'Error sending reset email. Status: ' . $response->statusCode();
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage();
        }
    }
    public function generateResetKey($login) {
        return md5($login . mt_rand(1000, 9999));
    }
    // Метод для подтверждения регистрации
    public function confirmRegistration()
    {
        session_start();
        $token = $_GET['user_key'] ?? '';

        if ($token) {
            $user = $this->userModel->get_temporary_user_by_token($token);

            if ($user) {
                if (empty($user['user_password'])) {
                    // Перенаправляем на страницу установки пароля
                    $_SESSION['user_id'] = $user['id'];
                    header('Location: /app/views/auth/set_password.php');
                    exit();
                } else {
                    // Переносим пользователя в основную таблицу и благодарим
                    $link = '/profile/' . $user['user_login'];
                    $userId = $this->userModel->move_to_main_table($user['user_login'], $user['user_email'], $user['user_password'], $link, $user['created_at']);
                    $this->userModel->delete_temporary_user($user['user_id']);
                    $this->settingModel->createDefaultSettings($userId);
                    header('Location: /app/views/auth/thank_to_authorized_user.php');
                    exit();
                }
            } else {
                echo 'Invalid or expired token.';
            }
        } else {
            echo 'No token provided.';
        }
    }
//    public function confirmRegistration() {
//        session_start();
//        $token = $_GET['user_key'] ?? '';
//
//        if ($token) {
//            $user = $this->userModel->get_temporary_user_by_token($token);
//
//            if ($user) {
//                // Проверяем, есть ли пароль
//                if (empty($user['user_password'])) {
//                    // Сохраняем пользователя в сессии
//                    $_SESSION['user_id'] = $user['id'];
//                    // Перенаправляем на страницу установки пароля
//                    header('Location: /app/views/auth/set_password.php');
//                    exit();
//                } else {
//                    // Если пароль есть, перемещаем в основную таблицу и благодарим
//                    $link = '/profile/' . $user['user_login'];
//                    $userId = $this->userModel->move_to_main_table($user['user_login'], $user['user_email'], $user['user_password'], $link, $user['created_at']);
//
//                    // Удаляем временного пользователя
//                    $this->userModel->delete_temporary_user($user['user_id']);
//                    $this->settingModel->createDefaultSettings($userId);
//                    // Перенаправляем на страницу благодарности
//                    header('Location: /app/views/auth/thank_to_authorized_user.php');
//                    exit();
//                }
//            } else {
//                echo 'Invalid or expired token.';
//            }
//        } else {
//            echo 'No token provided.';
//        }
//    }
    // Метод для подтверждения обновления email
    public function confirmEmailUpdate()
    {
        session_start();
        $token = $_GET['user_key'] ?? null;

        if (!$token) {
            header("Location: /error?message=Invalid+token");
            exit;
        }

        $user = $this->userModel->get_temporary_user_by_token($token);

        if (!$user) {
            header("Location: /error?message=Invalid+or+expired+token");
            exit;
        }

        $newEmail = $user['user_email'] ?? null;

        if (!$newEmail) {
            header("Location: /error?message=New+email+not+found");
            exit;
        }

        $this->userModel->updateUserFromTemporary($user['user_id']);
        if (!empty($user['user_login'])) {
            $link = '/profile/' . $user['user_login'];
            $_SESSION['user']['user_login'] = $user['user_login'];
            $this->userModel->updateUserLink($user['user_id'], $link);
        }
        $this->userModel->delete_temporary_user($user['id']);

        header("Location: /success?message=Email+updated+successfully&user_login=" . urlencode($user['user_login']));
        exit;
    }
//    public function confirmEmailUpdate() {
//        session_start();
//        // Получаем токен из GET-параметров
//        $token = $_GET['user_key'] ?? null;
//
//        if (!$token) {
//            header("Location: /error?message=Invalid+token");
//            exit;
//        }
//
//        // Проверяем токен в базе
//        $user = $this->userModel->get_temporary_user_by_token($token);
//
//        if (!$user) {
//            header("Location: /error?message=Invalid+or+expired+token");
//            exit;
//        }
//
//        // Получаем новый email из данных
//        $newEmail = $user['user_email'] ?? null;
//
//        if (!$newEmail) {
//            header("Location: /error?message=New+email+not+found");
//            exit;
//        }
//
//        // Обновляем email пользователя
//        $this->userModel->updateUserFromTemporary($user['user_id']);
//        if (!empty($user['user_login'])) {
//            $link = '/profile/' . $user['user_login'];
//            $_SESSION['user']['user_login'] = $user['user_login'];
//            $this->userModel->updateUserLink($user['user_id'], $link);
//        }
//        $this->userModel->delete_temporary_user($user['id']);
//
//        // Перенаправляем на страницу успешного подтверждения
//        header("Location: /success?message=Email+updated+successfully&user_login=" . urlencode($user['user_login']));
//        exit;
//    }


}