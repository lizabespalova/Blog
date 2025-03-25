<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/css/search/search.css">
    <link rel="stylesheet" href="/css/cards.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="/css/profile/favourite_template.css">
    <link rel="stylesheet" href="/css/settings/themes.css">
    <link rel="stylesheet" href="/css/settings/font-style.css">
    <link rel="stylesheet" href="/css/settings/font-size.css">
    <link rel="stylesheet" href="/css/search/popular_writers.css">
    <link rel="stylesheet" href="/css/profile/markdown.css">
    <link rel="stylesheet" href="/css/search/feed.css">
    <link rel="stylesheet" href="/css/courses/my_courses.css">

    <!--    <link rel="stylesheet" href="/css/pagination.css">-->

</head>
<body
        class="<?=
        isset($_SESSION['settings']['theme']) && $_SESSION['settings']['theme'] === 'dark' ? 'dark-mode' : '';
        ?>
    <?= isset($_SESSION['settings']['font_style']) ? htmlspecialchars($_SESSION['settings']['font_style']) : 'sans-serif'; ?>"
        style="font-size: <?= isset($_SESSION['settings']['font_size']) ? htmlspecialchars($_SESSION['settings']['font_size']) : '16' ?>px;">

<!-- Header -->
<?php include __DIR__ . '/../../views/base/profile_header.php'; ?>

<!-- Search Form -->
<?php include __DIR__ . '/../partials/search_field.php'; ?>

<!-- Меню секций -->
<div class="menu-section">
    <a href="?section=feed" class="menu-item" data-section="feed"><?= $translations['feed'] ?></a>
    <a href="?section=popular-articles" class="menu-item" data-section="popular-articles"><?= $translations['popular_articles'] ?></a>
    <a href="?section=popular-writers" class="menu-item" data-section="popular-writers"><?= $translations['popular_writers'] ?></a>
    <a href="?section=popular-courses" class="menu-item" data-section="popular-courses"><?= $translations['popular_courses'] ?></a>

    <!-- Форма поиска по тегам -->
    <form class="tag-search-form">
        <input type="hidden" name="section" value="tag-search">
        <input type="text" name="tag" placeholder="<?= $translations['search_by_tag'] ?>" value="<?= htmlspecialchars($_GET['tag'] ?? '') ?>">
        <button type="submit"><?= $translations['search'] ?></button>
    </form>
</div>


<!-- Контейнер для контента, загружаемого через AJAX -->
<div id="content-container">
    <!-- Здесь будет загружаться секция -->
</div>



<!-- Footer -->
<?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/showdown/1.9.1/showdown.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>

<script src="/js/authorized_users/menu.js"></script>
<script src="/js/authorized_users/articles/repost_article.js"></script>
<script src="/js/filter_articles.js"></script>
<script src="/js/search/autoload_feed.js"></script>
<script src="/js/search/autoload_popular_articles.js"></script>
<script src="/js/search/autoload_writers.js"></script>
<script src="/js/search/autoload_courses.js"></script>
<script src="/js/search/load_sections.js"></script>
<script src="/js/authorized_users/articles/get_markdown.js"></script>

</body>
</html>
