<?php
require __DIR__ . '/../vendor/autoload.php';
// Загрузка переменных из .env файла. Закомментировала для деплоя
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Функция для получения подключения к базе данных
function getDbConnection() {
    // Подключение к базе данных
    $servername = $_ENV['MYSQLHOST'];
    $username = $_ENV['MYSQLUSER'];
    $password = $_ENV['MYSQLPASSWORD'];
    $dbname = $_ENV['MYSQL_DATABASE'];

    // Создание подключения
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Проверка подключения
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
function getEmail(){return $_ENV['EMAIL'];}
function getEmailPassword(){return $_ENV['EMAIL_PASSWORD'];}
function getPort(){return $_ENV['PORT'];}
function getHost(){return $_ENV['HOST'];}
function getSmptSecure(){return $_ENV['SMTP_SECURE'];}
function getGoogleClientId(){return $_ENV['GOOGLE_CLIENT_ID'];}
function getGoogleClientSecret(){return $_ENV['GOOGLE_CLIENT_SECRET'];}
function getRedirectUriRegister(){return $_ENV['REDIRECT_URI_REGISTER'];}
function getRedirectUriLogin(){return $_ENV['REDIRECT_URI_LOGIN'];}
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    return $protocol . "://" . $_SERVER['HTTP_HOST'];
}
function getConfirmationUrl(){return $_ENV['CONFIRMATION_URL'];}
function getUpdateEmailUrl(){return $_ENV['UPDATE_EMAIL_URL'];}
?>
