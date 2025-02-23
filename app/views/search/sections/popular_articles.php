<div id="popular-articles-content">
    <div id="filter-results" class="favorite-articles-container">
        <?php if (!empty($article_cards)): ?>
            <?php foreach ($article_cards as $article): ?>
                <div class="article">
                    <?php include __DIR__ . '/../../../views/partials/card.php'; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
