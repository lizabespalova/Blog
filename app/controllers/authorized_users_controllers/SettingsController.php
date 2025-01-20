<?php

namespace controllers\authorized_users_controllers;
use models\User;

require_once 'app/services/helpers/session_check.php';

class SettingsController
{
    private $userModel;


    public function __construct($conn) {
        $this->userModel = new User($conn);
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

        // Получение данных из POST-запроса
        $login = trim($_POST['login']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        $userId = $_SESSION['user']['user_id'];

        // Проверка пароля
        $user = $this->userModel->getPasswordByUserId($userId);

        if (!$user || !password_verify($password, $user['user_password'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid password']);
            return;
        }

        // Если email не пустой, то проверяем его
        if (!empty($email)) {
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
        }

        // Обновляем только логин и, если нужно, email
        $result = $this->userModel->updateUser($userId, $login, $email);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
        }
    }
}