<?php
// Соединение с базой данных
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../../config/config.php';

$conn = getDbConnection();
$customerModel = new \models\User($conn);
// Проверяем наличие куки с идентификатором пользователя и хешем
if (isset($_COOKIE['id'], $_COOKIE['hash'])) {
    // Получаем данные пользователя из БД
    $user_id = intval($_COOKIE['id']);
    $userdata = $customerModel->getUserAuthDataById($user_id);

    // Проверяем, есть ли запись о пользователе с таким ID в БД и совпадает ли хеш
    if ($userdata && $userdata['user_hash'] === $_COOKIE['hash']) {
        // Перенаправляем пользователя на другую страницу, так как авторизация прошла успешно
        header("Location: /app/views/authorized_users/profile.php");
//        include __DIR__ .'/../authorized_users/profile.php';
//        include ('E:\LisasBlog\app\views\authorized_users\authorized.php');
        exit(); // Убедитесь, что после вызова header() следует вызов exit(), чтобы прекратить выполнение текущего скрипта
    } else {
        // Если хеш не совпадает или запись не найдена, выводим сообщение об ошибке авторизации
        echo "Authorization error";
    }
} else {
    // Если куки отсутствуют, выводим сообщение о необходимости включения куки
    echo "Enable cookies";
}
?>
