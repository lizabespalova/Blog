<?php

namespace controllers\search_controllers;
use controllers\authorized_users_controllers\CourseController;
use models\Articles;
use models\Courses;
use models\Follows;
use models\Search;
use models\Settings;
use models\User;
use services\MarkdownService;

require_once 'app/services/helpers/session_check.php';
const ARTICLES_LIMIT = 10; // Количество статей, демонстрируемых на странице
class SearchController
{
    private $searchModel;
    private $articleModel;
    private $courseModel;
    private $userModel;
    private $followModel;

    private $settingModel;

    private $courseController;
    private $markdownService;


    public function __construct($dbConnection)
    {
        $this->searchModel = new Search($dbConnection);
        $this->articleModel = new Articles($dbConnection);
        $this->markdownService = new MarkdownService();
        $this->courseModel = new Courses($dbConnection);
        $this->userModel = new User($dbConnection);
        $this->followModel = new Follows($dbConnection);
        $this->courseController = new CourseController($dbConnection);
        $this->settingModel = new Settings($dbConnection);
    }

    public function show_search_form()
    {
        require_once 'app/services/helpers/switch_language.php';

        // Доступные секции
        $sections = [
            'feed' => 'search/sections/feed.php',
            'popular-articles' => 'search/sections/popular_articles.php',
            'popular-writers' => 'search/sections/popular_writers.php',
        ];

        // Определяем текущую секцию
        $page = $_GET['section'] ?? 'popular-articles';

        // Проверяем, существует ли такая секция
        if (!array_key_exists($page, $sections)) {
            $page = 'popular-articles';
        }


        // Перенаправление, если параметр 'section' отсутствует
        if (!isset($_GET['section'])) {
            header("Location: " . $_SERVER['REQUEST_URI'] . "&section=popular-articles" . $paginationParams);
            exit;
        }

        // AJAX-запрос на загрузку секции
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            include __DIR__ . '/../../views/' . $sections[$page];
            exit;
        }


        // Загружаем основной шаблон
        include __DIR__ . '/../../views/search/form_search.php';
    }

    public function search() {
        $query = $_GET['query'] ?? '';
        $type = $_GET['type'] ?? 'all'; // Тип поиска: all, articles, courses, writers

        if (empty($query)) {
            echo json_encode(["error" => "Query is empty"]);
            return;
        }

        $results = [];

        switch ($type) {
            case 'articles':
                $results['articles'] = $this->searchModel->searchArticles($query);
                if (!empty($results['articles'])) {
                    foreach ($results['articles'] as $key => $article) {
                        // Парсим контент в HTML
                        $results['articles'][$key]['parsed_content'] = $this->markdownService->parseMarkdown($article['content']);
                    }
                }
                break;
            case 'courses':
                $results['courses'] = $this->searchModel->searchCourses($query);
                break;
            case 'writers':
                $results['writers'] = $this->searchModel->searchWriters($query);
                break;
            default:
                // Поиск по всем типам, если не указан конкретный
                $results['articles'] = $this->searchModel->searchArticles($query);
                $results['courses'] = $this->searchModel->searchCourses($query);
                $results['writers'] = $this->searchModel->searchWriters($query);
                break;
        }

        header('Content-Type: application/json');
        echo json_encode($results);
    }



    public function showPopularArticles() {
        require_once 'app/services/helpers/switch_language.php';

        $offset = $_GET['offset'] ?? 0;  // Получаем смещение из параметров запроса (по умолчанию 0)
        $userId = $_SESSION['user']['user_id'] ?? null;


        if ($userId) {
            $articles = $this->searchModel->getArticlesByUserInterests($userId, 3, $offset);  // Загружаем 2 статьи за раз
        } else {
            $articles = $this->searchModel->getMostPopularArticles(3, $offset);  // Загружаем 2 статьи за раз
        }
        if (!empty($articles)) {
            foreach ($articles as $key => $article) {
                $articles[$key]['parsed_content'] = $this->markdownService->parseMarkdown($article['content']);
            }
        }
        // Передаем статьи в шаблон
        include __DIR__ . '/../../views/search/sections/feed.php';

//        include __DIR__ . '/../../views/search/sections/popular_articles.php';
    }



    public function showPopularCourses() {
        // Подключаем вспомогательные файлы для смены языка
        require_once 'app/services/helpers/switch_language.php';
        // Получаем смещение из параметров запроса (по умолчанию 0)
        $offset = $_GET['offset'] ?? 0;
        $userId = $_SESSION['user']['user_id'] ?? null;

        // Получаем популярные курсы
        $courses = $this->searchModel->getPopularCourses(3, $offset);


        // Для каждого курса получаем email и настройки скрытия email
        foreach ($courses as &$course) { // Добавили &
            $course['email'] = $this->userModel->getUserEmail($course['user_id']);
            $course['hideEmail'] = $this->settingModel->getHideEmail($course['user_id']);
            if ($course['visibility_type'] === 'subscribers') {
                $course['isSubscriber'] =  $this->followModel->isFollowing($userId, $course['user_id']);
            } else {
                $course['isSubscriber'] = true; // Если курс не только для подписчиков, разрешаем доступ
            }
            // Получаем рейтинг курса (средний рейтинг)
            $course['rating'] =$this->courseModel->getCourseRating($course['course_id']);
            $course['owner'] =$this->userModel->getLoginById($course['user_id']);

//            var_dump( $course['email']);
//            var_dump( $course['hideEmail']);
//            var_dump( $course['visibility_type']);

        }
        unset($course); // Разрываем ссылку после использования

        // Передаем курсы в шаблон для отображения
        include __DIR__ . '/../../views/courses/courses.php';
    }

    public function showPopularWriters() {
        $limit = 4; // Количество авторов на странице
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        $popularWriters = $this->searchModel->getPopularWriters($limit, $offset);
        require_once 'app/services/helpers/switch_language.php';
        // Передаём авторов в шаблон
        include __DIR__ . '/../../views/search/sections/popular_writers.php';
    }


    public function showFeed() {
        $user = $_SESSION['user'] ?? null;

        if ($user) {
            $user_id = $user['user_id'];
            $articlesPerPage = 2; // Количество статей на одной загрузке
            $offset = $_GET['offset'] ?? 0; // Получаем смещение для загрузки новых статей
//            var_dump($offset);
            // Загружаем статьи с учетом смещения
            $articles = $this->articleModel->getArticlesForFeed($user_id, $offset, $articlesPerPage);

            if (!empty($articles)) {
                foreach ($articles as $key => $article) {
                    $articles[$key]['parsed_content'] = $this->markdownService->parseMarkdown($article['content']);
                }
            }
        } else {
            $articles = [];
        }

        // Передаем данные в шаблон
        include __DIR__ . '/../../views/search/sections/feed.php';
    }

    public function showArticlesFilteredByTags(){
        $user = $_SESSION['user'] ?? null;

        $tag = $_GET['tag'] ?? '';
        $articles = $this->articleModel->getArticlesFilteredByTags($tag);
        if (!empty($articles)) {
            foreach ($articles as $key => $article) {
                $articles[$key]['parsed_content'] = $this->markdownService->parseMarkdown($article['content']);
            }
        }
//        var_dump(count($articles)); // Выведет количество статей

        include __DIR__ . '/../../views/search/sections/feed.php';
    }

    public function showPopularAiArticles(){
        $articles = $this->articleModel->showPopularAiArticles();
        if (!empty($articles)) {
            foreach ($articles as $key => $article) {
                $articles[$key]['parsed_content'] = $this->markdownService->parseMarkdown($article['content']);
            }
        }
        include __DIR__ . '/../../views/search/sections/feed.php';
    }
    public function showPopularItNewsArticles(){
        $articles = $this->articleModel->showPopularItNewsArticles();
        if (!empty($articles)) {
            foreach ($articles as $key => $article) {
                $articles[$key]['parsed_content'] = $this->markdownService->parseMarkdown($article['content']);
            }
        }
        include __DIR__ . '/../../views/search/sections/feed.php';
    }

    public function showPopularWebDevelopmentArticles(){
        $articles = $this->articleModel->showPopularWebDevelopmentArticles();
        if (!empty($articles)) {
            foreach ($articles as $key => $article) {
                $articles[$key]['parsed_content'] = $this->markdownService->parseMarkdown($article['content']);
            }
        }
        include __DIR__ . '/../../views/search/sections/feed.php';
    }
    public function showPopularCyberSecurityArticles(){
        $articles = $this->articleModel->showPopularCyberSecurityArticles();
        if (!empty($articles)) {
            foreach ($articles as $key => $article) {
                $articles[$key]['parsed_content'] = $this->markdownService->parseMarkdown($article['content']);
            }
        }
        include __DIR__ . '/../../views/search/sections/feed.php';
    }

}