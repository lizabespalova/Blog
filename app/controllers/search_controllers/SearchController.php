<?php

namespace controllers\search_controllers;
use models\User;
use Exception;
class SearchController
{
    private $userModel;

    public function __construct($dbConnection) {
        $this->userModel = new \models\User($dbConnection);
    }

    public function show_search_form(){
        include __DIR__ . '/../../views/search/form_search.php';
    }
}