<?php

namespace controllers\search_controllers;
use models\Search;
use Exception;
const ARTICLES_LIMIT = 10; // Количество статей, демонстрируемых на странице
class SearchController
{
    private $searchModel;


    public function __construct($dbConnection)
    {
        $this->searchModel = new Search(getDbConnection());
    }

    public function show_search_form()
    {
        session_start();
        require_once 'app/services/helpers/switch_language.php';

//        // Загрузить данные для карточек в зависимости от наличия сессии
//        $userId = $_SESSION['user']['user_id'] ?? null;
//        if ($userId) {
//            $article_cards = $this->searchModel->getArticlesByUserInterests($userId, ARTICLES_LIMIT);
//        } else {
//            $article_cards = $this->searchModel->getMostPopularArticles(ARTICLES_LIMIT);
//        }

        // Доступные секции
        $sections = [
            'popular-articles' => 'search/sections/popular_articles.php',
            'popular-writers' => 'search/sections/popular_writers.php',
        ];

        // Определение текущей секции, если она передана, если нет, то используем 'popular-articles'
        $page = $_GET['section'] ?? 'popular-articles'; // Если нет параметра 'section', то 'popular-articles'

        // Если секция существует, загружаем её
        if (!array_key_exists($page, $sections)) {
            $page = 'popular-articles'; // Фолбэк на 'popular-articles' по умолчанию
        }

        // Проверяем, есть ли параметр 'section' в URL
        if (!isset($_GET['section'])) {
            // Переадресация с добавлением 'section=popular-articles'
            header("Location: " . $_SERVER['REQUEST_URI'] . "&section=popular-articles");
            exit;
        }

        // Загружаем секцию для AJAX-запроса
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page) {
            include __DIR__ . '/../../views/' . $sections[$page];
            exit;
        }

        // Загружаем шаблон с формой поиска
        include __DIR__ . '/../../views/search/form_search.php';
    }



    public function showPopularArticles(){
        session_start();
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
        session_start();
        $popularWriters = $this->searchModel->getPopularWriters();
        require_once 'app/services/helpers/switch_language.php';
        // Передаём статьи в шаблон
        include __DIR__ . '/../../views/search/sections/popular_writers.php';
    }

}