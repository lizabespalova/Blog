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

        // Сохраняем параметры пагинации
        $paginationParams = '';
        if (isset($_GET['page'])) {
            $paginationParams = '&page=' . (int)$_GET['page'];
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

        // Проверка на наличие пользователя
        if ($user) {
            $user_id = $user['user_id'];

            // Количество статей на странице
            $articlesPerPage = 2;

            // Получаем текущую страницу из параметра запроса
            $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $currentPage = max(1, $currentPage); // Гарантируем, что номер страницы не меньше 1

            // Определяем начальный индекс для запроса
            $startIndex = ($currentPage - 1) * $articlesPerPage;

            // Получаем общее количество статей для подсчета общего числа страниц
            $totalArticles = $this->articleModel->getTotalArticlesCountForFeed($user_id);

            // Вычисляем общее количество страниц
            $totalPages = max(1, ceil($totalArticles / $articlesPerPage)); // Минимум 1 страница

            // Получаем статьи для текущей страницы
            $articles = $this->articleModel->getArticlesForFeed($user_id, $startIndex, $articlesPerPage);

            // Парсим контент каждой статьи через markdownService
            foreach ($articles as $key => $article) {
                $articles[$key]['parsed_content'] = $this->markdownService->parseMarkdown($article['content']);
            }
        } else {
            $articles = [];
            $totalPages = 1; // Если нет статей, хотя бы одна страница
        }

        // Проверка на AJAX-запрос
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            // Если это AJAX-запрос, то возвращаем только блок с контентом ленты
            echo $this->renderFeedContent($articles, $totalPages);
            exit; // Завершаем выполнение, не выводим весь шаблон
        }

        // Для обычного запроса рендерим полную страницу
        include __DIR__ . '/../../views/search/sections/feed.php';
    }

    /**
     * Функция для рендеринга контента ленты в виде HTML
     */
    private function renderFeedContent($articles, $totalPages)
    {
        ob_start();
        ?>
        <div id="feed-content">
            <?php if (count($articles) > 0): ?>
                <?php foreach ($articles as $article): ?>
                    <div class="article">
                        <h3><?= htmlspecialchars($article['title']) ?></h3>
                        <div class="article-content"><?= $article['parsed_content'] ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No articles found</p>
            <?php endif; ?>
        </div>

        <div class="pagination">
            <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                <a href="?page=<?= $page ?>" class="pagination-link"><?= $page ?></a>
            <?php endfor; ?>
        </div>
        <?php
        $output = ob_get_clean();
        error_log($output);  // Логируем вывод в файл
        return $output;
    }

}