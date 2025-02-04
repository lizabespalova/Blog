<?php

namespace controllers\authorized_users_controllers;

use models\Session;
use models\Settings;
use models\User;
use services\EmailService;

require_once 'app/services/helpers/session_check.php';

class SettingsController
{
    private $userModel;
    private $settingModel;
    private $sessionModel;

    private $emailService;


    public function __construct($conn) {
        $this->userModel = new User($conn);
        $this->settingModel = new Settings($conn);
        $this->sessionModel = new Session($conn);
        $this->emailService = new EmailService();
        $this->emailService->configure_mailer();
    }
    public function showSettingsTemplate(){
        require_once 'app/services/helpers/switch_language.php';
        $page = $_GET['section'] ?? 'general';
        $sections = [
            'appearance' => $translations['appearance_settings'], // Настройки внешнего вида (день/ночь)
            'personal' => $translations['personal_data'],         // Настройки личных данных (email, пароль, логин)
            'privacy' => $translations['privacy_settings'], // Настройки конфиденциальности
            'security' => $translations['security'],              // Безопасность (2FA, активные сессии)
            'preferences' => $translations['preferences'], // Общие предпочтения (язык, формат даты/времени)
        ];
        if (!array_key_exists($page, $sections)) {
            $page = 'appearance';
        }
        $userId = $_SESSION['user']['user_id'];
        $currentLogin = $_SESSION['user']['user_login'];
        $currentEmail = $this->userModel->getUserEmail($userId);
        $profileVisibility = $this->settingModel->getProfileVisibility($userId);
        $showLastSeen = $this->settingModel->getShowLastSeen($userId);
        $language = $this->settingModel->getLanguage($userId);
        $flags = [
            'en' => 'gb',
            'ru' => 'ru',
            'de' => 'de',
            'ua' => 'ua'
        ];
        $selectedFlag = $flags[$language] ?? 'gb';


        $sessions = $this->sessionModel->getSessionsByUserId($userId);

        // Определяем текущий сеанс
        $currentSessionId = session_id();
        $currentSession = null;
        $otherSessions = [];

        // Проходим по сеансам и распределяем
        foreach ($sessions as $session) {
            if ($session['session_id'] === $currentSessionId) {
                $currentSession = $session;
            } else {
                $otherSessions[] = $session;
            }
        }
        include __DIR__ . '/../../views/authorized_users/settings/settings_template.php';
    }

    public function saveTheme()
    {
        $userId = $_SESSION['user']['user_id'];
        $theme = $_POST['theme'] ?? 'light';
        $_SESSION['settings']['theme'] = $theme;
        if ($this->settingModel->setTheme($userId, $theme)) {
            echo json_encode(['success' => true, 'message' => 'Theme saved.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save theme.']);
        }
    }

    public function saveFontSize()
    {
        $userId = $_SESSION['user']['user_id'];
        $fontSize = $_POST['font_size'] ?? null;
        $_SESSION['settings']['font_size'] = $fontSize;
        if ($fontSize && $this->settingModel->setFontSize($userId, $fontSize)) {
            echo json_encode(['success' => true, 'message' => 'Font size saved.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid font size.']);
        }
    }
    public function saveLanguage(){
        $language = in_array($_POST['language'], ['en', 'ru', 'de', 'ua']) ? $_POST['language'] : 'en';
        $_SESSION['settings']['language'] = $language;

        // Обновляем в БД, если пользователь авторизован
        $this->settingModel->setLanguage($language, $_SESSION['user']['user_id']);
        echo json_encode(['success' => true, 'message' => 'Language successfully changed']);
    }
    public function saveFontStyle()
    {
        $userId = $_SESSION['user']['user_id'];
        $fontStyle = $_POST['font_style'] ?? 'sans-serif';
        $_SESSION['settings']['font_style'] = $fontStyle;
        if ($this->settingModel->setFontStyle($userId, $fontStyle)) {
            echo json_encode(['success' => true, 'message' => 'Font style saved.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save font style.']);
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

    public function updatePrivacySettings(){
        $userId = $_SESSION['user']['user_id'];  // Получаем user_id из сессии
        $profileVisibility = $_POST['profile_visibility'];
        $showLastSeen = $_POST['show_last_seen'];
        // Обновление данных в базе
        $success = $this->settingModel->setProfileVisibility($userId, $profileVisibility);
        $success &= $this->settingModel->setShowLastSeen($userId, $showLastSeen);
        $_SESSION['settings']['profile_visibility'] = $profileVisibility;
        $_SESSION['settings']['show_last_seen'] = $showLastSeen;

        // Ответ на запрос
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Settings saved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save settings']);
        }
    }
    public function updatePassword() {
        $user = $_SESSION['user'];
        $login = $user['user_login'];

        header('Content-Type: application/json'); // JSON-ответ

        if (empty($_POST['current_password']) || empty($_POST['new_password']) || empty($_POST['confirm_password'])) {
            echo json_encode([
                'success' => false,
                'message' => 'All fields are required.',
                'user_login' => $login
            ]);
            exit();
        }

        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword !== $confirmPassword) {
            echo json_encode([
                'success' => false,
                'message' => 'New passwords do not match.',
                'user_login' => $login
            ]);
            exit();
        }

        $userData = $this->userModel->get_user_by_login($login);

        if (!password_verify($currentPassword, $userData['user_password'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Current password is incorrect.',
                'user_login' => $login
            ]);
            exit();
        }

        // Обновляем пароль
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->userModel->update_password($login, $hashedPassword);

        // Отправляем JSON-ответ, а не делаем редирект
        echo json_encode([
            'success' => true,
            'message' => 'Password successfully changed.',
            'user_login' => $login
        ]);
        exit();
    }

    public function deleteSession(){
        // Получаем данные из запроса
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['session_id'])) {
            $sessionId = $data['session_id'];
            // Удаляем сессию
            $result = $this->sessionModel->deleteSession($sessionId);

            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No session ID provided']);
        }
    }


}