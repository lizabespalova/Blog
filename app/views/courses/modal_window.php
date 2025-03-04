<!-- Модальное окно -->
<div id="articles-modal" class="modal">
    <div class="modal-content-container">
        <span class="close">&times;</span>
        <h2><?= $translations['select_articles_button'] ?></h2>
        <input type="text" id="article-search" placeholder="<?= $translations['search_articles'] ?>">

        <div id="articles-list">
            <?php foreach ($articles as $article): ?>
                <div class="article-item">
                    <label>
                        <input type="checkbox" value="<?= $article['id'] ?>">
                    </label>
                    <div class="course-card">
                        <?php include __DIR__ . '/../../views/partials/card.php'; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Кнопка для сохранения настроек -->
        <button id="save-settings-btn" class="courses-button" type="button"><?= $translations['save'] ?></button>
    </div>
</div>