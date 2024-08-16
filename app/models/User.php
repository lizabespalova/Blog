<?php
namespace models;
class User
{
    private $link;
    public $login;
    public $change_key;
    public $password;
    public function __construct($link) {
        $this->link = $link;
    }
    public function getUserByLogin($login) {
        $query = mysqli_query($this->link, "SELECT * FROM users WHERE user_login='".mysqli_real_escape_string($this->link, $login)."' LIMIT 1");
        return mysqli_fetch_assoc($query);
    }
    public function getUserByEmail($email) {
        $query = mysqli_query($this->link, "SELECT * FROM users WHERE user_email='".mysqli_real_escape_string($this->link, $email)."' LIMIT 1");
        return mysqli_fetch_assoc($query);
    }
    public function getUserByKey($key) {
        $query = mysqli_query($this->link, "SELECT * FROM users WHERE user_key='".mysqli_real_escape_string($this->link, $key)."' LIMIT 1");
        return mysqli_fetch_assoc($query);
    }
    public function createUser($login, $email, $password, $change_key) {
        $login = mysqli_real_escape_string($this->link, $login);
        $email = mysqli_real_escape_string($this->link, $email);
        $password = mysqli_real_escape_string($this->link, $password);
        $change_key = mysqli_real_escape_string($this->link, $change_key);
        mysqli_query($this->link, "INSERT INTO users (user_login, user_email, user_password, user_key) VALUES ('$login', '$email', '$password', '$change_key')");
    }

    public function setKey($login, $key) {
        $login = mysqli_real_escape_string($this->link, $login);
        $key = mysqli_real_escape_string($this->link, $key);
        mysqli_query($this->link, "UPDATE users SET user_key='$key' WHERE user_login='$login'");
        $this->change_key = $key;
    }
    public function updatePassword($login, $newPassword) {
        $sql = "UPDATE users SET user_password = ? WHERE user_login = ?";
        $stmt = $this->link->prepare($sql);
        $stmt->bind_param('ss', $newPassword, $login);
        return $stmt->execute();
    }

    public function updateUserAvatar($userId, $avatarPath) {
        $stmt = $this->link->prepare("UPDATE users SET user_avatar = ? WHERE user_id = ?");
        $stmt->bind_param("si", $avatarPath, $userId);
        $stmt->execute();
    }

    public function getUserById($userId) {
        $stmt = $this->link->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateUserHash($user_id, $hash, $attach_ip = false) {
        $user_id = intval($user_id);
        $hash = md5($hash);
        $insip = '';

        if ($attach_ip) {
            $insip = ", user_ip=INET_ATON('".$_SERVER['REMOTE_ADDR']."')";
        }

        $query = "UPDATE users SET user_hash='$hash' $insip WHERE user_id='$user_id'";
        return mysqli_query($this->link, $query);
    }
    public function getUserAuthDataById($userId) {
        $stmt = $this->link->prepare("SELECT user_id, user_hash FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}