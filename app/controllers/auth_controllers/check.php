<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use controllers\authorized_users_controllers\ProfileController;
use models\User;

// Подключаем необходимые файлы
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../../config/config.php';

//// Начинаем сессию
//session_start();

// Соединение с базой данных
$conn = getDbConnection();
$customerModel = new User($conn);

// Проверяем наличие куки
if (isset($_COOKIE['id'], $_COOKIE['hash'])) {
    // Получаем данные пользователя из базы данных по ID
    $user_id = intval($_COOKIE['id']);
    $userdata = $customerModel->getUserById($user_id);

    // Проверяем, совпадает ли хеш из куков с хешем в базе данных
    if ($userdata && $userdata['user_hash'] === $_COOKIE['hash']) {
        // Если хеш совпадает, сохраняем данные пользователя в сессии
        $_SESSION['user'] = [
            'user_avatar' => $userdata['user_avatar'],
            'user_login' => $userdata['user_login'],
            'user_specialisation' => $userdata['user_specialisation'],
            'user_company' => $userdata['user_company'],
            'user_experience' => $userdata['user_experience'],
            'user_articles' => $userdata['user_articles'],
        ];

        // Перенаправляем пользователя на страницу профиля
        $profileController = new ProfileController($conn);
        $profileController->showProfile();
        exit(); // Завершаем выполнение скрипта после отображения профиля
    } else {
        // Если хеш не совпадает, выводим сообщение об ошибке авторизации
        echo "Authorization error";
    }
} else {
    // Если куки отсутствуют, выводим сообщение о необходимости включения куки
    echo "Enable cookies";
}
?>
