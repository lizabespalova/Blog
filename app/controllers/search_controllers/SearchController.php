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
        // Проверяем, авторизован ли пользователь
        $userId = $_SESSION['user']['user_id'] ?? null;
        if ($userId) {
            // Если пользователь авторизован, показываем статьи по его интересам
            $article_cards = $this->searchModel->getArticlesByUserInterests($userId, ARTICLES_LIMIT);
        } else {
            // Если пользователь не авторизован, показываем популярные статьи
             $article_cards = $this->searchModel->getMostPopularArticles(ARTICLES_LIMIT);
        }

        // Передаём статьи в шаблон
        include __DIR__ . '/../../views/search/form_search.php';
    }
}