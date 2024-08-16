<?php
//require_once __DIR__ . '/../../models/User.php';
//require_once __DIR__ .'/../../../config/config.php';
//// Начало сессии для передачи $data
//session_start();
//
//$conn = getDbConnection();
//
//// Получаем данные из GET запроса
//$data = $_GET;
//
//// Сохраняем данные в сессии для использования на форме
//$_SESSION['data'] = $data;
//
//// Проверяем наличие ключа в GET данных
//if (empty($data['key'])) {
//    header('Location: /');
//    exit;
//}
//
//$customerModel = new \models\User($conn);
//$user = $customerModel->getUserByKey($data['key']);
//
//if (!$user) {
//    header('Location: /');
//    exit;
//}
//
//$login = $user['user_login'];
//
//// Проверяем, был ли отправлен запрос на установку нового пароля
//if (isset($_POST['set_new_password'])) {
//    $newPassword = $_POST['password'];
//
//    // Хешируем новый пароль
//    $customerModel->password = password_hash($newPassword, PASSWORD_DEFAULT);
//
//    // Очищаем ключ для сброса пароля, чтобы он больше не использовался
//    $customerModel->change_key = NULL;
//
//    // Обновляем данные пользователя в базе данных
//    $customerModel->setKey($login, NULL);  // Очищаем ключ в базе данных
//    $customerModel->updatePassword($login, $customerModel->password); // Обновляем пароль в базе данных
//
//    // Перенаправляем пользователя на страницу логина после успешного сброса пароля
//    header('Location: /form_login.php');
//    exit;
//}
//?>
