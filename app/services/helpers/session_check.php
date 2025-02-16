<?php
session_start();
$user = $_SESSION['user'] ?? null; // Если пользователь не авторизован, будет null

//if (!isset($_SESSION['user'])) {
//    // Если сессия истекла, перенаправляем на страницу входа
//    header('Location: /login'); // Замените на вашу страницу входа
//    exit();
//}
//
//// Безопасное использование user в сессиях
//$user = $_SESSION['user'];
?>
