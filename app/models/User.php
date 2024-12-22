<?php

namespace models;

use Exception;

class User
{
    private $conn;

    public function __construct($link) {
        $this->conn = $link;
    }

    public function get_user_by_login($login) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE user_login = ? LIMIT 1");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function get_user_by_email($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE user_email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function get_user_by_key($key) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE user_key = ? LIMIT 1");
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

    public function set_key($login, $key) {
        $login = mysqli_real_escape_string($this->conn, $login);
        $key = mysqli_real_escape_string($this->conn, $key);
        $createdAt = date('Y-m-d H:i:s'); // Текущее время
        mysqli_query($this->conn, "UPDATE users SET user_key='$key', key_created_at='$createdAt' WHERE user_login='$login'");
    }

    public function get_key($email) {
        $stmt = $this->conn->prepare("SELECT user_key FROM users WHERE user_email = ?");
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

    public function update_password($login, $newPassword) {
        $stmt = $this->conn->prepare("UPDATE users SET user_password = ? WHERE user_login = ?");
        $stmt->bind_param('ss', $newPassword, $login);
        return $stmt->execute();
    }

    public function update_user_avatar($userId, $avatarPath) {
        $stmt = $this->conn->prepare("UPDATE users SET user_avatar = ? WHERE user_id = ?");
        $stmt->bind_param("si", $avatarPath, $userId);
        $stmt->execute();
        $stmt->close();
    }
    public function update_user_description($userId, $description): bool
    {
        // Подготовка SQL-запроса
        $stmt = $this->conn->prepare("UPDATE users SET user_description = ? WHERE user_id = ?");
        if ($stmt === false) {
            error_log("Error preparing statement: " . $this->conn->error);
            return false;
        }

        // Связывание параметров
        $stmt->bind_param('si', $description, $userId);

        // Выполнение запроса
        if (!$stmt->execute()) {
            error_log("Error executing statement: " . $stmt->error);
            return false;
        }

        // В данном случае нам не нужно проверять количество затронутых строк
        // Мы считаем, что успешное выполнение запроса всегда возвращает true
        return true;
    }
    public function get_user_by_id($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update_user_hash($user_id, $hash, $attach_ip = false) {
        $stmt = $this->conn->prepare("UPDATE users SET user_hash = ?" . ($attach_ip ? ", user_ip = INET_ATON(?)" : "") . " WHERE user_id = ?");
        if ($attach_ip) {
            $stmt->bind_param("ssi", $hash, $_SERVER['REMOTE_ADDR'], $user_id);
        } else {
            $stmt->bind_param("si", $hash, $user_id);
        }
        return $stmt->execute();
    }
    // Создание временного пользователя
    public function create_temporary_user($login, $email, $password, $token) {
        $stmt = $this->conn->prepare("INSERT INTO temporary_users (user_login, user_email, user_password, confirmation_token) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $login, $email, $password, $token);
        $stmt->execute();
        $stmt->close();
    }

    // Получение временного пользователя по токену
    public function get_temporary_user_by_token($token) {
        $stmt = $this->conn->prepare("SELECT * FROM temporary_users WHERE confirmation_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }
    public function get_temporary_user_by_id($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM temporary_users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    // Перемещение временного пользователя в основную таблицу
    public function move_to_main_table($login, $email, $password, $link, $created_at) {
        $stmt = $this->conn->prepare("INSERT INTO users (user_login, user_email, user_password, link, created_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $login, $email, $password, $link, $created_at);
        $stmt->execute();
        $stmt->close();
    }

    // Удаление временного пользователя
    public function delete_temporary_user($userId) {
        $stmt = $this->conn->prepare("DELETE FROM temporary_users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
    }
    //Логин должен быть уникальным
    public function update_user_profile($user_login, $data)
    {
        // Исправленный запрос без user_articles
        $sql = "UPDATE users SET 
                user_specialisation = ?,
                user_company = ?,
                user_experience = ?
            WHERE user_login = ?";

        // Подготавливаем запрос
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . $this->conn->error);
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
    public function add_one_articles_to_user($userId) {
        // Подготовка запроса для увеличения значения столбца user_articles
        $query = "UPDATE users SET user_amount_of_articles = user_amount_of_articles + 1 WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . $this->conn->error);
        }

        $stmt->bind_param("i", $userId);

        // Выполнение запроса
        $result = $stmt->execute();

        if ($result === false) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }

        // Закрытие соединения
        $stmt->close();

        return $result;
    }
    public function delete_one_articles_from_user($userId) {
        // Подготовка запроса для увеличения значения столбца user_articles
        $query = "UPDATE users SET user_amount_of_articles = user_amount_of_articles - 1 WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . $this->conn->error);
        }

        $stmt->bind_param("i", $userId);

        // Выполнение запроса
        $result = $stmt->execute();

        if ($result === false) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }

        // Закрытие соединения
        $stmt->close();

        return $result;
    }
    public function get_author_avatar($author_login)
    {
        $sql = "SELECT user_avatar FROM users WHERE user_login = ?";

        // Подготовка и выполнение запроса
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $author_login);
        $stmt->execute();

        // Получение результата
        $result = $stmt->get_result();
        $author = $result->fetch_assoc();

        // Закрытие запроса
        $stmt->close();

        // Возвращаем аватар автора
        return $author;
    }
    public function getPublications($userLogin) {
        // Подготавливаем SQL-запрос
        $sql = "SELECT * 
            FROM articles 
            WHERE author = ? AND is_published = 1 
            ORDER BY created_at DESC";

        // Подготовка запроса
        if ($stmt = $this->conn->prepare($sql)) {
            // Привязываем параметры
            $stmt->bind_param('s', $userLogin); // 'i' обозначает integer

            // Выполняем запрос
            $stmt->execute();

            // Получаем результат
            $result = $stmt->get_result();
            $articles = $result->fetch_all(MYSQLI_ASSOC); // Получаем данные в виде ассоциативного массива

            // Закрываем подготовленный запрос
            $stmt->close();

            // Возвращаем массив статей
            return $articles;
        } else {
            // В случае ошибки возвращаем пустой массив
            return [];
        }
    }

    public function getUserArticlesCount($userId) {
        // SQL-запрос для получения количества статей
        $query = "SELECT user_amount_of_articles FROM users WHERE user_id = ?";

        // Подготавливаем запрос
        $stmt = $this->conn->prepare($query);
//        if (!$stmt) {
//            error_log("Ошибка подготовки запроса: " . $this->conn->error);
//            return 0;
//        }

        // Привязываем параметры
        $stmt->bind_param("i", $userId);

        // Выполняем запрос
        $stmt->execute();

        // Получаем результат
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['user_amount_of_articles'];
        }

        // Если данных нет, возвращаем 0
        return 0;
    }

    // Поиск пользователя по Google ID
    public function findUserByGoogleId($googleId) {
        // Подготовка SQL-запроса
        $stmt = $this->conn->prepare('SELECT * FROM users WHERE google_id = ?');
        if ($stmt === false) {
            die('Prepare failed: ' . $this->conn->error);
        }

        // Привязываем параметры
        $stmt->bind_param('s', $googleId);

        // Выполняем запрос
        $stmt->execute();

        // Получаем результат
        $result = $stmt->get_result();

        // Проверяем наличие записи
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        // Закрываем запрос
        $stmt->close();

        // Возвращаем null, если пользователь не найден
        return null;
    }



}
