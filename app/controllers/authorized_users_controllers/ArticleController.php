<?php

namespace controllers\authorized_users_controllers;

use models\Articles;
use models\User;
use Parsedown;
use services\LoginService;

class ArticleController
{
    private $articleModel;
    private $userModel;
    private $loginService;

    public function __construct($conn)
    {
        $this->articleModel = new Articles($conn);
        $this->loginService = new LoginService($conn);
        $this->userModel = new User($conn);
    }

    public function show_article_form()
    {
        include __DIR__ . '/../../views/authorized_users/form_article.php';
    }
    public function create_article()
    {
        session_start();
        $this->loginService->check_authorisation();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $content = $_POST['content'];
            $youtube_link = $_POST['youtube_link'];
            $category = $_POST['category'];
            $difficulty = $_POST['difficulty'];
            $read_time = $_POST['read_time'];
            $tags = $_POST['tags'];
            $author = $_SESSION['user']['user_login'];
            $user_id = $_SESSION['user']['user_id'];

            // Валидация данных
            $this->validate_input($title, $content);

            // Ищем все изображения в контенте
            preg_match('/\[image\d+\]\(data:image\/(png|jpg|jpeg|gif);base64,([^"]+)\)/', $content, $matches);

//            // Отладка: выводим массив с найденными изображениями
//            echo "<pre>Контент";
//            print_r($content);
//            echo "</pre>";

            // Добавляем статью в базу данных и получаем её ID
            $cover_image_path = 'templates/images/article_logo.png'; // Путь по умолчанию
            $article_id = $this->articleModel->add_article($title, '', $author, $cover_image_path, $youtube_link, $category, $difficulty, $read_time, $tags);

            if ($article_id) {
                $articleDir = $this->create_user_directory('uploads/' . $user_id . '/article_photos/' . $article_id);

                // Создаем директорию для статьи (и cover)
                $this->upload_cover_image($article_id, $user_id);

                // Обрабатываем и сохраняем все найденные изображения
               $this->process_images($matches, $articleDir, $article_id, $content);
            }
            // Генерируем слаг
            $slug = $this->create_slug($author, $article_id,$title);
            $this->articleModel->update_article_slug($article_id, $slug);
            header('Location: /articles/' . $slug);
            exit(); //exit, чтобы остановить выполнение скрипта после перенаправления
        }
    }
    public function process_images($matches, $articleDir, $article_id, $content){
        $pattern = '/\[image(\d+)\]\(data:image\/[a-zA-Z]+;base64,([^\)]+)\)/';
        preg_match_all($pattern, $matches[0], $result);

        foreach ($result[2] as $index => $base64Data) {
            $photoNumber = $result[1][$index];

            // Генерация уникального имени файла для каждого изображения
            $fileName = "image_" . $photoNumber . ".jpg";
            $imagePath =  $articleDir . '/' . $fileName;

            // Сохраняем изображение на сервере
            if ($this->save_Base64Image($base64Data, $imagePath)) {
                // Если изображение успешно сохранено, сохраняем путь в базу данных
                $this->articleModel->save_image_path_to_db($article_id, $imagePath);
            }
        }
        // Заменяем base64 код в контенте на путь к изображению
        $content = preg_replace(
            $pattern,
            '[image' . $photoNumber . '](' . $imagePath . ')',
            $content
        );
        $this->articleModel->update_content($article_id, $content);
    }

    private function validate_input($title, $content)
    {
        if (empty($title) || empty($content)) {
            echo '<script type="text/javascript">
                alert("Title and content are required.");
                window.location.href = "/create-article";
              </script>';
            exit();
        }
    }
    private function create_user_directory($userDir)
    {
        if (!file_exists($userDir)) {
            mkdir($userDir, 0777, true);
        }
        return $userDir;
    }
    private function upload_cover_image($article_id, $user_id)
    {

        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
            // Создаем директорию для обложки
            $coverDir = $this->create_user_directory('uploads/' . $user_id . '/article_photos/' . $article_id . '/cover');
            // Получаем имя файла обложки
            $cover_image_name = basename($_FILES['cover_image']['name']);
            // Полный путь для сохранения файла на сервере
            $cover_image_path = $coverDir . '/' . $cover_image_name;
            // Перемещаем загруженный файл из временной директории в указанную директорию
            if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $cover_image_path)) {
                echo "Error uploading cover image.";
                return;
            }
        }
    }

    public function create_slug($author, $article_id, $title): string
    {
        // Преобразуем заголовок в нижний регистр
        $title_slug = strtolower($title);

        // Заменяем пробелы на тире
        $title_slug = str_replace(' ', '-', $title_slug);

        // Удаляем все символы, кроме букв, цифр и тире
        $title_slug = preg_replace('/[^a-z0-9\-]+/', '', $title_slug);

        // Формируем слаг с именем автора, ID статьи и заголовком
        return $author . '-' . $article_id . '-' . $title_slug;
    }

    public function show_article($slug)
    {
        // Ищем статью по слагу
        $article = $this->articleModel->get_article_by_slug($slug);

        if ($article) {
//            echo "<pre>Article";
//            print_r($article);
//            echo "</pre>";
            // Получаем данные автора (аватар)
            $author_info = $this->userModel->get_author_avatar($article['author']);
            // Парсим содержимое статьи из Markdown в HTML
            $parsedContent = $this->parseMarkdown($article['content']);

            // Передаем данные в шаблон
            include __DIR__ . '/../../views/authorized_users/article_template.php';
        } else {
            echo "Article not found.";
        }
    }
    /**
     * Сохраняет изображение, переданное в формате base64, по указанному пути.
     *
     * @param string $base64Data base64-код изображения (без префикса).
     * @param string $imagePath Путь, по которому нужно сохранить изображение.
     * @return bool Успех операции.
     */
    public function save_Base64Image($base64Data, $imagePath): bool
    {
        // Декодируем base64-код в бинарные данные
        $imageData = base64_decode($base64Data);

        // Проверяем, удалось ли декодировать данные
        if ($imageData === false) {
            echo "Ошибка: не удалось декодировать base64 данные.\n";
            return false;
        }

        // Сохраняем декодированные данные в файл по указанному пути
        $result = file_put_contents($imagePath, $imageData);

        // Проверяем, удалось ли сохранить файл
        if ($result === false) {
            echo "Ошибка: не удалось сохранить файл по пути {$imagePath}.\n";
            return false;
        }

        echo "Изображение успешно сохранено по пути {$imagePath}.\n";
        return true;
    }

    // Функция для парсинга Markdown
    public function parseMarkdown($markdownContent): string
    {
        return (new Parsedown())->text($markdownContent);
    }

}