<?php
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ .'/../../../config/config.php';
// Начало сессии
session_start();

$conn = getDbConnection();

// Получаем данные из GET запроса
$data = $_GET;

// Проверяем наличие ключа в GET данных
if (empty($data['key'])) {
    header('Location: /');
    exit;
}

$customerModel = new \models\User($conn);
$user = $customerModel->getUserByKey($data['key']);

if (!$user) {
    header('Location: /');
    exit;
}

$login = $user['user_login'];

// Проверяем, был ли отправлен запрос на установку нового пароля
if (isset($_POST['set_new_password'])) {
    $newPassword = $_POST['password'];

    // Хешируем новый пароль
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Обновляем данные пользователя в базе данных
    $customerModel->updatePassword($login, $hashedPassword); // Обновляем пароль в базе данных
    $customerModel->setKey($login, NULL);  // Очищаем ключ в базе данных

    // Перенаправляем пользователя на страницу логина после успешного сброса пароля
    header('Location: /app/views/auth/form_login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/authorization.css">
    <title>New password</title>
</head>
<body>
<section>
    <form method="POST">
        <h1>New password</h1>
        <p>Set new password</p>
        <input type="hidden" name="key" value="<?php echo htmlspecialchars($data['key']); ?>">
        <div class="inputbox">
            <i class="bi bi-envelope"></i>
            <input type="password" name="password" required>
            <label>Password</label>
        </div>
        <button type="submit" name="set_new_password">Reset password</button>
    </form>
</section>
<img id="women" src="/templates/images/women_at_the_computer.png" alt="Women">
</body>
</html>
