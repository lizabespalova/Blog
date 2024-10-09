<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?></title>
    <link rel="stylesheet" href="/css/profile/article_template.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Подключаем стили SimpleMDE для рендеринга Markdown -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
<!--    <base href="http://localhost:8080/">-->
</head>
<body>
<header>
    <?php include __DIR__ . '/../../views/base/profile_header.php'; ?>
</header>

<main class="article-container">
    <article class="article-content">
        <!-- Заголовок статьи -->
        <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>

        <!-- Карточка с информацией об авторе, дате и просмотрах -->
        <div class="author-card">
            <img src="<?php echo htmlspecialchars($author_info['user_avatar']); ?>" alt="Author Avatar" class="author-avatar">
            <div class="author-info">
                <a href="/profile/<?php echo urlencode($article['author']); ?>" class="article-author"><?php echo htmlspecialchars($article['author']); ?></a>
                <span class="article-date">Published on: <?php echo htmlspecialchars(date("Y-m-d", strtotime($article['created_at']))); ?></span>
                <span class="article-views">Views: <?php echo htmlspecialchars($article['views']); ?></span>
            </div>
        </div>





        <!-- Основной контент статьи -->
        <div class="article-text">
            <?php if (!empty($article['content'])): ?>
                <!-- Контейнер для рендеринга Markdown -->
                <div id="rendered-content"></div>
            <?php else: ?>
                <p>No content available for this article.</p>
            <?php endif; ?>
        </div>

        <!-- Видеоролик YouTube -->
        <?php if ($article['youtube_link']): ?>
            <div class="article-video">
                <iframe src="<?php echo htmlspecialchars($article['youtube_link']); ?>" frameborder="0" allowfullscreen></iframe>
                <p class="video-caption">Video related to the content of the article.</p>
            </div>
        <?php endif; ?>

    </article>
</main>

<footer>
    <?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>
</footer>


<!-- Подключаем SimpleMDE JavaScript для рендеринга Markdown -->
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
<script src="/js/authorized_users/menu.js"></script>
<script src="/js/authorized_users/get_markdown.js"></script>
</body>
</html>
