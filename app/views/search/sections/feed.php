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
    <p><?= $translations['no_feed'] ?? 'No posts from your subscriptions' ?></p>
<?php else: ?>
    <div id="feed-content">
        <?php foreach ($articles as $article): ?>
            <div class="article">
                <img src="<?= htmlspecialchars($article['user_avatar'] ?: '/templates/images/default_avatar.png') ?>"
                     alt="<?= htmlspecialchars($article['user_login']) ?>" class="article-avatar">
                <div class="article-info">
                    <h2><?= $translations['author'] ?? 'Author: ' ?><a href="/profile/<?= htmlspecialchars($article['user_login']) ?>">
                            <?= htmlspecialchars($article['user_login']) ?></a></h2>

                    <p><a href="/articles/<?= htmlspecialchars($article['slug']) ?>">
                            <?= htmlspecialchars($article['title']) ?></a></p>

                    <div class="article-content">
                        <?= isset($article['parsed_content']) ? truncateContent($article['parsed_content']) : 'No content available' ?>
                    </div>

                    <?php if (strlen($article['parsed_content']) > 300): ?>
                        <a href="/articles/<?= htmlspecialchars($article['slug']) ?>" class="read-more">
                            <?= $translations['read_more'] ?? 'Read more' ?></a>
                    <?php endif; ?>

                    <p><small><?= $translations['date'] ?? 'Date: ' ?>
                            <?= date('d M Y, H:i', strtotime($article['created_at'])) ?></small></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Пагинация -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="?section=feed&page=<?= $currentPage - 1 ?>" class="pagination-link prev">Prev</a>
            <?php endif; ?>

            <span>Page <?= $currentPage ?> of <?= $totalPages ?></span>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?section=feed&page=<?= $currentPage + 1 ?>" class="pagination-link next">Next</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
