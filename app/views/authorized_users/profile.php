<?php
session_start();
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../../config/config.php';

$conn = getDbConnection();
$customerModel = new \models\User($conn);

if (isset($_COOKIE['id'])) {
    $user_id = intval($_COOKIE['id']);

    // Получаем данные пользователя из базы данных
    $user = $customerModel->getUserById($user_id);

//    // Отладка: Вывод массива $user
//    echo '<pre>';
//    print_r($user);
//    echo '</pre>';

    if ($user) {
        // Подключаем шаблон и передаем данные пользователя
        include __DIR__ . '/profile_template.php';
    } else {
        echo "User not found.";
    }
} else {
    echo "User not authenticated.";
    exit();
}
