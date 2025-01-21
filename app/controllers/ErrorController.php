<?php

namespace controllers;

class ErrorController
{
    public function show_error(){
        include __DIR__ . '/../services/helpers/error_message.php';
    }
    public function show_success(){
        include __DIR__ . '/../services/helpers/success_message.php';
    }
}