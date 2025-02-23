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

        // Параметры пагинации
        $paginationParams = '';
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($currentPage > 1) {
            $paginationParams = '&page=' . $currentPage;
        }

        // Перенаправление, если параметр 'section' отсутствует
        if (!isset($_GET['section'])) {
            header("Location: " . $_SERVER['REQUEST_URI'] . "&section=popular-articles" . $paginationParams);
            exit;
        }

        // AJAX-запрос на загрузку секции
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page) {
            include __DIR__ . '/../../views/' . $sections[$page];
            exit;
        }

        // Загружаем основной шаблон
        include __DIR__ . '/../../views/search/form_search.php';
    }





    public function showPopularArticles(){
//        session_start();
        // Загрузить данные для карточек в зависимости от наличия сессии
        $userId = $_SESSION['user']['user_id'] ?? null;
        if ($userId) {
            $article_cards = $this->searchModel->getArticlesByUserInterests($userId, ARTICLES_LIMIT);
        } else {
            $article_cards = $this->searchModel->getMostPopularArticles(ARTICLES_LIMIT);
        }
        require_once 'app/services/helpers/switch_language.php';
        // Передаём статьи в шаблон
        include __DIR__ . '/../../views/search/sections/popular_articles.php';
    }

    public function showPopularWriters(){
//        session_start();
        $popularWriters = $this->searchModel->getPopularWriters();
        require_once 'app/services/helpers/switch_language.php';
        // Передаём статьи в шаблон
        include __DIR__ . '/../../views/search/sections/popular_writers.php';
    }
    public function showFeed()
    {
        $user = $_SESSION['user'] ?? null;

        if ($user) {
            $user_id = $user['user_id'];
            $articlesPerPage = 2;
            $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $currentPage = max(1, $currentPage);
            $startIndex = ($currentPage - 1) * $articlesPerPage;

            $totalArticles = $this->articleModel->getTotalArticlesCountForFeed($user_id);
            $totalPages = max(1, ceil($totalArticles / $articlesPerPage));

            $articles = $this->articleModel->getArticlesForFeed($user_id, $startIndex, $articlesPerPage);

            if (!empty($articles)) {
                foreach ($articles as $key => $article) {
                    $articles[$key]['parsed_content'] = $this->markdownService->parseMarkdown($article['content']);
                }
            }
        } else {
            $articles = [];
            $totalPages = 1;
        }


        // Если не AJAX-запрос, загружаем шаблон полностью
        include __DIR__ . '/../../views/search/sections/feed.php';
    }




}