<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Followers</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/css/profile/followers.css">
    <link rel="stylesheet" href="/css/search/search.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="/css/settings/themes.css">

    <link rel="stylesheet" href="/css/settings/font-size.css">
    <link rel="stylesheet" href="/css/settings/font-style.css">

</head>
<body
        class="<?=
        isset($_SESSION['settings']['theme']) && $_SESSION['settings']['theme'] === 'dark' ? 'dark-mode' : '';
        ?>
    <?= isset($_SESSION['settings']['font_style']) ? htmlspecialchars($_SESSION['settings']['font_style']) : 'sans-serif'; ?>"
        style="font-size: <?= isset($_SESSION['settings']['font_size']) ? htmlspecialchars($_SESSION['settings']['font_size']) : '16' ?>px;">

<div class="profile-container">
    <!-- Хедер профиля -->
    <?php include __DIR__ . '/../../views/base/profile_header.php'; ?>

    <!-- Секция для подписчиков -->
    <div class="followers-section">
        <h2> <?= $translations['followings']; ?></h2>
        <?php if (!empty($followingsCount)): ?>
            <span class="followers-count"><?= htmlspecialchars($followingsCount) ?></span>
        <?php else: ?>
            <span class="followers-count">0</span>
        <?php endif; ?>
    </div>

    <!-- Search Form -->
    <?php include __DIR__ . '/../partials/search_field.php'; ?>
    <div class="followers-list">
        <?php if (!empty($followings)): ?>
            <?php foreach ($followings as $follower): ?>
                <a href="/profile/<?= urlencode($follower['user_login']) ?>" class="follower-link">
                    <div class="follower-card">
                        <img
                                src="<?= htmlspecialchars($follower['user_avatar'] ?: '/templates/images/profile.jpg') ?>"
                                alt="Avatar"
                                class="follower-avatar">
                        <div class="follower-info">
                        <span class="follower-login">
                            <?= htmlspecialchars($follower['user_login']) ?>
                        </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<!-- Footer Section -->
<?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>

<script src="/js/authorized_users/follow.js"></script>
<script src="/js/authorized_users/menu.js"></script>
<script src="/js/filter_follower-ings_search_field.js"></script>

</body>
</html>
