<?php
//session_start();
//require_once __DIR__ . '/../../models/User.php';
//require_once __DIR__ . '/../../../config/config.php';
//
//header('Content-Type: application/json');
//
//$conn = getDbConnection();
//$response = ['success' => false, 'message' => ''];
//
//if (isset($_COOKIE['id']) && isset($_POST['article_id'])) {
//    $user_id = intval($_COOKIE['id']);
//    $article_id = intval($_POST['article_id']);
//
//    if (isset($_FILES['article_photo']) && $_FILES['article_photo']['error'] === UPLOAD_ERR_OK) {
//        $fileTmpPath = $_FILES['article_photo']['tmp_name'];
//        $fileName = $_FILES['article_photo']['name'];
//        $fileSize = $_FILES['article_photo']['size'];
//        $fileNameCmps = explode(".", $fileName);
//        $fileExtension = strtolower(end($fileNameCmps));
//
////        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
////        $maxFileSize = 5 * 1024 * 1024; // 5 MB
////
////        if ($fileSize > $maxFileSize) {
////            $response['message'] = "File size exceeds the maximum allowed limit of 5 MB.";
////        } elseif (!in_array($fileExtension, $allowedExts)) {
////            $response['message'] = "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
////        } else {
//            $uploadFileDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $user_id . '/articles/' . $article_id . '/';
//            $dest_path = $uploadFileDir . 'article_' . $article_id . '.' . $fileExtension;
//
//            if (!file_exists($uploadFileDir)) {
//                mkdir($uploadFileDir, 0777, true); // Создаём директорию, если она не существует
//            }
//
//            if (move_uploaded_file($fileTmpPath, $dest_path)) {
//                $avatarPath = '/uploads/' . $user_id . '/articles/' . $article_id . '/article_' . $article_id . '.' . $fileExtension;
//
//                $this->articleModel->add_article_images($article_id, $images);
////                // Здесь сохраняем путь к изображению в базу данных
////                $avatarPath = '/uploads/' . $user_id . '/articles/' . $article_id . '/article_' . $article_id . '.' . $fileExtension;
////
////                // Пример SQL-запроса для сохранения в базу
////                $sql = "INSERT INTO article_images (article_id, image_path) VALUES (?, ?)";
////                $stmt = $conn->prepare($sql);
////                if ($stmt->execute([$article_id, $avatarPath])) {
////                    $response['success'] = true;
////                } else {
////                    $response['message'] = "Failed to save image to database.";
////                }
//            } else {
//                $response['message'] = "Error moving uploaded file.";
//            }
////        }
//    } else {
//        $response['message'] = "No file uploaded or upload error.";
//    }
//} else {
//    $response['message'] = "User not authenticated or article ID missing.";
//}
//
//echo json_encode($response);
