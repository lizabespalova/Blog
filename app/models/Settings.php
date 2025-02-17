<?php

namespace models;

class Settings
{
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }
// Получить настройки по user_id
    public function getSettingsByUserId($user_id) {
        $query = "SELECT * FROM settings WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null; // Возвращаем null, если настройки не найдены
    }

    // Установить стиль шрифта
    public function setFontStyle($user_id, $fontStyle) {
        $validStyles = ['sans-serif', 'serif', 'monospace', 'georgia', 'verdana'];
        if (in_array($fontStyle, $validStyles)) {
            $query = "UPDATE settings SET font_style = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("si", $fontStyle, $user_id);
            return $stmt->execute();
        }
        return false; // Некорректный стиль шрифта
    }
    public function setLanguage($language, $user_id) {
        $stmt = $this->conn->prepare("UPDATE settings SET language = ? WHERE user_id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $language, $user_id);
            $stmt->execute();
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
            exit;
        }
    }
    // Установить размер шрифта
    public function setFontSize($user_id, $fontSize) {
        if (is_numeric($fontSize) && $fontSize > 0 && $fontSize <= 100) { // Проверка на диапазон
            $query = "UPDATE settings SET font_size = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ii", $fontSize, $user_id);
            return $stmt->execute();
        }
        return false; // Некорректный размер шрифта
    }

    // Установить тему
    public function setTheme($user_id, $theme) {
        // Проверяем, существует ли настройка для данного пользователя
        $query = "SELECT * FROM settings WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Если запись существует, обновляем настройки
            $query = "UPDATE settings SET theme = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("si", $theme, $user_id);
            return $stmt->execute();
        } else {
            // Если записи нет, создаем новую строку с настройками по умолчанию
            $query = "INSERT INTO settings (user_id, theme) VALUES (?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("is", $user_id, $theme);
            return $stmt->execute();
        }
    }
    // Установить видимость профиля
    public function setProfileVisibility($userId, $profileVisibility) {

        // Запрос для обновления данных
        $query = "UPDATE settings SET profile_visibility = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $profileVisibility, $userId);

        return $stmt->execute(); // Выполняем запрос
    }
    public function getLanguage($userId){
        $stmt = $this->conn->prepare("SELECT language FROM settings WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($language);
        $stmt->fetch();
        $stmt->close();
        return $language;
    }
    public function getProfileVisibility($userId)
    {
        // Подготовка SQL-запроса
        $query = "SELECT profile_visibility FROM settings WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            die("Ошибка подготовки запроса: " . $this->conn->error);
        }
        // Привязываем параметры
        $stmt->bind_param("i", $userId);
        // Выполняем запрос
        if (!$stmt->execute()) {
            die("Ошибка выполнения запроса: " . $stmt->error);
        }
        // Получаем результат
        $result = $stmt->get_result();
        $profileVisibility = null;
        if ($row = $result->fetch_assoc()) {
            $profileVisibility = $row['profile_visibility'];
        }
        // Закрываем подготовленный запрос
        $stmt->close();
        return $profileVisibility;
    }
    // Установить параметр "показать последний визит"
    public function setShowLastSeen($userId, $showLastSeen) {
        // Проверяем, что значение для показа/непоказа последнего визита — булево
        $showLastSeen = $showLastSeen ? 1 : 0; // Преобразуем в 1/0 для базы данных

        // Запрос для обновления данных
        $query = "UPDATE settings SET show_last_seen = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $showLastSeen, $userId);

        return $stmt->execute(); // Выполняем запрос
    }
    public function getShowLastSeen($userId) {
        // Запрос для получения значения show_last_seen для пользователя
        $query = "SELECT show_last_seen FROM settings WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        // Привязываем параметр
        $stmt->bind_param("i", $userId);
        // Выполняем запрос
        if (!$stmt->execute()) {
            die("Ошибка выполнения запроса: " . $stmt->error);
        }
        // Получаем результат
        $stmt->bind_result($showLastSeen);
        $stmt->fetch();
        // Закрываем подготовленный запрос
        $stmt->close();
        // Возвращаем значение show_last_seen
        return $showLastSeen ? true : false; // Возвращаем булево значение
    }
    public function createDefaultSettings($userId) {
        // Подготовка SQL-запроса
        $stmt = $this->conn->prepare("
            INSERT INTO settings (user_id) 
            VALUES (?)
        ");
        // Привязка параметра
        $stmt->bind_param("i", $userId);
        // Выполнение запроса
        if ($stmt->execute()) {
            return true; // Успешное добавление
        } else {
            die("Error : " . $stmt->error);
        }
    }
    public function deleteSettingsByUserId($user_id){
        $stmt = $this->conn->prepare("DELETE FROM settings WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }
}