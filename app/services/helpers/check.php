<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use controllers\authorized_users_controllers\ProfileController;
use models\Session;
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
    $userdata = $customerModel->get_user_by_id($user_id);
    if ($userdata && md5($userdata['user_hash']) === $_COOKIE['hash']) {
            // Хеш совпадает
            $_SESSION['user'] = [
                'user_id' => $userdata['user_id'],
                'user_description' => $userdata['user_description'],
                'user_avatar' => $userdata['user_avatar'],
                'user_login' => $userdata['user_login'],
                'user_specialisation' => $userdata['user_specialisation'],
                'user_company' => $userdata['user_company'],
                'user_experience' => $userdata['user_experience'],
                'user_articles' => $userdata['user_articles'],
                'login_error_message'=> $userdata['login_error_message'],
                'created_at'=> $userdata['created_at']
            ];
            $userModel = new User(getDbConnection());
            $user = $userModel->get_user_by_id($userdata['user_id']);

            // Создаем объект сессии и добавляем новую сессию
            $sessionModel = new Session($conn);
            $sessionId = session_id(); // Получаем ID текущей сессии
            $userAgent = $_SERVER['HTTP_USER_AGENT']; // Получаем информацию о браузере
            // Пример использования:
            $ipAddress = getRealIP();
            $location = getUserLocation($ipAddress);

            $sessionModel->addSession($user_id, $sessionId, $userAgent, $ipAddress, $location);

            header('Location: /profile' . '/'. $user['user_login']); // Путь к странице профиля
            exit();
    }
} else {
    echo "Enable cookies";
}
function getRealIP() {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Иногда в заголовке X-Forwarded-For может быть несколько IP-адресов, разделенных запятой
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } else {
        $ip = 'Unknown';
    }
    return $ip;
}

function getUserLocation($ip) {
    // Если это локальный адрес (например, ::1 или 127.0.0.1), вернем "Unknown"
    if ($ip == '::1' || $ip == '127.0.0.1') {
        return "Unknown";
    }

    // Получаем данные геолокации с помощью ip-api
    $response = file_get_contents("http://ip-api.com/json/{$ip}?fields=city,country");
    $data = json_decode($response, true);
    return ($data && isset($data['city'], $data['country'])) ? "{$data['city']}, {$data['country']}" : "Unknown";
}

?>
