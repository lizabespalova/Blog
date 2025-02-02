<?php
//session_start();

// Получаем язык из сессии или ставим по умолчанию 'en'
$language = $_SESSION['settings']['language']  ?? 'en';

// Список доступных языков
$availableLanguages = ['en', 'ru', 'ua', 'de'];
if (!in_array($language, $availableLanguages)) {
    $language = 'en'; // Фолбэк на английский
}

// Сохраняем язык в сессию
$_SESSION['settings']['language']  = $language;

// Подключаем нужный файл перевода
$translations = include __DIR__ . "/../../../locales/{$language}.php";
