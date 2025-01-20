<?php

namespace services;

class ErrorService
{
    public function show_error($message)
    {
//        $encodedMessage = urlencode($message); // Кодируем сообщение для URL
//        header("Location: /error?message=$encodedMessage");
//        exit(); // Завершаем выполнение текущего скрипта

        // Проверяем, является ли $errors массивом
        if (!is_array($message)) {
            $message = [$message]; // Если это не массив, преобразуем в массив
        }

        // Соединяем сообщения в одну строку
        $errorMessages = implode(" ", $message);

        // Кодируем сообщение для URL
        $encodedMessage = urlencode($errorMessages);

        // Перенаправляем на страницу ошибки с сообщением
        header("Location: /../../app/services/helpers/error_message.php?message=$encodedMessage");
        exit(); // Завершаем выполнение текущего скрипта
    }
}