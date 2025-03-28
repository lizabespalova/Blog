<?php

namespace models;

use Exception;

class User
{
    private $conn;
    private $session;
    private $setting;
    private $repost;
    private $notification;
    private $comment;
    private $article;
    private $articleReaction;


    public function __construct($conn) {
        $this->conn = $conn;
        $this->session = new Session($conn);
        $this->setting = new Settings($conn);
        $this->repost = new Reposts($conn);
        $this->notification = new Notifications($conn);
        $this->comment = new Comment($conn);
        $this->article = new Articles($conn);
        $this->articleReaction = new ArticleReactions($conn);
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
    public function getUserEmail($userId){
        $stmt = $this->conn->prepare("SELECT user_email FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['user_email'];
    }
//    public function get_key($email) {
//        $stmt = $this->conn->prepare("SELECT user_key FROM users WHERE user_email = ?");
//        $stmt->bind_param("s", $email);
//        $stmt->execute();
//        $user_key = null;
//        $stmt->bind_result($user_key);
//        $stmt->fetch();
//        $stmt->close();
//        return $user_key;
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
        return true;
    }
    public function updateUserLink($userId, $newLink) {
        $query = "UPDATE users SET link = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $newLink, $userId);
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
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
    public function create_temporary_user($login, $email, $password, $token, $userId = null) {
        // Подготовка SQL-запроса с ON DUPLICATE KEY UPDATE
        $stmt = $this->conn->prepare("
        INSERT INTO temporary_users (user_login, user_email, user_password, confirmation_token,  user_id)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
        user_login = VALUES(user_login),
        user_password = VALUES(user_password),
        confirmation_token = VALUES(confirmation_token),
        user_id = VALUES(user_id)
    ");
        $stmt->bind_param("ssssi", $login, $email, $password, $token, $userId);
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
        $newUserId = $this->conn->insert_id; // Получаем ID вставленной записи
        $stmt->close();
        return $newUserId; // Возвращаем ID нового пользователя
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
    public function getLoginById($userId) {
        $userQuery = "SELECT user_login FROM users WHERE user_id = ?";
        $userStmt = $this->conn->prepare($userQuery);
        $userStmt->bind_param('i', $userId);
        $userStmt->execute();
        $userStmt->bind_result($userLogin);
        $userStmt->fetch();
        $userStmt->close();
        return $userLogin;
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

    public function trackUserInterest(int $userId, string $category): void
    {
        $query = "
        INSERT INTO user_interests (user_id, category, interest_level)
        VALUES (?, ?, 1)
        ON DUPLICATE KEY UPDATE
            interest_level = interest_level + 1
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $userId, $category);
        $stmt->execute();
    }
    public function updateUser($userId, $login, $link, $email = null) {
        // Формируем SQL-запрос
        $query = "UPDATE users SET user_login = ?,link = ?";

        // Если почта передана, добавляем её в запрос
        if (!is_null($email)) {
            $query .= ", user_email = ?";
        }
        $query .= " WHERE user_id = ?";

        // Подготовка запроса
        $stmt = $this->conn->prepare($query);

        // Привязываем параметры
        if (!is_null($email)) {
            $stmt->bind_param('sssi', $login, $link, $email, $userId);
        } else {
            $stmt->bind_param('ssi', $login, $link, $userId);
        }

        return $stmt->execute();
    }


    public function getPasswordByUserId($userId) {
        $stmt = $this->conn->prepare("SELECT user_password FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    public function isEmailExist($email, $userId) {
        $query = "SELECT COUNT(*) FROM users WHERE user_email = ? AND user_id != ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('si', $email, $userId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        return $count > 0; // Возвращает true, если email уже занят
    }
    public function updateUserFromTemporary($userId) {
        // SQL-запрос для обновления записи по user_id
        $query = "
        UPDATE users AS u
        JOIN temporary_users AS t
        ON u.user_id = t.user_id
        SET 
            u.user_login = t.user_login,
            u.user_email = t.user_email,
            u.user_password = t.user_password,
            u.created_at = t.created_at
        WHERE t.user_id = ?";

        // Подготовка и выполнение запроса
        $stmt = $this->conn->prepare($query);

        if ($userId !== null) {
            $stmt->bind_param("i", $userId);
        }

        $stmt->execute();
        $stmt->close();
    }

    public function setStatus($userId){
        $currentTime = date('Y-m-d H:i:s');
        // Подготавливаем SQL-запрос
        $stmt = $this->conn->prepare("UPDATE users SET last_active_at = ? WHERE user_id = ?");
        if ($stmt === false) {
            die("Ошибка подготовки запроса: " . $this->conn->error);
        }
        // Привязываем параметры
        $stmt->bind_param("si", $currentTime, $userId);
        // Выполняем запрос
        if (!$stmt->execute()) {
            die("Ошибка выполнения запроса: " . $stmt->error);
        }
        // Закрываем подготовленный запрос и соединение
        $stmt->close();
        $this->conn->close();
    }
    public function getStatus($userId) {
        // Подготавливаем SQL-запрос для получения времени последней активности
        $stmt = $this->conn->prepare("SELECT last_active_at FROM users WHERE user_id = ?");

        if ($stmt === false) {
            die("Ошибка подготовки запроса: " . $this->conn->error);
        }

        // Привязываем параметры
        $stmt->bind_param("i", $userId);

        // Выполняем запрос
        if (!$stmt->execute()) {
            die("Ошибка выполнения запроса: " . $stmt->error);
        }

        // Получаем результат
        $stmt->bind_result($lastActiveAt);
        $stmt->fetch();

        // Закрываем подготовленный запрос и соединение
        $stmt->close();

        // Возвращаем результат
        return $lastActiveAt;
    }
    public function deleteAccount($user_id) {
         $this->session->deleteSessionByUserId($user_id);
         $this->article->deleteArticleByUserId($user_id);
         (new Follows($this->conn))->deleteFollowerAndFollowingByUserId($user_id);
         (new Follows($this->conn))->deleteFollowRequestByUserId($user_id);
         $this->articleReaction->deleteReactionByUserId($user_id);
         $this->comment->deleteCommentByUserId($user_id);
         (new ArticleComments($this->conn))->deleteReactionsByUserId($user_id);
         $this->notification->deleteNotificationsByUserId($user_id);
         $this->deleteUserInterestsByUserId($user_id);
         $this->setting->deleteSettingsByUserId($user_id);
         $this->repost->deleteRepostByUserId($user_id);
         (new Favourites($this->conn))->deleteFavouriteByUserId($user_id);
         $this->deleteUser($user_id);
    }
    public function deleteUserInterestsByUserId($user_id){
        $stmt = $this->conn->prepare("DELETE FROM user_interests WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }
    public function deleteUser($user_id){
        $stmt = $this->conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }
    public function getUsersBySearchQuery($query){
        if (strlen($query) < 2) {
            echo json_encode([]);
            return;
        }

        $stmt = $this->conn->prepare("SELECT user_id, user_login, user_email, user_avatar FROM users WHERE user_login LIKE CONCAT('%', ?, '%') OR user_email LIKE CONCAT('%', ?, '%') LIMIT 10");
        if (!$stmt) {
            echo json_encode(['error' => 'Database error']);
            return;
        }

        $stmt->bind_param("ss", $query, $query);
        $stmt->execute();

        $result = $stmt->get_result();
        $users = [];

        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        return $users;
    }
}
