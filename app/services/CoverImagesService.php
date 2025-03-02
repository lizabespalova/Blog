<?php

namespace services;

class CoverImagesService
{
    public function upload_cover_image($coverImage, $path)
    {
        if ($coverImage) {
            // Создаем директорию для обложки
            $coverDir = $this->create_user_directory($path);

            // Удаляем старое изображение, если оно существует, чтобы в директории оставалось только одно
            $existingFiles = glob($coverDir . '/*'); // Получаем все файлы в папке cover
            foreach ($existingFiles as $file) {
                if (is_file($file)) {
                    unlink($file); // Удаляем файл
                }
            }

            // Получаем новое имя файла обложки
            $cover_image_name = basename($_FILES['cover_image']['name']);
            $cover_image_path = $coverDir . '/' . $cover_image_name;

            // Перемещаем загруженный файл
            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $cover_image_path)) {
                // Возвращаем путь для сохранения в базе данных
                return $cover_image_path;
            } else {
                echo "Error uploading cover image.";
                return false;
            }
        }
        return false;
    }
    public function create_user_directory($userDir)
    {
        if (!file_exists($userDir)) {
            mkdir($userDir, 0777, true);
        }
        return $userDir;
    }
}