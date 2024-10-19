<?php
namespace controllers\authorized_users_controllers;
use models\ArticleImages;


class ArticleImagesController
{
    private $articleImagesModel;
    public function __construct($conn)
    {
        $this->articleImagesModel = new ArticleImages($conn);
    }
    public function delete_article_images($slug){
        if ($this->articleImagesModel->delete_images_from_db($slug)) {
            header('Location: /articles?status=deleted');
            exit();
        } else {
            // Обработка ошибки
            header('Location: /error');
            exit();
        }
    }
}