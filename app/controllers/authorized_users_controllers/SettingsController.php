<?php

namespace controllers\authorized_users_controllers;
require_once 'app/services/helpers/session_check.php';

use models\User;
use services\EmailService;

require_once 'app/services/helpers/session_check.php';

class SettingsController
{
    private $userModel;

    private $emailService;


    public function __construct($conn) {
        $this->userModel = new User($conn);
        $this->emailService = new EmailService();
        $this->emailService->configure_mailer();
    }
    public function showSettingsTemplate(){
        $page = $_GET['section'] ?? 'general';

        $sections = [
            'appearance' => 'Appearance Settings', // Настройки внешнего вида (день/ночь)
            'personal' => 'Personal Data',         // Настройки личных данных (email, пароль, логин)
            'privacy' => 'Privacy Settings',       // Настройки конфиденциальности
            'notifications' => 'Notifications',    // Уведомления
            'security' => 'Security',              // Безопасность (2FA, активные сессии)
            'integrations' => 'Integrations',      // Интеграции (подключение соцсетей, API ключи)
            'preferences' => 'Preferences',        // Общие предпочтения (язык, формат даты/времени)
        ];
        if (!array_key_exists($page, $sections)) {
            $page = 'appearance';
        }
//        if (empty($_SESSION['settings']['font_style'])) {
//            $_SESSION['settings']['font_style'] = 'serif';
//        }

        $currentLogin = $_SESSION['user']['user_login'];
        $currentEmail = $this->userModel->getUserEmail($_SESSION['user']['user_id']);
        include __DIR__ . '/../../views/authorized_users/settings/settings_template.php';
    }

    public function saveTheme()
    {
        // Получение данных из запроса
        $theme = $_POST['theme'] ?? 'light';
        $fontSize = $_POST['font_size'] ?? 16;
        $fontStyle = $_POST['font_style'] ?? 'sans-serif';

        // Сохранение в сессию
        $_SESSION['settings'] = [
            'theme' => $theme,
            'font_size' => $fontSize,
            'font_style' => $fontStyle,
        ];

        // Возврат успешного ответа
        echo json_encode(['success' => true]);
    }
    public function saveFontSize()
    {
        $fontSize = $_POST['font_size'] ?? null;

        if ($fontSize && is_numeric($fontSize)) {
            // Сохраняем размер шрифта в сессии
            $_SESSION['settings']['font_size'] = $fontSize;
            echo json_encode(['success' => true, 'message' => 'Font size updated.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid font size.']);
        }
        exit();
    }
    // Сохранение выбранного стиля шрифта
    public function saveFontStyle()
    {
        if (isset($_POST['font_style'])) {
            $_SESSION['settings']['font_style'] = $_POST['font_style'];
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Font style not specified']);
        }
    }

    public function updateUser() {
        // Проверка аутентификации
        if (!isset($_SESSION['user']['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        // Получаем данные из тела запроса
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
            return;
        }

        // Получаем текущие данные из сессии
        $userId = $_SESSION['user']['user_id'];
        $currentLogin = $_SESSION['user']['user_login'];
        $currentEmail = $this->userModel->getUserEmail($userId);

        // Данные из запроса
        $login = trim($data['login'] ?? $currentLogin); // Если логин пустой, берем из сессии
        // Проверка на уникальность логина
        if ($login !== $currentLogin && $this->userModel->get_user_by_login($login)) {
            // Если логин уже существует, возвращаем ошибку
            echo json_encode([
                'success' => false,
                'message' => 'The provided login is already in use. Please choose a different one.'
            ]);
            exit; // Прекращаем выполнение
        }
        $email = trim($data['email'] ?? $currentEmail); // Если email пустой, берем из сессии
        $password = $data['password'] ?? '';

        // Проверка пароля
        $user = $this->userModel->get_user_by_id($userId);
        if (!$user || !password_verify($password, $user['user_password'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid password']);
            return;
        }

        // Проверка на изменение email
        $isEmailChanged = $email !== $currentEmail;

        // Если email изменен, выполняем проверки
        if ($isEmailChanged) {
            // Проверка на уникальность email
            if ($this->userModel->isEmailExist($email, $userId)) {
                echo json_encode(['success' => false, 'message' => 'Email is already in use']);
                return;
            }

            // Проверка на корректность формата email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Invalid email format']);
                return;
            }

            // Генерация токена для подтверждения email
            $token = $this->emailService->generateResetKey($login);

            // Сохранение изменений во временную таблицу
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $this->userModel->create_temporary_user($login, $email, $hashedPassword, $token, $userId);

            // Отправка подтверждающего письма
            $confirmationUrl = getUpdateEmailUrl() . $token;
            $subject = 'Confirm Your Email Change';
            $body = 'You requested to change your email. Please confirm by clicking the link below:<br>
                 <a href="' . $confirmationUrl . '">Confirm Email Change</a>';
            $emailSentResponse = $this->emailService->sendUpdateEmail($email, $subject, $body);

            // Преобразование строки JSON в массив
            $response = json_decode($emailSentResponse, true);

            if ($response['success']) {
                // Если отправка успешна
                echo json_encode(['success' => true, 'message' => 'A confirmation email has been sent to your new address']);
            } else {
                // Если произошла ошибка, передаем детальную информацию
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to send confirmation email',
                    'error' => $response['error'] ?? 'Unknown error'
                ]);
            }

            return;
        }

        // Если только логин изменен, обновляем логин
        if ($login !== $currentLogin) {
            $link = '/profile/' . $login;
            $this->userModel->updateUser($userId, $login, $link, $currentEmail);
        }
        $_SESSION['user']['user_login'] = $login;
        echo json_encode(['success' => true, 'message' => 'User data updated successfully']);
    }


}