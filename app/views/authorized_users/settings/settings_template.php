<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/default.min.css">
    <link rel="stylesheet" href="/css/settings.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
</head>
<body>
<!-- Header Section -->
<?php include __DIR__ . '/../../../views/base/profile_header.php'; ?>

<!-- Main Section -->
<div class="settings-container">
    <aside class="settings-menu">
        <ul>
            <li><a href="?section=general" class="<?= $page === 'general' ? 'active' : '' ?>">General Settings</a></li>
            <li><a href="?section=profile" class="<?= $page === 'profile' ? 'active' : '' ?>">Profile Settings</a></li>
            <li><a href="?section=articles" class="<?= $page === 'articles' ? 'active' : '' ?>">Article Settings</a></li>
            <li><a href="?section=notifications" class="<?= $page === 'notifications' ? 'active' : '' ?>">Notifications</a></li>
            <li><a href="?section=privacy" class="<?= $page === 'privacy' ? 'active' : '' ?>">Privacy</a></li>
        </ul>
    </aside>
    <main class="settings-content">
        <h1><?= htmlspecialchars($sections[$page]) ?></h1>
        <div class="settings-section">
            <?php include "sections/{$page}.php"; ?>
        </div>
    </main>
</div>
<!-- Footer Section -->
<?php include __DIR__ . '/../../../views/base/profile_footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/showdown/dist/showdown.min.js"></script>
<script src="/js/authorized_users/menu.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>


</body>
</html>
