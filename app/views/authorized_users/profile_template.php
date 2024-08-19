<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="/css/profile/navigation.css">
    <link rel="stylesheet" href="/css/profile/content.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
</head>
<body>
<!-- Header Section -->
<?php include __DIR__ . '/../../views/authorized_users/profile_header.php'; ?>

<!-- Profile Section -->
<div class="profile-container">
    <div class="profile-header">
        <div class="profile-photo">
            <?php if (!empty($user['user_avatar'])): ?>
                <img src="<?= htmlspecialchars($user['user_avatar']) ?>" alt="Your Avatar">
            <?php else: ?>
                <img src="/templates/images/profile.jpg" alt="Default Avatar">
            <?php endif; ?>
        </div>

        <div class="profile-info">
            <h1><?= htmlspecialchars($user['user_login']) ?></h1>
            <p><strong>Specialization:</strong> <?= htmlspecialchars($user['user_specialisation']) ?></p>
            <p><strong>Company:</strong> <?= htmlspecialchars($user['user_company']) ?></p>
            <p><strong>Experience:</strong> <?= htmlspecialchars($user['user_experience']) ?> years</p>
            <p><strong>Articles:</strong> <?= htmlspecialchars($user['user_articles']) ?></p>
        </div>
    </div>
</div>
<!-- Navigation Menu -->
<div class="profile-navigation">
    <div class="menu-items">
        <a href="#" class="navigation-item active" data-page="profile">Profile</a>
        <a href="#" class="navigation-item" data-page="publication">My publications</a>
        <a href="#" class="navigation-item" data-page="video">My videos</a>
    </div>
    <div class="menu-indicator"></div>
</div>
<!-- Content Section -->
<div class="content-section">
    <div class="content-text">
        <p>For adding a description, click on the edit button above.</p>
    </div>
    <div class="content-image">
        <img src="/templates/images/woman-thinking-concept-illustration.png" alt="Profile Description Image">
    </div>
</div>
<!-- Footer Section -->
<?php include __DIR__ . '/../../views/authorized_users/profile_footer.php'; ?>


<script src="/js/add_avatar.js"></script>
<script src="/js/menu.js"></script>
</body>
</html>
