<div class="repost-wrapper">
    <?php if (!empty($reposts)): ?>
        <?php foreach ($reposts as $article): ?>
            <?php if (!empty($article['created_at'])): ?>
                <div class="card-repost">
                    <?php if (!empty($avatar['user_avatar'])): ?>
                        <div class="repost-avatar">
                            <img src="<?= htmlspecialchars($avatar['user_avatar'], ENT_QUOTES) ?>" alt="User Avatar">
                        </div>
                    <?php endif; ?>
                    <div class="repost-message">
                        <div class="repost-header">
                            <span class="repost-username"><?= htmlspecialchars($profileUserLogin, ENT_QUOTES) ?></span>
                            <span class="repost-time"><?= htmlspecialchars($article['repost_created_at'], ENT_QUOTES) ?></span>
                        </div>
                        <p><?= htmlspecialchars($article['message'], ENT_QUOTES) ?></p>
                    </div>
                </div>
                <?php include __DIR__ . '/../../views/partials/card.php'; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
