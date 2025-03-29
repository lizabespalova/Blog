<?php
// Функция для обрезки контента
function truncateContent($content, $limit = 300) {
    if (strlen($content) <= $limit) {
        return $content;
    }

    $truncated = substr($content, 0, $limit);
    $lastSpace = strrpos($truncated, ' ');

    if ($lastSpace !== false) {
        $truncated = substr($truncated, 0, $lastSpace);
    }

    return $truncated . '...';
}

// Проверяем, есть ли статьи
if (empty($articles)): ?>
<!--    <p>--><?php //= $translations['no_feed'] ?? 'No posts from your subscriptions' ?><!--</p>-->
<?php else: ?>
    <div id="feed-content">
        <?php foreach ($articles as $article): ?>
            <div class="article-feed-container"> <!-- "article" оставляем для стилей, "article-item" для JS -->
            <div class="article-feed">
                <img src="<?= htmlspecialchars($article['user_avatar'] ?: '/templates/images/profile.jpg') ?>"
                     alt="<?= htmlspecialchars($article['user_login']) ?>" class="article-avatar">
                <div class="article-info">
                    <h2><?= $translations['author'] ?? 'Author: ' ?><a href="/profile/<?= htmlspecialchars($article['user_login']) ?>">
                            <?= htmlspecialchars($article['user_login']) ?></a></h2>

                    <p><a href="/articles/<?= htmlspecialchars($article['slug']) ?>">
                            <?= htmlspecialchars($article['title']) ?></a></p>

                    <div class="article-content" id="rendered-content-<?= htmlspecialchars($article['id']) ?>">
                        <?= isset($article['parsed_content']) ? truncateContent($article['parsed_content']) : 'No content available' ?>
                    </div>

                    <?php if (strlen($article['parsed_content']) > 300): ?>
                        <a href="/articles/<?= htmlspecialchars($article['slug']) ?>" class="read-more">
                            <?= $translations['read_more'] ?? 'Read more' ?></a>
                    <?php endif; ?>
                    <!-- Блок с лайками в правом верхнем углу -->
                    <div class="article-likes">
                        <span><?= htmlspecialchars($article['likes']) ?> 👍</span>
                    </div>
                    <p><small><?= $translations['date'] ?? 'Date: ' ?>
                            <?= date('d M Y, H:i', strtotime($article['created_at'])) ?></small></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>


<?php endif; ?>
