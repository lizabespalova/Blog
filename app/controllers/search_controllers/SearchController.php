<?php

namespace controllers\search_controllers;
use models\Articles;
use models\Search;
use services\MarkdownService;

require_once 'app/services/helpers/session_check.php';
const ARTICLES_LIMIT = 10; // Количество статей, демонстрируемых на странице
class SearchController
{
    private $searchModel;
    private $articleModel;
    private $markdownService;


    public function __construct($dbConnection)
    {
        $this->searchModel = new Search($dbConnection);
        $this->articleModel = new Articles($dbConnection);
        $this->markdownService = new MarkdownService();

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





    public function showPopularArticles() {
        $offset = $_GET['offset'] ?? 0;  // Получаем смещение из параметров запроса (по умолчанию 0)
        $userId = $_SESSION['user']['user_id'] ?? null;
        if ($userId) {
            $article_cards = $this->searchModel->getArticlesByUserInterests($userId, 2, $offset);  // Загружаем 2 статьи за раз
        } else {
            $article_cards = $this->searchModel->getMostPopularArticles(2, $offset);  // Загружаем 2 статьи за раз
        }

        require_once 'app/services/helpers/switch_language.php';
        // Передаем статьи в шаблон
        include __DIR__ . '/../../views/search/sections/popular_articles.php';
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




}