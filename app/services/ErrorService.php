<?php

namespace services;

class ErrorService
{
    public function show_error($message)
    {
        $encodedMessage = urlencode($message); // Кодируем сообщение для URL
        header("Location: /error?message=$encodedMessage");
        exit(); // Завершаем выполнение текущего скрипта
    }
}