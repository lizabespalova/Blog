<?php

use models\User;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api\Exception\ApiError;

session_start();
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../vendor/autoload.php'; // Подключаем Cloudinary SDK

header('Content-Type: application/json');

$conn = getDbConnection();
$customerModel = new User($conn);

$response = ['success' => false, 'message' => ''];

if (isset($_COOKIE['id'])) {
    $user_id = intval($_COOKIE['id']);

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['avatar']['tmp_name'];
        $fileName = $_FILES['avatar']['name'];
        $fileSize = $_FILES['avatar']['size'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Подготовка для загрузки на Cloudinary
        try {
            // Загружаем изображение в Cloudinary
            $uploadApi = new UploadApi();
            $responseCloudinary = $uploadApi->upload($fileTmpPath, [
                'folder' => 'user_avatars/', // Папка на Cloudinary для аватаров
                'resource_type' => 'image',  // Загружаем как изображение
                'public_id' => 'user_' . $user_id // Уникальное имя для файла
            ]);

            // Получаем URL изображения
            $avatarPath = $responseCloudinary['secure_url'];

            // Обновляем аватар пользователя в базе данных
            $customerModel->update_user_avatar($user_id, $avatarPath);

            // Обновляем сессию с новым аватаром
            $_SESSION['user']['user_avatar'] = $avatarPath;

            // Успешный ответ
            $response['success'] = true;
            $response['avatar_url'] = $avatarPath;
        } catch (ApiError $e) {
            // Обработка ошибки загрузки в Cloudinary
            $response['message'] = "Error uploading avatar to Cloudinary: " . $e->getMessage();
        }

    } else {
        $response['message'] = "No file uploaded or upload error.";
    }
} else {
    $response['message'] = "User not authenticated.";
}

echo json_encode($response);

//для локалхоста
//
//use models\User;
//
//session_start();
//require_once __DIR__ . '/../../models/User.php';
//require_once __DIR__ . '/../../../config/config.php';
//
//header('Content-Type: application/json');
//
//$conn = getDbConnection();
//$customerModel = new User($conn);
//
//$response = ['success' => false, 'message' => ''];
//
//if (isset($_COOKIE['id'])) {
//    $user_id = intval($_COOKIE['id']);
//
//    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
//        $fileTmpPath = $_FILES['avatar']['tmp_name'];
//        $fileName = $_FILES['avatar']['name'];
//        $fileSize = $_FILES['avatar']['size'];
//        $fileNameCmps = explode(".", $fileName);
//        $fileExtension = strtolower(end($fileNameCmps));
//
//            $uploadFileDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $user_id . '/avatar/';
//            $dest_path = $uploadFileDir . 'user_' . $user_id . '.' . $fileExtension;
//            // Правильный относительный путь, который будет сохранен в базе данных:
//            $avatarPath = '/uploads/' . $user_id . '/avatar/user_' . $user_id . '.' . $fileExtension;
//
//            if (!file_exists($uploadFileDir)) {
//                mkdir($uploadFileDir, 0777, true); // Создаём директорию, если она не существует
//            }
//            else{
//                //Проверка и удаления лишних файлов
//                $existingFiles = glob($uploadFileDir . 'user_' . $user_id . '.*');
//                foreach ($existingFiles as $file) {
//                    unlink($file);
//                }
//            }
//            if (move_uploaded_file($fileTmpPath, $dest_path)) {
//                $customerModel->update_user_avatar($user_id, $avatarPath);
//                $response['success'] = true;
//                $_SESSION['user']['user_avatar'] = $avatarPath;
//            } else {
//                $response['message'] = "Error moving uploaded file.";
//            }
//    } else {
//        $response['message'] = "No file uploaded or upload error.";
//    }
//} else {
//    $response['message'] = "User not authenticated.";
//}
//
//echo json_encode($response);
//
?>
