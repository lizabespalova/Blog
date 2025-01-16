<?php

namespace controllers\authorized_users_controllers;

class SettingsController
{
    public function showSettingsTemplate(){
        $page = $_GET['section'] ?? 'general';

        $sections = [
            'general' => 'General Settings',
            'profile' => 'Profile Settings',
            'articles' => 'Article Settings',
            'notifications' => 'Notifications',
            'privacy' => 'Privacy'
        ];

        if (!array_key_exists($page, $sections)) {
            $page = 'general';
        }

        include __DIR__ . '/../../views/authorized_users/settings/settings_template.php';
    }
}