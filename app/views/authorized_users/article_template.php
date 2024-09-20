<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?></title>
    <link rel="stylesheet" href="/css/profile/article_template.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Подключаем стили SimpleMDE для рендеринга Markdown -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
    <base href="http://localhost:8080/">
</head>
<body>
<header>
    <?php include __DIR__ . '/../../views/base/profile_header.php'; ?>
</header>

<main class="article-container">
    <article class="article-content">
        <!-- Заголовок статьи -->
        <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>

        <!-- Информация об авторе, дате и просмотрах -->
        <div class="article-header">
            <img src="<?php echo htmlspecialchars($author['avatar']); ?>" alt="Author Avatar" class="author-avatar">
            <div class="author-info">
                <a href="/profile/<?php echo urlencode($article['author']); ?>" class="article-author"><?php echo htmlspecialchars($article['author']); ?></a>
                <span class="article-date">Published on: <?php echo htmlspecialchars($article['date_created']); ?></span>
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var markdownElement = document.getElementById('markdown-content');

        if (markdownElement) {
            var markdownContent = markdownElement.value;

            // Создаем элемент для отображения SimpleMDE
            var renderedElement = document.getElementById('rendered-content');

            // Используем существующее textarea для SimpleMDE
            var simplemde = new SimpleMDE({
                element: markdownElement,
                autoDownloadFontAwesome: false,
                initialValue: markdownContent,
                spellChecker: false,
                toolbar: false
            });

            // Рендерим Markdown в HTML и вставляем его в контейнер
            renderedElement.innerHTML = simplemde.options.previewRender(markdownContent);
        } else {
            console.warn("No markdown content element found.");
        }
    });
</script>
</body>
</html>
