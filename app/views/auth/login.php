<?php
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ .'/../../../config/config.php';

function generateCode($length=6) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $clen = strlen($chars) - 1;
    while (strlen($code) < $length) {
        $code .= $chars[mt_rand(0,$clen)];
    }
    return $code;
}

// Соединение с БД
$conn = getDbConnection();
$customerModel = new \models\User($conn);

if (isset($_POST['submit'])) {
    // Получаем данные пользователя из БД по логину
    $data = $customerModel->getUserByLogin($_POST['login']);

    // Если пользователь существует
    if ($data) {
        // Проверяем введенный пароль
        if (password_verify($_POST['password'], $data['user_password'])) {
            // Генерируем случайный код для хеша авторизации
            $hash = generateCode(10);

            // Если пользователь выбрал привязку к IP
            $attach_ip = !empty($_POST['not_attach_ip']);

            // Обновляем хеш авторизации и IP в базе данных
            $customerModel->updateUserHash($data['user_id'], $hash, $attach_ip);


            // Устанавливаем куки
            setcookie("id", $data['user_id'], time()+60*60*24*30, "/");
            setcookie("hash", md5($hash), time()+60*60*24*30, "/", null, null, true); // httponly !!!

            // Перенаправляем на страницу проверки
            header("Location: check.php");
            exit();
        } else {
            // Если пароль неверный
            echo "<script>alert('You entered the wrong login or password.');</script>";
            include 'form_login.php';
        }
    } else {
        // Если пользователь не найден
        echo "<script>alert('The user with this login was not found.');</script>";
        include 'form_login.php';
    }
}
?>
