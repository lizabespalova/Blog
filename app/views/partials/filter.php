<div class="filter-section">
    <form id="filter-form" action="/favourites/filter" method="GET" class="filter-form">

        <div class="filter-group">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" class="filter-input" placeholder="Article title">
        </div>

        <div class="filter-group">
            <label for="author">Author:</label>
            <input type="text" name="author" id="author" class="filter-input" placeholder="Authors name">
        </div>

        <div class="filter-group">
            <?php include __DIR__ . '/categories.php'; ?>
        </div>


        <div class="filter-group">
            <label for="date-from">From(save date (approx.)):</label>
            <input type="date" name="date_from" id="date-from" class="filter-input">
        </div>

        <div class="filter-group">
            <label for="date-to">To(save date (approx.):</label>
            <input type="date" name="date_to" id="date-to" class="filter-input">
        </div>

        <button type="submit" class="filter-button">Apply Filter</button>
    </form>
</div>