<?php

namespace models;

use Exception;

class User
{
    private $link;

    public function __construct($link) {
        $this->link = $link;
    }

    public function getUserByLogin($login) {
        $stmt = $this->link->prepare("SELECT * FROM users WHERE user_login = ? LIMIT 1");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getUserByEmail($email) {
        $stmt = $this->link->prepare("SELECT * FROM users WHERE user_email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getUserByKey($key) {
        $stmt = $this->link->prepare("SELECT * FROM users WHERE user_key = ? LIMIT 1");
        $stmt->bind_param("s", $key);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

//    public function createUser($login, $email, $password, $change_key) {
//        $stmt = $this->link->prepare("INSERT INTO users (user_login, user_email, user_password, user_key) VALUES (?, ?, ?, ?)");
//        $stmt->bind_param("ssss", $login, $email, $password, $change_key);
//        $stmt->execute();
//        $stmt->close();
//    }

    public function setKey($login, $key) {
        $login = mysqli_real_escape_string($this->link, $login);
        $key = mysqli_real_escape_string($this->link, $key);
        $createdAt = date('Y-m-d H:i:s'); // Текущее время
        mysqli_query($this->link, "UPDATE users SET user_key='$key', key_created_at='$createdAt' WHERE user_login='$login'");
    }

    public function getKey($email) {
        $stmt = $this->link->prepare("SELECT user_key FROM users WHERE user_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user_key = null;
        $stmt->bind_result($user_key);
        $stmt->fetch();
        $stmt->close();
        return $user_key;
    }

//    public function activateUser($user_id) {
//        $stmt = $this->link->prepare("UPDATE users SET is_active = 1, user_key = NULL WHERE user_id = ?");
//        $stmt->bind_param("i", $user_id);
//        $stmt->execute();
//        $stmt->close();
//    }

    public function updatePassword($login, $newPassword) {
        $stmt = $this->link->prepare("UPDATE users SET user_password = ? WHERE user_login = ?");
        $stmt->bind_param('ss', $newPassword, $login);
        return $stmt->execute();
    }

    public function updateUserAvatar($userId, $avatarPath) {
        $stmt = $this->link->prepare("UPDATE users SET user_avatar = ? WHERE user_id = ?");
        $stmt->bind_param("si", $avatarPath, $userId);
        $stmt->execute();
        $stmt->close();
    }

    public function getUserById($userId) {
        $stmt = $this->link->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateUserHash($user_id, $hash, $attach_ip = false) {
        $stmt = $this->link->prepare("UPDATE users SET user_hash = ?" . ($attach_ip ? ", user_ip = INET_ATON(?)" : "") . " WHERE user_id = ?");
        if ($attach_ip) {
            $stmt->bind_param("ssi", $hash, $_SERVER['REMOTE_ADDR'], $user_id);
        } else {
            $stmt->bind_param("si", $hash, $user_id);
        }
        return $stmt->execute();
    }
    // Создание временного пользователя
    public function createTemporaryUser($login, $email, $password, $token) {
        $stmt = $this->link->prepare("INSERT INTO temporary_users (user_login, user_email, user_password, confirmation_token) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $login, $email, $password, $token);
        $stmt->execute();
        $stmt->close();
    }

    // Получение временного пользователя по токену
    public function getTemporaryUserByToken($token) {
        $stmt = $this->link->prepare("SELECT * FROM temporary_users WHERE confirmation_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    // Перемещение временного пользователя в основную таблицу
    public function moveToMainTable($login, $email, $password) {
        $stmt = $this->link->prepare("INSERT INTO users (user_login, user_email, user_password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $login, $email, $password);
        $stmt->execute();
        $stmt->close();
    }

    // Удаление временного пользователя
    public function deleteTemporaryUser($token) {
        $stmt = $this->link->prepare("DELETE FROM temporary_users WHERE confirmation_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->close();
    }
    //Логин должен быть уникальным
    public function updateUserProfile($user_login, $data)
    {
        // Исправленный запрос без user_articles
        $sql = "UPDATE users SET 
                user_specialisation = ?,
                user_company = ?,
                user_experience = ?
            WHERE user_login = ?";

        // Подготавливаем запрос
        $stmt = $this->link->prepare($sql);

        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . $this->link->error);
        }

        // Привязываем параметры
        // 'sss' - строки для всех параметров
        // Если user_experience является числом, то можно использовать 'sssi'
        $stmt->bind_param('ssis',
            $data['user_specialisation'],
            $data['user_company'],
            $data['user_experience'],
            $user_login
        );

        // Выполняем запрос
        $result = $stmt->execute();

        if ($result === false) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }

        return $result; // Возвращает true при успешном выполнении
    }

}
