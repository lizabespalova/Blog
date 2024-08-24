<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use controllers\authorized_users_controllers\ProfileController;
use models\User;

// Подключаем необходимые файлы
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../../config/config.php';

//// Начинаем сессию
session_start();

// Соединение с базой данных
$conn = getDbConnection();
$customerModel = new User($conn);

// Проверяем наличие куки
if (isset($_COOKIE['id'], $_COOKIE['hash'])) {
    $user_id = intval($_COOKIE['id']);
    $userdata = $customerModel->getUserById($user_id);
    if ($userdata && md5($userdata['user_hash']) === $_COOKIE['hash']) {
        // Хеш совпадает
        $_SESSION['user'] = [
            'user_avatar' => $userdata['user_avatar'],
            'user_login' => $userdata['user_login'],
            'user_specialisation' => $userdata['user_specialisation'],
            'user_company' => $userdata['user_company'],
            'user_experience' => $userdata['user_experience'],
            'user_articles' => $userdata['user_articles'],
        ];
        header('Location: /profile'); // Путь к странице профиля
        exit();

        exit();
    } else {
        echo "Authorization error";
        echo "Stored hash: " . md5($userdata['user_hash']) . "<br>"; // Используйте md5 для отладки
        echo "Cookie hash: " . $_COOKIE['hash'] . "<br>";
    }
} else {
    echo "Enable cookies";
}

?>
