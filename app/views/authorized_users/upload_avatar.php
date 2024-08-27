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

        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
        $maxFileSize = 5 * 1024 * 1024; // 5 MB

        if ($fileSize > $maxFileSize) {
            $response['message'] = "File size exceeds the maximum allowed limit of 5 MB.";
        } elseif (!in_array($fileExtension, $allowedExts)) {
            $response['message'] = "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
        } else {
            $uploadFileDir = __DIR__ . '/uploads/';
            $dest_path = $uploadFileDir . 'user_' . $user_id . '.' . $fileExtension;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $avatarPath = '/app/views/authorized_users/uploads/user_' . $user_id . '.' . $fileExtension;
                $customerModel->update_user_avatar($user_id, $avatarPath);

                $response['success'] = true;
             //   $response['message'] = "Avatar uploaded successfully!";
            } else {
                $response['message'] = "Error moving uploaded file.";
            }
        }
    } else {
        $response['message'] = "No file uploaded or upload error.";
    }
} else {
    $response['message'] = "User not authenticated.";
}

echo json_encode($response);
?>
