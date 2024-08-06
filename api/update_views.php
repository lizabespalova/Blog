<?php
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../config/config.php';

$link = isset($_GET['link']) ? $_GET['link'] : '';

$response = array('status' => 'failure');

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
        // Если запись существует, обновляем количество просмотров
        $sql = "UPDATE pages SET views = views + 1 WHERE link = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $link);
        if ($stmt->execute()) {
            $response['status'] = 'success';
        } else {
            $response['error'] = 'Failed to update views';
        }
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
