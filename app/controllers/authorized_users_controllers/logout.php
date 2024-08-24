<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use controllers\authorized_users_controllers\LogoutController;

$logoutController = new LogoutController();
$logoutController->logout();
