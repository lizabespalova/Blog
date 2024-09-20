<?php
session_start();
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../../config/config.php';

header('Content-Type: application/json');

$conn = getDbConnection();
$customerModel = new \models\User($conn);

$response = ['success' => false, 'message' => ''];

if (isset($_COOKIE['id'])) {
    $user_id = intval($_COOKIE['id']);

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['avatar']['tmp_name'];
        $fileName = $_FILES['avatar']['name'];
        $fileSize = $_FILES['avatar']['size'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

            $uploadFileDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $user_id . '/avatar/';
            $dest_path = $uploadFileDir . 'user_' . $user_id . '.' . $fileExtension;
            // Правильный относительный путь, который будет сохранен в базе данных:
            $avatarPath = '/uploads/' . $user_id . '/avatar/user_' . $user_id . '.' . $fileExtension;

            if (!file_exists($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true); // Создаём директорию, если она не существует
            }

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
//                $avatarPath = '/app/views/authorized_users/uploads/user_' . $user_id . '.' . $fileExtension;
                $customerModel->update_user_avatar($user_id, /*$avatarPath*/ $avatarPath);

                $response['success'] = true;
             //   $response['message'] = "Avatar uploaded successfully!";
            } else {
                $response['message'] = "Error moving uploaded file.";
            }
//        }
    } else {
        $response['message'] = "No file uploaded or upload error.";
    }
} else {
    $response['message'] = "User not authenticated.";
}

echo json_encode($response);
?>
