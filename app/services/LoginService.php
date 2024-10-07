<?php

namespace services;

use models\User;

class LoginService
{
    private $errorService;
    private $userModel;
    public function __construct($dbConnection) {
        $this->userModel = new User($dbConnection);
        $this->errorService = new ErrorService();
    }

    public function check_authorisation(){
          if (!isset($_SESSION['user']['user_login'])) {
            header('Location: /login');
            exit();
          }
    }
//    public function checkAuthentication()
//    {
//
//        session_start();
//
//        if (isset($_COOKIE['id'], $_COOKIE['hash'])) {
//            $user_id = intval($_COOKIE['id']);
//            $userdata = $this->userModel->get_user_by_id($user_id);
//
//            if ($userdata && md5($userdata['user_hash']) === $_COOKIE['hash']) {
//                // Хеш совпадает, сохраняем сессию
//                $_SESSION['user'] = $userdata;
//
//                // Редирект на страницу профиля
//                header('Location: /profile/' . urlencode($userdata['user_login']));
//                exit();
//            } else {
//             $this->errorService->show_error("Authorization error.");
//            }
//        } else {
//            $this->errorService->show_error("Enable cookies.");
//        }
//    }
}