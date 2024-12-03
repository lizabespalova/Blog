<div id="filter-results" class="favorite-articles-container">
    <?php if (!empty($article_cards)): ?>
        <?php foreach ($article_cards as $article): ?>
            <div class="card-container">
                <?php if (!empty($article['message'])): ?>
                    <div class="card-repost">
                        <?php if (!empty($user['user_avatar'])): ?>
                            <div class="repost-avatar">
                                <img src="<?= htmlspecialchars($user['user_avatar'], ENT_QUOTES) ?>" alt="User Avatar">
                            </div>
                        <?php endif; ?>
                        <div class="repost-message">
                            <p><?= htmlspecialchars($article['message'], ENT_QUOTES) ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-image">
                        <img src="/<?= htmlspecialchars($article['cover_image'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($article['title'], ENT_QUOTES) ?>">
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">
                            <a href="/articles/<?= htmlspecialchars($article['slug'], ENT_QUOTES) ?>">
                                <?= htmlspecialchars($article['title'], ENT_QUOTES) ?>
                            </a>
                        </h3>
                        <p class="card-meta">
                            Author: <?= htmlspecialchars($article['author'], ENT_QUOTES) ?> | Date: <?= date('d.m.Y', strtotime($article['created_at'])) ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>You don't have any favourite articles yet.</p>
    <?php endif; ?>
</div>
