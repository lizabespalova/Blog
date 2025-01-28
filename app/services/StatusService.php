<?php

namespace services;

class StatusService
{
    function isUserOnline($lastActiveAt) {
        $lastActiveTime = strtotime($lastActiveAt);
        $currentTime = time();

        // Если прошло менее 5 минут (300 секунд), считаем пользователя онлайн
        return ($currentTime - $lastActiveTime) <= 300;
    }
}