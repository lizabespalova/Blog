<?php
namespace controllers\authorized_users_controllers;

class LogoutController
{
    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        // После выхода перенаправляем на страницу входа
        header("Location: /index.php");
        exit();
    }
}