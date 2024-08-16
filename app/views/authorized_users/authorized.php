<?php
//// Убедитесь, что сессия не используется
//require_once __DIR__ . '/../../models/User.php';
//require_once __DIR__ . '/../../../config/config.php';
//
//$conn = getDbConnection();
//$customerModel = new \models\User($conn);
//
//    if (isset($_COOKIE['id'])) {
//        $user_id = intval($_COOKIE['id']);
//        $user = $customerModel->getUserById($user_id);
//
//        if (!$user) {
//            echo "User not found!";
//            exit();
//        }
//
//        // Подключаем шаблон профиля, передавая в него данные пользователя
//        include __DIR__ . '/profile_template.php';
//    } else {
//        echo "User not authenticated.";
//    }
//?>
