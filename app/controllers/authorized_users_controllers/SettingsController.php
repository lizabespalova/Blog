<?php

namespace controllers\authorized_users_controllers;

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
            $page = 'general';
        }

        include __DIR__ . '/../../views/authorized_users/settings/settings_template.php';
    }
}