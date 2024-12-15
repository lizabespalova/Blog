<?php

namespace services;

use models\User;

class AuthService
{

    public function __construct($dbConnection) {
        $this->userModel = new User($dbConnection);
    }

    public function generate_code($length = 6) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0, $clen)];
        }
        return $code;
    }
}