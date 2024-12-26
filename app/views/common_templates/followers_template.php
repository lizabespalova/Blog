<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Followers</title>
    <link rel="stylesheet" href="/css/profile/followers.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
</head>
<body>
<div class="profile-container">
    <!-- Хедер профиля -->
    <?php include __DIR__ . '/../../views/base/profile_header.php'; ?>

    <!-- Секция для подписчиков -->
    <div id="followers-section" class="section">
        <ul>
            <h2>Followers</h2>
            <?php if (!empty($followers)): ?>
                <?php foreach ($followers as $follower): ?>
                    <?php if (!empty($follower['user_login'])): ?>
                        <li><?= htmlspecialchars($follower['user_login']) ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>

<!-- Footer Section -->
<?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>
</body>
</html>
