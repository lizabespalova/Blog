<?php

namespace controllers\authorized_users_controllers;

class EditController
{
    private $userModel;

    public function __construct($dbConnection) {
        $this->userModel = new \models\User($dbConnection);
    }
    public function show_edit(){
        include __DIR__ . '/../../views/authorized_users/edit_description.php';
    }
}