<div class="filter-section">
    <form id="filter-form" action="<?= htmlspecialchars($action) ?>" method="GET" class="filter-form">

        <div class="filter-group">
            <label for="title"><?= $translations['title'] ?></label>
            <input type="text" name="title" id="title" class="filter-input" placeholder="<?= $translations['filter_title_placeholder'] ?>">
        </div>

        <div class="filter-group">
            <label for="author"><?= $translations['filter_author'] ?></label>
            <input type="text" name="author" id="author" class="filter-input" placeholder="<?= $translations['filter_author_placeholder'] ?>">
        </div>

        <div class="filter-group">
            <?php include __DIR__ . '/categories.php'; ?>
        </div>

        <div class="filter-group">
            <label for="date-from"><?= $translations['filter_from_date'] ?></label>
            <input type="date" name="date_from" id="date-from" class="filter-input">
        </div>

        <div class="filter-group">
            <label for="date-to"><?= $translations['filter_to_date'] ?></label>
            <input type="date" name="date_to" id="date-to" class="filter-input">
        </div>

        <button type="submit" class="filter-button"><?= $translations['filter_apply'] ?></button>
    </form>
</div>
