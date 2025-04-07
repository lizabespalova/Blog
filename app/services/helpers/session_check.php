<?php
session_start();
$user = $_SESSION['user'] ?? null; // Если пользователь не авторизован, будет null
?>
