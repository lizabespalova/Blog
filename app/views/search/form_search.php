<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="/css/search/search.css">
</head>
<body>
<!-- Header -->
<?php include __DIR__ . '/../../views/base/profile_header.php'; ?>

<!-- Search Form -->
<div class="search-section">
    <form action="/search" method="GET" class="search-form">
        <input type="text" name="query" placeholder="Type your search..." class="searching-input">
        <button type="submit" class="searching-button">
            <i class="fas fa-search"></i>
        </button>
    </form>
</div>
<!-- Menu Section -->
<div class="menu-section">
    <a href="/popular-articles" class="menu-item">Popular articles</a>
    <a href="/popular-courses" class="menu-item">Popular courses</a>
    <a href="/popular-writers" class="menu-item">Popular IT-writers</a>
    <a href="/it-events" class="menu-item">IT-events</a>
    <a href="/it-news" class="menu-item">IT news</a>
</div>
<!-- Footer -->
<?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>

<script src="/js/authorized_users/menu.js"></script>
</body>
</html>
