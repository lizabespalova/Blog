<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favourites</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="/css/profile/favourite_template.css">
    <link rel="stylesheet" href="/css/cards.css">
    <link rel="stylesheet" href="/css/search/search.css">
</head>
<body>
<!-- Header Section -->
<?php include __DIR__ . '/../../../views/base/profile_header.php'; ?>

<!-- Search Form -->
<?php include __DIR__ . '/../../partials/search_field.php'; ?>


<!-- Filter Form -->

<div class="filter-section">
    <form action="/favourites/filter" method="GET" class="filter-form">
        <div class="filter-group">
            <label for="author">Author:</label>
            <input type="text" class="filter-input" placeholder="Authors name">
        </div>

        <div class="filter-group">
            <?php include __DIR__ . '/../../partials/categories.php'; ?>
        </div>

        <div class="filter-group">
            <label for="date-from">Date From:</label>
            <input type="date" name="date_from" id="date-from" class="filter-input">
        </div>

        <div class="filter-group">
            <label for="date-to">Date To:</label>
            <input type="date" name="date_to" id="date-to" class="filter-input">
        </div>

        <button type="submit" class="filter-button">Apply Filter</button>
    </form>
</div>




<!-- Основной контент -->
<div class="favorite-articles-container">
    <?php if (!empty($favorites)): ?>
        <?php foreach ($favorites as $article): ?>
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
        <?php endforeach; ?>
    <?php else: ?>
        <p>You don't have any favourite articles yet.</p>
    <?php endif; ?>
</div>



<!-- Footer Section -->
<?php include __DIR__ . '/../../../views/base/profile_footer.php'; ?>

<script src="/js/authorized_users/menu.js"></script>

</body>
</html>
