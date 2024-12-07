<div class="repost-wrapper">
    <?php if (!empty($article_cards)): ?>
        <?php foreach ($article_cards as $article): ?>
            <?php if (!empty($article['message'])): ?>
                <div class="card-repost">
                    <?php if (!empty($user['user_avatar'])): ?>
                        <div class="repost-avatar">
                            <img src="<?= htmlspecialchars($user['user_avatar'], ENT_QUOTES) ?>" alt="User Avatar">
                        </div>
                    <?php endif; ?>
                    <div class="repost-message">
                        <div class="repost-header">
                            <span class="repost-username"><?= htmlspecialchars($article['author'], ENT_QUOTES) ?></span>
                            <span class="repost-time"><?= htmlspecialchars($article['created_at'], ENT_QUOTES) ?></span>
                        </div>
                        <p><?= htmlspecialchars($article['message'], ENT_QUOTES) ?></p>
                    </div>
                </div>
                <?php include __DIR__ . '/../../views/partials/card.php'; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
