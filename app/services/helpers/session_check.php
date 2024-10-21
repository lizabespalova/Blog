<?php
session_start();

if (!isset($_SESSION['user'])) {
    // Если сессия истекла, перенаправляем на страницу входа
    header('Location: /login.php'); // Замените на вашу страницу входа
    exit();
}

// Безопасное использование user в сессиях
$user = $_SESSION['user'];
?>
