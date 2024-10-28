<?php

namespace controllers\authorized_users_controllers;

use models\ArticleComments;
use models\ArticleImages;
use models\ArticleReactions;
use models\Articles;
use models\User;
use Parsedown;
use services\LoginService;
use function Symfony\Component\String\s;
require_once 'app/services/helpers/session_check.php';
class ArticleController
{
    private $articleModel;
    private $articleImagesModel;
    private $articleReactionsModel;
    private $userModel;
    private $articleCommentsModel;
    private $loginService;

    public function __construct($conn)
    {
        $this->articleModel = new Articles($conn);
        $this->articleImagesModel = new ArticleImages($conn);
        $this->loginService = new LoginService($conn);
        $this->userModel = new User($conn);
        $this->articleReactionsModel = new ArticleReactions($conn);
        $this->articleCommentsModel = new ArticleComments(getDbConnection());

    }

    public function show_article_form()
    {
        include __DIR__ . '/../../views/authorized_users/form_article.php';
    }
    public function create_article()
    {
//        session_start();
        $this->loginService->check_authorisation();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputData = $this->get_article_input();

            // Валидация данных
            $this->validate_input($inputData['title'], $inputData['content']);
            // Обрабатываем контент для поиска изображений
            $matches = $this->find_images_in_content($inputData['content']);
//            // Отладка: выводим массив с найденными изображениями
//            echo "<pre>Контент";
//            print_r($content);
//            echo "</pre>";

            // Добавляем статью в базу данных и получаем её ID
            $cover_image_path = 'templates/images/article_logo.png'; // Путь по умолчанию
            $article_id = $this->articleModel->add_article($inputData, $cover_image_path/*$title, '', $author, $cover_image_path, $youtube_link, $category, $difficulty, $read_time, $tags*/);

            if ($article_id) {
                $articleDir = $this->create_user_directory('uploads/' . $inputData['user_id'] . '/article_photos/' . $article_id);

                // Создаем директорию для статьи (и cover)
                $this->upload_cover_image($article_id, $inputData['user_id']);

                // Генерируем слаг
                $slug = $this->create_slug($inputData['author'], $article_id, $inputData['title']);
                $this->articleModel->update_article_slug($article_id, $slug);

                // Обрабатываем и сохраняем все найденные изображения
                $this->process_images($matches, $articleDir, $article_id, $inputData['content'], $slug);

                header('Location: /articles/' . $slug);

                //Добавляю +1 к статье
                $this->userModel->set_articles($inputData['user_id']);
            }else{
                header('Location: /error');
            }

            exit(); //exit, чтобы остановить выполнение скрипта после перенаправления
        }
    }
    public function process_images($matches, $articleDir, $article_id, $content, $slug){
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
                $this->articleImagesModel->save_image_path_to_db($article_id, $imagePath,$slug);
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

            // Получаем YouTube ссылку
            $youtube_link = $article['youtube_link'];
            $youtube_embed_url = !empty($youtube_link) ? $this->getYouTubeEmbedUrl($youtube_link) : null;
            //comments
           $unstructuredComments = $this->articleCommentsModel->get_comments_by_slug($article['slug']);
//            if (is_array($ustructuredComments)) {
//                echo "<pre>"; // Используем <pre> для форматирования вывода
//                var_dump($ustructuredComments);
//                echo "</pre>";
//            } else {
//                echo "No comments found or not an array.";
//            }

            $comments = $this->structure_comments(  $unstructuredComments );
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

    public function structure_comments($comments) {
        $structuredComments = [];

        // Вывод всех комментариев для отладки
        error_log("All comments: " . json_encode($comments));

        // Сначала обрабатываем корневые комментарии
        foreach ($comments as $comment) {
            if (is_null($comment['parent_id'])) {
                // Если это корневой комментарий, сохраняем его и инициализируем массив для вложенных комментариев
                $structuredComments[$comment['id']] = $comment;
                $structuredComments[$comment['id']]['replies'] = [];
            }
        }

        // Затем обрабатываем вложенные комментарии
        foreach ($comments as $comment) {
            if (!is_null($comment['parent_id'])) {
                // Если это вложенный комментарий, добавляем его в массив replies
                if (isset($structuredComments[$comment['parent_id']])) {
                    $structuredComments[$comment['parent_id']]['replies'][] = $comment;
                }
//                else {
//                    // Вывод информации для отладки, если родительский комментарий не найден
//                    error_log("Parent comment ID {$comment['parent_id']} not found for reply: " . json_encode($comment));
//                }
            }
        }

//        // Вывод структурированных комментариев для отладки
//        var_dump($structuredComments);

        return $structuredComments;
    }




    // Функция для парсинга Markdown
    public function parseMarkdown($markdownContent): string
    {
        return (new Parsedown())->text($markdownContent);
    }
// Функция для получения правильной ссылки на YouTube видео
    private function getYouTubeEmbedUrl($youtube_link)
    {
        // Извлекаем ID видео
        preg_match("/(youtu\.be\/|youtube\.com\/(watch\?(.*&)?v=|embed\/|v\/))([^\?&\"'>]+)/", $youtube_link, $matches);
        $video_id = isset($matches[4]) ? $matches[4] : null;

        // Возвращаем ссылку для встраивания
        return $video_id ? 'https://www.youtube.com/embed/' . $video_id : null;
    }

    private function get_article_input(): array
    {
        return [
            'title' => $_POST['title'],
            'content' => $_POST['content'],
            'youtube_link' => $_POST['youtube_link'],
            'category' => $_POST['category'],
            'difficulty' => $_POST['difficulty'],
            'read_time' => $_POST['read_time'],
            'tags' => $_POST['tags'],
            'author' => $_SESSION['user']['user_login'],
            'user_id' => $_SESSION['user']['user_id']
        ];
    }
    private function find_images_in_content($content): array
    {
        preg_match('/\[image\d+\]\(data:image\/(png|jpg|jpeg|gif);base64,([^"]+)\)/', $content, $matches);
        return $matches;
    }
    public function delete_article($slug){
            if ($this->articleModel->delete_article($slug)) {
//                header('Location: /articles?status=deleted');
//                exit();
                return true;
            } else {
                // Обработка ошибки
                header('Location: /error');
                exit();
            }
    }

    public function handle_reaction() {
        // Получаем данные из POST-запроса
        $input = json_decode(file_get_contents('php://input'), true);
        $this->check_data($input);

        $slug = $input['slug'];
        $reactionType = $input['reaction_type'];
        $userId = $input['user_id'];

        // Проверка существующей реакции
        $existingReaction = $this->articleReactionsModel->get_reaction($userId, $slug);

        // Отладочный вывод
        error_log('Существующая реакция: ' . print_r($existingReaction, true));

        if ($existingReaction) {
            // Если реакция уже есть
            if ($existingReaction['reaction_type'] === $reactionType) {
                // Если это та же реакция, удалить ее
                $this->articleReactionsModel->remove_reaction($userId, $slug);
                if ($reactionType === 'like') {
                    $this->articleModel->decrement_like_count($slug);
                } else {
                    $this->articleModel->decrement_dislike_count($slug);
                }
                $message = 'Reaction removed';
            } else {
                // Если реакция отличается, обновить ее
                $this->articleReactionsModel->update_reaction($userId, $slug, $reactionType);
                if ($reactionType === 'like') {
                    $this->articleModel->increment_like_count($slug);
                    $this->articleModel->decrement_dislike_count($slug);
                } else {
                    $this->articleModel->increment_dislike_count($slug);
                    $this->articleModel->decrement_like_count($slug);
                }
                $message = 'Reaction updated';
            }
        } else {
            // Если реакции нет, добавить новую
            $this->articleReactionsModel->add_reaction($userId, $slug, $reactionType);
            if ($reactionType === 'like') {
                $this->articleModel->increment_like_count($slug);
            } else {
                $this->articleModel->increment_dislike_count($slug);
            }
            $message = 'Reaction added';
        }

        // Получаем актуальное количество лайков и дизлайков
        $updated_likes = $this->articleModel->get_likes_count($slug);
        $updated_dislikes = $this->articleModel->get_dislikes_count($slug);

        // Возвращаем обновленные данные в ответе
        echo json_encode([
            'success' => true,
            'message' => $message,
            'likes' => $updated_likes,
            'dislikes' => $updated_dislikes
        ]);

        exit();
    }

    public function handle_add_comment() {
        // Получаем данные из POST-запроса
        $input = json_decode(file_get_contents('php://input'), true);

        // Валидация данных
        if (empty($input['comment_text']) || empty($input['article_slug']) || empty($input['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit();
        }

        $article_slug = $input['article_slug'];
        $user_id = $input['user_id'];
        $comment_text = $input['comment_text'];
        $parent_id = !empty($input['parent_id']) ? $input['parent_id'] : null;

        // Используем модель для сохранения комментария
        $isAdded = $this->articleCommentsModel->add_comment($article_slug, $user_id, $comment_text, $parent_id);

        if ($isAdded) {
            echo json_encode(['success' => true, 'message' => 'Comment added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add comment']);
        }
        exit();
    }

    public function get_comments()
    {
        $slug = $_GET['article_slug'] ?? '';
        if ($slug) {
            $comments = $this->articleCommentsModel->get_comments_by_slug($slug);
            $structuredComments = $this->structure_comments($comments);
            include __DIR__ . '/../../views/authorized_users/comments_template.php'; // шаблон только для комментариев
        } else {
            echo "Comments not found.";
        }
    }



    public function check_data($input){
        if (!isset($input['slug'], $input['reaction_type'], $input['user_id'])) {
            $missingParams = [];

            // Проверяем, какие параметры отсутствуют
            if (!isset($input['slug'])) {
                $missingParams[] = 'slug';
            }
            if (!isset($input['reaction_type'])) {
                $missingParams[] = 'reaction_type';
            }
            if (!isset($input['user_id'])) {
                $missingParams[] = 'user_id';
            }

            echo json_encode([
                'success' => false,
                'error' => 'Missing parameters',
                'missing' => $missingParams
            ]);
            exit();
        }
    }


}