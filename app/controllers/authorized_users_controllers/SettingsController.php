<?php

namespace controllers\authorized_users_controllers;
require_once 'app/services/helpers/session_check.php';

class SettingsController
{
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

        include __DIR__ . '/../../views/authorized_users/settings/settings_template.php';
    }

    public function saveSettings()
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
    public function actionSaveFontStyle()
    {
        if (isset($_POST['font_style'])) {
            $_SESSION['settings']['font_style'] = $_POST['font_style'];
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Font style not specified']);
        }
    }
}