<?php

namespace controllers;

class ErrorController
{
    public function show_error(){
        include __DIR__ . '/../services/helpers/error_message.php';
    }
}