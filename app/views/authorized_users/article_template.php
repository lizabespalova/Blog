<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?></title>
    <link rel="stylesheet" href="/css/profile/article_template.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
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
        <p class="article-author"><strong>Author:</strong> <?php echo htmlspecialchars($article['author']); ?></p>

        <!-- Основной контент статьи -->
        <div class="article-text">
            <?php if (!empty($article['content'])): ?>
                <!-- Элемент для отображения Markdown-контента -->
                <textarea id="markdown-content" style="display:none;"><?php echo htmlspecialchars($article['content']); ?></textarea>
                <!-- Контейнер для рендеринга Markdown -->
                <div id="rendered-content"></div>
            <?php else: ?>
                <p>No content available for this article.</p>
            <?php endif; ?>
        </div>

        <!-- Изображение обложки -->
        <?php if ($article['cover_image']): ?>
            <div class="article-cover">
                <img src="<?php echo htmlspecialchars($article['cover_image']); ?>" alt="Cover Image">
                <p class="image-caption">This is the cover image of the article.</p>
            </div>
        <?php endif; ?>

        <!-- Видеоролик YouTube -->
        <?php if ($article['youtube_link']): ?>
            <div class="article-video">
                <iframe src="<?php echo htmlspecialchars($article['youtube_link']); ?>" frameborder="0" allowfullscreen></iframe>
                <p class="video-caption">Video related to the content of the article.</p>
            </div>
        <?php endif; ?>

        <!-- Прочие изображения статьи -->
        <?php if (!empty($article['images'])): ?>
            <div class="article-images">
                <?php foreach ($article['images'] as $image): ?>
                    <figure>
                        <img src="<?php echo htmlspecialchars($image); ?>" alt="Article Image">
                        <figcaption>Caption for the image</figcaption>
                    </figure>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </article>
</main>

<footer>
    <?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>
</footer>

<!-- Подключаем SimpleMDE JavaScript для рендеринга Markdown -->
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
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
