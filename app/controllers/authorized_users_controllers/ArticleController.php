<?php

namespace controllers\authorized_users_controllers;

use models\User;

class ArticleController
{
    private $userModel;

    public function __construct($conn)
    {
        $this->userModel = new User($conn);
    }
    public function show_article_form(){
        include __DIR__ . '/../../views/authorized_users/form_article.php';
    }
}