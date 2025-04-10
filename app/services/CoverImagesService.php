<?php

namespace services;
require __DIR__ . '/../../vendor/autoload.php';

use Cloudinary\Api\Exception\ApiError;
use \Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use Exception;


class CoverImagesService
{

    public function upload_cover_image($coverImage, $path)
    {
        if ($coverImage) {
            // Создаем директорию для обложки (папка на сервере)
            $coverDir = $this->create_user_directory($path);

            // Удаляем старое изображение, если оно существует, чтобы в директории оставалось только одно
            $existingFiles = glob($coverDir . '/*'); // Получаем все файлы в папке cover
            foreach ($existingFiles as $file) {
                if (is_file($file)) {
                    unlink($file); // Удаляем файл
                }
            }

//            // Получаем имя файла обложки
//            $cover_image_name = basename($_FILES['cover_image']['name']);

            // Загружаем изображение в Cloudinary
            $cover_image_path = isset($_FILES['cover_image']['tmp_name']) ? $this->uploadToCloudinary($_FILES['cover_image']['tmp_name']) : null;
            return $cover_image_path;
//            if ($cover_image_path) {
//                // Возвращаем URL изображения для сохранения в базе данных
//                return $cover_image_path; // Путь к изображению на Cloudinary
//            } else {
////                echo "Error uploading cover image.";
//                return false;
//            }
        }
        return false;
    }

// Метод для загрузки изображения в Cloudinary
    private function uploadToCloudinary($filePath)
    {
        // Инициализируем конфигурацию Cloudinary
        initCloudinaryConfig();

        try {
            // Загружаем файл на Cloudinary
            $response = (new UploadApi())->upload($filePath);

            // Если загрузка прошла успешно, возвращаем URL изображения
            return $response['secure_url'];
        } catch (ApiError $e) {
            // Обработка ошибок API Cloudinary
            echo "Cloudinary API error: " . $e->getMessage();
            return false;
        } catch (Exception $e) {
            // Общая ошибка
            echo "Ошибка при загрузке изображения в Cloudinary: " . $e->getMessage();
            return false;
        }
    }
    public function create_user_directory($userDir)
    {
        if (!file_exists($userDir)) {
            mkdir($userDir, 0777, true);
        }
        return $userDir;
    }



//    Для локалхоста
//    public function upload_cover_image($coverImage, $path)
//    {
//        if ($coverImage) {
//            // Создаем директорию для обложки
//            $coverDir = $this->create_user_directory($path);
//
//            // Удаляем старое изображение, если оно существует, чтобы в директории оставалось только одно
//            $existingFiles = glob($coverDir . '/*'); // Получаем все файлы в папке cover
//            foreach ($existingFiles as $file) {
//                if (is_file($file)) {
//                    unlink($file); // Удаляем файл
//                }
//            }
//
//            // Получаем новое имя файла обложки
//            $cover_image_name = basename($_FILES['cover_image']['name']);
//            $cover_image_path = $coverDir . '/' . $cover_image_name;
//
//            // Перемещаем загруженный файл
//            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $cover_image_path)) {
//                // Возвращаем путь для сохранения в базе данных
//                return $cover_image_path;
//            } else {
//                echo "Error uploading cover image.";
//                return false;
//            }
//        }
//        return false;
//    }
//    public function create_user_directory($userDir)
//    {
//        if (!file_exists($userDir)) {
//            mkdir($userDir, 0777, true);
//        }
//        return $userDir;
//    }
}