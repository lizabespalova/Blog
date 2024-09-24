<?php

namespace controllers\authorized_users_controllers;

use models\User;
use models\Articles;

class ArticleController
{
    private $userModel;
    private $articleModel;

    public function __construct($conn)
    {
        $this->userModel = new User($conn);
        $this->articleModel = new Articles($conn);
    }

    public function show_article_form()
    {
        include __DIR__ . '/../../views/authorized_users/form_article.php';
    }

    public function create_article()
    {
        session_start();
        if (!isset($_SESSION['user']['user_login'])) {
            header('Location: /login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $content = $_POST['content'];
            $author = $_SESSION['user']['user_login'];
            $user_id = $_SESSION['user']['user_id'];
            $youtube_link = $_POST['youtube_link'];

            if (empty($title) || empty($content)) {
                echo '<script type="text/javascript">
            alert("Title and content are required.");
            window.location.href = "/create-article";
        </script>';
            }
            $userDir = 'uploads/' . $user_id . '/article_photos/';
            if (!file_exists($userDir)) {
                mkdir($userDir, 0777, true);
            }

            $cover_image_path = "templates/images/article_logo.png";
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
                $cover_image_name = basename($_FILES['cover_image']['name']);
                $cover_image_path = $userDir . $cover_image_name;
                if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $cover_image_path)) {
                    echo "Error uploading cover image.";
                    return;
                }
            }


            // Добавляем статью и получаем ID
            $article_id = $this->articleModel->add_article($title, $content, $author, $cover_image_path, $youtube_link);
            var_dump($article_id);

            if ($article_id) {
                // Генерация ссылки на статью по ID
                $article_link = '/articles/' . urlencode(strtolower(str_replace(' ', '-', $title))) . '?id=' . $article_id;
               // $_SESSION['user']['user_articles'] = isset($_SESSION['user']['user_articles']) ? $_SESSION['user']['user_articles'] + 1 : 1;
                $this->articleModel->update_article_link($article_id, $article_link);
                $this->userModel->set_articles($user_id);
                if (isset($_FILES['image_path']) && is_array($_FILES['image_path']['tmp_name'])) {
                    $images = [];
                    foreach ($_FILES['image_path']['tmp_name'] as $index => $tmpName) {
                        if ($_FILES['image_path']['error'][$index] == 0) {
                            $image_name = basename($_FILES['image_path']['name'][$index]);
                            $image_path = $userDir . $image_name;
                            if (move_uploaded_file($tmpName, $image_path)) {
                                $images[] = $image_path;
                            } else {
                                echo "Error uploading image: " . $image_name;
                            }
                        }
                    }
                    $this->articleModel->add_article_images($article_id, $images);
                }


                // Перенаправление на страницу статьи
                header('Location: ' . $article_link);
                exit();
            } else {
                echo "Error creating article. Check if add_article method is working correctly.";
            }
        }
    }
    public function show_article($article_id)
    {
        if (!$article_id) {
            echo "Invalid article ID.";
            return;
        }

        // Получаем данные статьи из модели
        $article = $this->articleModel->get_article_by_id($article_id);

        if (!$article) {
            echo "Article not found.";
            return;
        }

        // Проверка, получили ли мы корректные данные статьи
        echo "<pre>";
        print_r($article);
        echo "</pre>";

        // Получаем изображения статьи, если они есть
        $images = $this->articleModel->get_article_images($article_id);
        $article['images'] = $images;

        // Передаем данные в шаблон
        include __DIR__ . '/../../views/authorized_users/article_template.php';
    }


}