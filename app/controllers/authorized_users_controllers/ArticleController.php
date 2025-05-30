<?php

namespace controllers\authorized_users_controllers;

use Cloudinary\Api\Upload\UploadApi;
use models\ArticleComments;
use models\ArticleImages;
use models\ArticleReactions;
use models\Articles;
use models\Comment;
use models\Favourites;
use models\Notifications;
use models\Reposts;
use models\User;
use Parsedown;
use services\CoverImagesService;
use services\ErrorService;
use services\LoginService;
use services\MarkdownService;
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
    private $commentModel;
    private $favouriteModel;
    private $repostModel;
    private $notificationModel;
    private $markdownService;
    private $errorService;
    private $coverImagesService;

    public function __construct($conn)
    {
        $this->articleModel = new Articles($conn);
        $this->articleImagesModel = new ArticleImages($conn);
        $this->loginService = new LoginService($conn);
        $this->userModel = new User($conn);
        $this->articleReactionsModel = new ArticleReactions($conn);
        $this->articleCommentsModel = new ArticleComments(getDbConnection());
        $this->commentModel = new Comment(getDbConnection());
        $this->favouriteModel = new Favourites(getDbConnection());
        $this->repostModel = new Reposts(getDbConnection());
        $this->notificationModel = new Notifications(getDbConnection());
        $this->markdownService = new MarkdownService();
        $this->errorService = new ErrorService();
        $this->coverImagesService = new CoverImagesService();
    }

    public function show_article_form($slug)
    {
        require_once 'app/services/helpers/switch_language.php';
        $userId = $_SESSION['user']['user_id'] ?? null;

        if ($slug) {
            $article = $this->articleModel->get_article_by_slug($slug);
            // Проверка и обработка обложки
            $article['cover_image'] = $this->articleModel->get_cover_image_by_slug($slug) ?? '';
//            $baseUrl = 'http://localhost:8080/';
//            $coverImage = preg_replace('#^articles/edit/#', '', $coverImage);
//            $coverImage = getBaseUrl() ."/". ltrim($coverImage, '/');
//
//        // Обработка пробелов в пути, если они не закодированы
//            $article['cover_image'] = preg_replace('/\s+/', '%20', $coverImage);

//            var_dump($coverImage);
            $title = $article['title'] ?? '';
            $content = $article['content'] ?? '';
//            var_dump($content);
        }
        // Передаем данные в форму
        include __DIR__ . '/../../views/authorized_users/form_article.php';
    }

    public function create_article()
    {
        $this->loginService->check_authorisation();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputData = $this->get_article_input();
//            // Обрезаем лишние части в контенте, такие как (56/article_photos/18/image1741392398658.jpg)
//            $inputData['content'] = $this->remove_extra_image_info($inputData['content']);

            $this->validate_input($inputData['title'], $inputData['content']);
            $matches = $this->find_images_in_content($inputData['content']);

            // Проверка наличия `article_id`
            $articleId = $_POST['article_id'] ?? null;

            if ($articleId) {
                // Обновление статьи
                $cover_image_path = $this->coverImagesService->upload_cover_image(
                    $inputData['cover_image'],
                    'uploads/' . $inputData['user_id'] . '/article_photos/' . $articleId . '/cover'
                );

//                if (!$cover_image_path) {
//                    $cover_image_path = 'templates/images/article_logo.png';
//                }

                $result = $this->articleModel->update_article($articleId, $inputData, $cover_image_path);
            } else {
                // **Создание новой статьи (без загрузки обложки)**

                $articleId = $this->articleModel->add_article($inputData, null); // Пока без обложки

                if ($articleId) {
                    // После получения articleId загружаем обложку
                    $cover_image_path = $this->coverImagesService->upload_cover_image(
                        $inputData['cover_image'],
                        'uploads/' . $inputData['user_id'] . '/article_photos/' . $articleId . '/cover'
                    );

//                    if (!$cover_image_path) {
//                        $cover_image_path = 'templates/images/article_logo.png';
//                    }

                    // Обновляем статью с путем к обложке
                    $this->articleModel->update_article_cover($articleId, $cover_image_path);

                    // Добавить +1 к статьям пользователя
                    $this->userModel->add_one_articles_to_user($inputData['user_id']);

                    $result = true;
                } else {
                    $result = false;
                }
            }

            if ($result) {

                $articleDir = $this->coverImagesService->create_user_directory('uploads/' . $inputData['user_id'] . '/article_photos/' . $articleId);

                if ($articleId) {
                    // Если создается статья, создаем slug для новой статьи
                    $slug = $this->create_slug($inputData['author'], $articleId, $inputData['title']);
                    $this->articleModel->update_article_slug($articleId, $slug);

                } else {
                    // Если обновляется статья, используем уже существующий slug
                    $slug = $inputData['slug'];
                }
                $this->process_images( $articleDir, $articleId, $inputData['content'], $slug, $inputData['user_id'], $matches);

                header('Location: /articles/' . $slug);
                exit();
            } else {
                header('Location: /error');
                exit();
            }
        }
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
//    private function create_user_directory($userDir)
//    {
//        if (!file_exists($userDir)) {
//            mkdir($userDir, 0777, true);
//        }
//        return $userDir;
//    }
//    private function upload_cover_image($article_id, $user_id, $coverImage)
//    {
//        if (/*isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0*/$coverImage) {
//            // Создаем директорию для обложки
//            $coverDir = $this->create_user_directory('uploads/' . $user_id . '/article_photos/' . $article_id . '/cover');
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


    // Генерация пути для изображения
    private function generate_image_path($user_id, $article_id, $photoNumber) {
        $fileName = "image" . $photoNumber . ".jpg";
        return 'http://' . $_SERVER['HTTP_HOST'] . '/uploads/' . $user_id . '/article_photos/' . $article_id . '/' . $fileName;
    }

//    localhost
//    public function process_images($articleDir, $article_id, $content, $slug, $userId, $matches) {
//        foreach ($matches[2] as $index => $base64Data) {
//            $photoNumber = $matches[1][$index];
//            $imagePath = $this->generate_image_path($userId, $article_id, $photoNumber);
//
//            // Сохраняем изображение на сервере
//            if ($this->save_Base64Image($base64Data, $articleDir . '/' . basename($imagePath))) {
//                // Сохраняем путь к изображению в базе данных
//                $this->articleImagesModel->save_image_path_to_db($article_id, $imagePath, $slug);
//
//                // Заменяем base64-код на путь к изображению в контенте
//                $currentMatch = $matches[0][$index];
//                $content = str_replace($currentMatch, '[image' . $photoNumber . '](' . $imagePath . ')', $content);
//            }
//        }
//
//        // Обновляем контент статьи в базе данных
//        $this->articleModel->update_content($article_id, $content);
//
//        // Удаляем лишние изображения
//       $this->delete_unused_images($content, $slug);
//    }
    public function process_images($articleDir, $article_id, $content, $slug, $userId, $matches)
    {
        foreach ($matches[2] as $index => $base64Data) {
            $photoNumber = $matches[1][$index];

            // Сохраняем изображение в Cloudinary
            $imageUrl = $this->uploadBase64ToCloudinary($base64Data);

            if ($imageUrl) {
                // Сохраняем путь к изображению в базе данных
                $this->articleImagesModel->save_image_path_to_db($article_id, $imageUrl, $slug);

                // Заменяем base64-код на путь к изображению в контенте
                $currentMatch = $matches[0][$index];
                $content = str_replace($currentMatch, '[image' . $photoNumber . '](' . $imageUrl . ')', $content);
            }
        }

        // Обновляем контент статьи в базе данных
        $this->articleModel->update_content($article_id, $content);

        // Удаляем лишние изображения
        $this->delete_unused_images($content, $slug);
    }

    public function uploadBase64ToCloudinary($base64Data)
    {
        // Инициализируем Cloudinary (если нужно)
        initCloudinaryConfig();

        try {
            // Преобразуем в формат, который понимает Cloudinary
            $base64 = 'data:image/jpeg;base64,' . $base64Data;

            // Загружаем через SDK
            $response = (new UploadApi())->upload($base64, [
                'folder' => 'article_photos' // можешь поменять на нужную папку
            ]);

            return $response['secure_url'];
        } catch (ApiError $e) {
            echo "Cloudinary API error: " . $e->getMessage();
            return false;
        } catch (Exception $e) {
            echo "Ошибка при загрузке base64 в Cloudinary: " . $e->getMessage();
            return false;
        }
    }

    public function delete_unused_images( $content, $slug)
    {
        // Получить все изображения, связанные со статьей, из базы данных
        $existingImages = $this->articleImagesModel->get_images_by_article_slug($slug);

        // Найти пути к изображениям в контенте
        $contentImagePaths = $this->find_images_pathes_in_content($content);

        // Проверить, какие изображения в базе данных больше не используются
        foreach ($existingImages as $image) {
            if (!in_array($image['image_path'], $contentImagePaths)) {
                // Удалить файл с сервера, если он больше не используется
                if (file_exists($image['image_path'])) {
                    unlink($image['image_path']);
                }

                // Удалить запись из базы данных
                $this->articleImagesModel->delete_image($image['id']);
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
//    private function remove_extra_image_info($content)
//    {
//        return preg_replace(['/^!+/', '/\((\/uploads[^)]+)\)/'], ['', ''], $content);
//    }


    public function show_article($slug)
    {
        require_once 'app/services/helpers/switch_language.php';

        // Ищем статью по слагу
        $article = $this->articleModel->get_article_by_slug($slug);

        if ($article) {
            // Получаем данные автора (аватар)
            $author_info = $this->userModel->get_author_avatar($article['author']);
            // Парсим содержимое статьи из Markdown в HTML
            $parsedContent = $this->markdownService->parseMarkdown($article['content']);

            // Получаем YouTube ссылку
            $youtube_link = $article['youtube_link'];
            $youtube_embed_url = !empty($youtube_link) ? $this->getYouTubeEmbedUrl($youtube_link) : null;

            $comment_count = $this->articleCommentsModel->get_comments_amount($article['slug']);
            // Обработка тегов
            $tagsOutput = '-'; // Значение по умолчанию

            if (!empty($article['tags'])) {
                // Разбиваем строку на массив
                $tagsArray = explode(',', $article['tags']);
                // Преобразуем массив обратно в строку с использованием implode
                $tagsOutput = htmlspecialchars(implode(', ', $tagsArray));
            }
            // Получаем избранные статьи для пользователя
            $user = $_SESSION['user'] ?? null;
            $is_favorite = false; // По умолчанию, если неавторизован
            if ($user) {
                // Получаем избранные статьи для пользователя
                $favorites = $this->favouriteModel->getUserFavorites($user['user_id']);
                $is_favorite = in_array($article['id'], $favorites, true);
            }
            // Проверяем, находится ли текущая статья в избранных
            $viewsAmount = $this->articleModel->incrementViews($article['id']);
//            $user = $this->userModel->get_user_by_login($article['author']);

//            var_dump($is_favorite); // Проверка

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
//    localhost
//    public function save_Base64Image($base64Data, $imagePath): bool
//    {
//        // Декодируем base64-код в бинарные данные
//        $imageData = base64_decode($base64Data);
//
//        // Проверяем, удалось ли декодировать данные
//        if ($imageData === false) {
//            echo "Error: didn`t decode base64 data.\n";
//            return false;
//        }
//
//        // Сохраняем декодированные данные в файл по указанному пути
//        $result = file_put_contents($imagePath, $imageData);
//
//        // Проверяем, удалось ли сохранить файл
//        if ($result === false) {
//            echo "Error during saving in path: {$imagePath}.\n";
//            return false;
//        }
//
//        echo "Image successfully saved in path {$imagePath}.\n";
//        return true;
//    }

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
            'slug' => $_POST['slug'] ?? null,
            'is_published' => $_POST['is_published'],
            'cover_image' => $_FILES['cover_image'],
            'author' => $_SESSION['user']['user_login'],
            'user_id' => $_SESSION['user']['user_id'],

        ];
    }
    private function find_images_in_content($content): array
    {
        preg_match_all('/\[image(\d+)\]\(data:image\/[a-zA-Z]+;base64,([^\)]+)\)/', $content, $matches);
        return $matches;
    }
    private function find_images_pathes_in_content($content): array
    {
        // Регулярное выражение для извлечения путей из контента
        preg_match_all('/!\[image\d+\]\(([^)]+)\)/', $content, $matches);
        return $matches[1]; // Возвращаем массив путей
    }
    public function delete_article($slug){
            if ($this->articleModel->delete_article($slug)) {
                $inputData = $this->get_article_input();

                $this->userModel->delete_one_articles_from_user($inputData['user_id']);
//                header('Location: /articles?status=deleted');
//                exit();
                return true;
            } else {
                // Обработка ошибки
                header('Location: /error');
                exit();
            }
    }
    public function handle_reaction($entityType) {
        // Получаем данные из POST-запроса
        $data = $this->get_posted_data($entityType);
        $reactionerId = $_SESSION['user']['user_id'] ?? null;
        if ($reactionerId === null) {
            header('Content-Type: application/json');
            echo json_encode(["error" => "You cannot leave likes or dislikes because you are not authorized"]);
            exit();
        }
        $userId = $data['user_id'];
        $userLogin = $this->userModel->getLoginById($reactionerId);
        $reactionType = $data['reaction_type'];
        $entityId = $data['id'];  // ID статьи (слаг) или комментария (число)

        // Определяем соответствующую модель и счетчик
        $reactionModel = $entityType === 'article' ? $this->articleReactionsModel : $this->articleCommentsModel;
        $entityModel = $entityType === 'article' ? $this->articleModel : $this->commentModel;
        $notificationType = $entityType === 'article' ? 'article_reaction' : 'comment_reaction';

        // Проверка существующей реакции
        $existingReaction = $reactionModel->get_reaction($reactionerId, $entityId);

        // Определяем, статья или комментарий
        $isComment = is_numeric($entityId); // Проверка: число означает, что это комментарий
        $entityDescription = $isComment ? 'comment' : 'article';

        if ($existingReaction) {
            // Если реакция уже есть
            if ($existingReaction['reaction_type'] === $reactionType) {
                // Удалить реакцию, если та же
                $reactionModel->remove_reaction($reactionerId, $entityId);
                $this->update_reaction_count($entityModel, $entityId, $reactionType, 'decrement');
                $message = "Reaction removed from the $entityDescription";
            } else {
                // Обновить, если реакция отличается
                $reactionModel->update_reaction($reactionerId, $entityId, $reactionType);
                $this->update_reaction_count($entityModel, $entityId, $reactionType, 'increment');
                $this->update_reaction_count($entityModel, $entityId, $existingReaction['reaction_type'], 'decrement');
                $message = "User $userLogin changed reaction to $reactionType on the $entityDescription";

                // Отправить уведомление при обновлении
                $this->notificationModel->addNotification($userId, $reactionerId, $notificationType, $message, $entityId);

                // Если лайк на статью, то добавить как интересы пользователя
                if($entityDescription == 'article' && $reactionType == 'like'){
                    $article = $this->articleModel->get_article_by_slug($entityId);
                    $this->userModel->trackUserInterest($reactionerId, $article['category']);
                }
            }
        } else {
            // Добавить новую реакцию
            $reactionModel->add_reaction($reactionerId, $entityId, $reactionType);
            $this->update_reaction_count($entityModel, $entityId, $reactionType, 'increment');
            $message = "User $userLogin added $reactionType on the $entityDescription";

            // Отправить уведомление
            $this->notificationModel->addNotification($userId, $reactionerId, $notificationType, $message, $entityId);
            // Если лайк на статью, то добавить как интересы пользователя
            if($entityDescription == 'article' && $reactionType == 'like'){
                $article = $this->articleModel->get_article_by_slug($entityId);
                $this->userModel->trackUserInterest($reactionerId, $article['category']);
            }
        }

        // Получаем актуальное количество лайков и дизлайков
        $updated_likes = $entityModel->get_likes_count($entityId);
        $updated_dislikes = $entityModel->get_dislikes_count($entityId);

        $this->show_json($message, $updated_likes, $updated_dislikes);
        exit();
    }


    // Универсальная функция для обновления счетчиков реакции
    private function update_reaction_count($entityModel, $entityId, $reactionType, $action) {
        if ($reactionType === 'like') {
            $action === 'increment' ? $entityModel->increment_like_count($entityId) : $entityModel->decrement_like_count($entityId);
        } else {
            $action === 'increment' ? $entityModel->increment_dislike_count($entityId) : $entityModel->decrement_dislike_count($entityId);
        }
    }

    // Универсальная функция получения POST-данных для статей и комментариев
    public function get_posted_data($entityType) {
        $input = json_decode(file_get_contents('php://input'), true);
        $this->check_data($input, $entityType);
        return [
            'id' => $entityType === 'article' ? $input['slug'] : $input['comment_id'],
            'reaction_type' => $input['reaction_type'],
            'user_id' => $input['user_id']
        ];
    }

    // Проверка данных в зависимости от типа сущности
    private function check_data($input, $entityType) {
        $requiredFields = $entityType === 'article' ? ['slug', 'reaction_type', 'user_id'] : ['comment_id', 'reaction_type', 'user_id'];
        foreach ($requiredFields as $field) {
            if (!isset($input[$field])) {
                echo json_encode(['success' => false, 'error' => 'Missing parameter', 'missing' => $field]);
                exit();
            }
        }
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
        $article = $this->articleModel->get_article_by_slug($article_slug);
//        $userId = $input['user_id'];
        $reactionerId = $_SESSION['user']['user_id'] ?? null;
        if ($reactionerId === null) {
            header('Content-Type: application/json');
            echo json_encode(["error" => "You cannot leave comments because you are not authorized"]);
            exit();
        }

        $user_login = $this->userModel->getLoginById($reactionerId);
        $comment_text = $input['comment_text'];
        $parent_id = !empty($input['parent_id']) ? $input['parent_id'] : null;

        // Используем модель для сохранения комментария
        $commentId = $this->articleCommentsModel->add_comment($article_slug, $reactionerId, $comment_text, $parent_id);

        if ($commentId) {
            echo json_encode(['success' => true, 'message' => 'Comment added successfully']);
            $message = 'User '. $user_login.' added a comment';

            // Отправить уведомление при обновлении
            $this->notificationModel->addNotification($article['user_id'], $reactionerId, 'comment', $message, $commentId);
            // Если это ответ, уведомить автора родительского комментария
            if ($parent_id) {
                $parentComment = $this->articleCommentsModel->get_comment_by_id($parent_id);
                if ($parentComment) {
                    $parentAuthorId = $parentComment['user_id']; // ID автора родительского комментария
                    if ($parentAuthorId !== $reactionerId) { // Чтобы не отправлять уведомление самому себе
                        $replyMessage = "User $user_login replied to your comment.";
                        $this->notificationModel->addNotification($parentAuthorId, $reactionerId, 'comment_reply', $replyMessage, $commentId);
                    }
                }
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add comment']);
        }
        exit();
    }


    public function delete_comment() {
        header('Content-Type: application/json'); // Установите заголовок JSON

        // Получаем данные из JSON-запроса
        $input = json_decode(file_get_contents('php://input'), true);
        $commentId = $input['comment_id'] ?? null;

        if (!$commentId) {
            echo json_encode(['success' => false, 'error' => 'Comment ID is required.']);
            exit;
        }

        // Удаляем комментарий
        $result = $this->commentModel->delete_comment($commentId);

        // Проверяем результат и возвращаем ответ
        if ($result['success']) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $result['error']]);
        }
        exit;
    }

    public function get_comments()
    {
        $slug = $_GET['article_slug'] ?? '';
        if ($slug) {
            $comments = $this->articleCommentsModel->get_comments_by_slug($slug);
//            var_dump($comments);
            $structuredComments = $this->structure_comments($comments);
            $user = $_SESSION['user'] ?? null;
            if ($user) {
                $user = $this->userModel->get_user_by_login($_SESSION['user']['user_login']);
            }
            include __DIR__ . '/../../views/authorized_users/comments_template.php'; // шаблон только для комментариев
        } else {
            echo "Comments not found.";
        }
    }
    public function show_json($message,$updated_likes,$updated_dislikes){
        // Возвращаем обновленные данные в ответе
        echo json_encode([
            'success' => true,
            'message' => $message,
            'likes' => $updated_likes,
            'dislikes' => $updated_dislikes
        ]);
    }

    // Метод для обработки запроса на репост
    public function repost() {
        // Получаем данные из тела запроса
        $data = json_decode(file_get_contents('php://input'), true);

        // Проверяем, что все необходимые данные присутствуют
        if (empty($data['user_id']) || empty($data['article_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            exit();
        }
//        session_start();
        $userId = $_SESSION['user']['user_id'];
        $articleId = $data['article_id'];
        $message = $data['message'];

        // Вызываем метод модели для репоста
        $result = $this->repostModel->create_repost($userId, $articleId, $message);

        // Отправляем результат
        echo json_encode($result);
    }
    public function delete_repost(){
        if (!empty($_POST['repost_id'])) {
            $repostId = $_POST['repost_id'];

            // Удаление репоста из базы данных

            $isDeleted = $this->repostModel->deleteRepost($repostId);

            // Возвращаем JSON-ответ
            echo json_encode(['success' => $isDeleted]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
        }
    }
    public function editRepost() {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            echo json_encode(["success" => false, "error" => "You are not authorized"]);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['repost_id'], $_POST['message'])) {
            $repostId = intval($_POST['repost_id']);
            $newMessage = trim($_POST['message']);

//            if ($newMessage === "") {
//                echo json_encode(["success" => false, "error" => "Описание не может быть пустым."]);
//                exit;
//            }


            $updated = $this->repostModel->updateRepostMessage($repostId, $user['user_id'], $newMessage);

            if ($updated) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "error" => "Error"]);
            }
        }
    }
    public function getArticleStatistics($articleId){
        // Получаем данные статьи
        require_once 'app/services/helpers/switch_language.php';
        $article = $this->articleModel->getArticleById($articleId);
        if (!$article) {
            http_response_code(404);
            echo json_encode(['error' => 'Article not found']);
            return;
        }
        // Формируем статистику
        $statistics = $this->getStatistics($article);

        include __DIR__ . '/../../views/authorized_users/users_articles/statistic_template.php';
    }
    public function getStatistics($article){
            return [
                'likes' => (int)$article['likes'],
                'dislikes' => (int)$article['dislikes'],
                'views' => (int)$article['views'],
                'author' => $article['author'],
                'title' => $article['title'],
                'created_at' => $article['created_at'],
            ];
    }
    public function getArticleReactioners($slug)
    {
        // Получаем данные из модели
        $likes = $this->articleReactionsModel->getLikesBySlug($slug);
        $dislikes = $this->articleReactionsModel->getDislikesBySlug($slug);

        // Возвращаем данные в формате JSON
        header('Content-Type: application/json');
        echo json_encode([
            'likes' => $likes,
            'dislikes' => $dislikes
        ]);
    }
    public function upload_temp_image()
    {
        if (!isset($_FILES['image'])) {
            echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
            return;
        }

        initCloudinaryConfig(); // твоя функция конфигурации

        try {
            $fileTmp = $_FILES['image']['tmp_name'];
            $upload = (new UploadApi())->upload($fileTmp, [
                'folder' => 'temp_editor_uploads' // временная папка или та же, что и всегда
            ]);

            echo json_encode([
                'success' => true,
                'url' => $upload['secure_url']
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}