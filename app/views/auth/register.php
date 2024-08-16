<?php
// Страница регистрации нового пользователя

// Подключаем модель Customer
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ .'/../../../config/config.php';

// Проверяем, если форма была отправлена
if(isset($_POST['register'])) {
    $err = [];

    // Проверяем логин
    if(!preg_match("/^[a-zA-Z0-9\-_]+$/",$_POST['login'])) {
        $err[] = "The login can consist only of Latin letters, numbers, hyphens and underscores";
    }

    if(strlen($_POST['login']) < 3 or strlen($_POST['login']) > 30) {
        $err[] = "The login must be no less than 3 characters and no more than 30";
    }


    $conn = getDbConnection();

    $customerModel = new \models\User($conn);
    $existingCustomer = $customerModel->getUserByLogin($_POST['login']);
    if($existingCustomer) {
        $err[] = "A user with this login already exists in the database";
    }

    // Если нет ошибок, то добавляем в БД нового пользователя
    if(empty($err)) {
        $login = $_POST['login'];
        $customerModel->login = $login;
        $email = $_POST['email'];
        // Хешируем пароль при регистрации или изменении
        $hashedPassword = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

        $customerModel->createUser($login, $email, $hashedPassword, 0);

        header("Location: profile.php");
        exit();
    } else {
        echo "<b>The following errors occurred during registration:</b><br>";
        foreach($err as $error) {
            echo $error."<br>";
        }
    }
}
?>
