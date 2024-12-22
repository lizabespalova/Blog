<div class="repost-wrapper">
    <?php if (!empty($publications)): ?>
        <?php foreach ($publications as $article): ?>
            <?php if (!empty($article['created_at'])): ?>
                <?php include __DIR__ . '/../../views/partials/card.php'; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
