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

</head>
<body>
<div class="profile-container">
    <!-- Хедер профиля -->
    <?php include __DIR__ . '/../../views/base/profile_header.php'; ?>

    <!-- Секция для подписчиков -->
    <div class="followers-section">
        <h2>Followers</h2>
        <?php if (!empty($followersCount)): ?>
            <span class="followers-count"><?= htmlspecialchars($followersCount) ?></span>
        <?php else: ?>
            <span class="followers-count">0</span>
        <?php endif; ?>
    </div>


    <!-- Search Form -->
    <?php include __DIR__ . '/../partials/search_field.php'; ?>
    <!-- Followers list -->
    <div class="followers-list">
        <?php if (!empty($followers)): ?>
            <?php foreach ($followers as $follower): ?>
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
<script src="/js/filter_follower-ings_search_field.js"></script>

</body>
</html>
