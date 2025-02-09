<div id="filter-results" class="favorite-articles-container">
    <?php if (!empty($article_cards)): ?>
        <?php foreach ($article_cards as $article): ?>
            <?php include __DIR__ . '/../../../views/partials/card.php'; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
