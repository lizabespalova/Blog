<!-- views/authorized_users/profile_template.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="/css/profile.css">
    <link rel="stylesheet" href="/css/menu.css">
</head>
<body>
<div class="container">
    <div class="profile-header">
        <div class="avatar-container" onclick="document.getElementById('avatar').click();">

            <?php if (!empty($user['user_avatar'])): ?>
                <img src="<?= htmlspecialchars($user['user_avatar']) ?>" alt="Your Avatar">
            <?php else: ?>
                <img src="/templates/images/profile.jpg" alt="Default Avatar">
            <?php endif; ?>
            <input type="file" name="avatar" id="avatar" onchange="uploadAvatar()">
        </div>

    </div>
</div>
<div class="menu">
    <button class="menu-toggle" onclick="toggleMenu()">☰</button>
    <div class="menu-content">
        <a href="#my_article">My articles</a>
        <a href="#video">My videos in youtube</a>
        <a href="#new_article">Write an article</a>
        <a href="#favourites">Favourites</a>
        <a href="#settings">Settings</a>
        <a href="#logout">Logout</a>
    </div>
</div>
<script src="/js/add_avatar.js"></script>
<script src="/js/menu.js"></script>
</body>
</html>
