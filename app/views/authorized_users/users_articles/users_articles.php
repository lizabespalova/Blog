<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Articles</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="/css/profile/favourite_template.css">
    <link rel="stylesheet" href="/css/cards.css">
    <link rel="stylesheet" href="/css/search/search.css">
    <link rel="stylesheet" href="/css/settings/themes.css">
    <link rel="stylesheet" href="/css/settings/font-size.css">

</head>
<body
        class="<?= isset($_SESSION['settings']['theme']) && $_SESSION['settings']['theme'] === 'dark' ? 'dark-mode' : ''; ?>"
        style="font-family: <?= isset($_SESSION['settings']['font_style']) ? htmlspecialchars($_SESSION['settings']['font_style']) : 'Arial, sans-serif'; ?>;
                font-size: <?= isset($_SESSION['settings']['font_size']) ? htmlspecialchars($_SESSION['settings']['font_size']) : '16' ?>px;">

<!-- Header Section -->
<?php include __DIR__ . '/../../../views/base/profile_header.php'; ?>

<!-- Filter Form -->
<?php
$action = "/users-articles/filter";
include __DIR__ . '/../../../views/partials/filter.php';
?>

<!-- Main Content -->
<div id="filter-results" class="favorite-articles-container">
    <?php if (!empty($article_cards)): ?>
        <?php foreach ($article_cards as $article): ?>
            <?php include __DIR__ . '/../../../views/partials/card.php'; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<!-- Footer Section -->
<?php include __DIR__ . '/../../../views/base/profile_footer.php'; ?>

<script src="/js/authorized_users/menu.js"></script>
<script src="/js/authorized_users/favourites/filter_favourites.js"></script>
<script src="/js/authorized_users/articles/repost_article.js"></script>

</body>
</html>
