<?php
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../config/config.php';

$link = isset($_GET['link']) ? $_GET['link'] : '';

$response = array('views' => 0);

if ($link) {
    // Логирование входящих запросов
    file_put_contents('log.txt', "Link: $link\n", FILE_APPEND);

    // Получение подключения к базе данных
    $conn = getDbConnection();

    // Проверяем, существует ли запись для данной ссылки
    $sql = "SELECT views FROM pages WHERE link = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $link);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response['views'] = $row['views'];
    } else {
        $response['error'] = 'No record found';
    }

    $stmt->close();
    $conn->close();
} else {
    $response['error'] = 'No link parameter provided';
}

echo json_encode($response);
?>
