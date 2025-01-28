<?php

require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../../config/config.php';

use models\User;

session_start();
if (isset($_SESSION['user']['user_id'])) {
// var_dump($_SESSION['user']['user_id']);
    $userModel = new User(getDbConnection());
    $userModel->setStatus($_SESSION['user']['user_id']);
}
?>
