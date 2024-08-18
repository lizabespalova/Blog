<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/css/profile.css">
    <link rel="stylesheet" href="/css/menu.css">
</head>
<body>
<!-- Header Section -->
<header class="header">
    <div class="header-content">
        <div class="header-left">
            <div class="avatar-container" onclick="document.getElementById('avatar').click();">
                <?php if (!empty($user['user_avatar'])): ?>
                    <img src="<?= htmlspecialchars($user['user_avatar']) ?>" alt="Your Avatar">
                <?php else: ?>
                    <img src="/templates/images/profile.jpg" alt="Default Avatar">
                <?php endif; ?>
                <input type="file" name="avatar" id="avatar" onchange="uploadAvatar()">
            </div>
        </div>
        <div class="header-right">
            <div class="search-container">
                <a href="#" class="search-button">
                    <i class="fas fa-search"></i>
                </a>
            </div>

            <button class="edit-button">✎</button> <!-- Edit button -->
            <div class="menu">
                <button class="menu-toggle" onclick="toggleMenu()">☰</button>
                <div class="menu-content">
                    <a href="#my_article"> My articles <i class="fas fa-newspaper"></i></a>
                    <a href="#video"> My videos in YouTube <i class="fab fa-youtube"></i></a>
                    <a href="#new_article"> Write an article <i class="fas fa-pen"></i></a>
                    <a href="#favourites"> Favourites <i class="fas fa-heart"></i></a>
                    <a href="#settings"> Settings <i class="fas fa-cog"></i></a>
                    <a href="#logout"> Logout <i class="fas fa-sign-out-alt"></i></a>
                </div>
            </div>
        </div>
    </div>
</header>

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


<script src="/js/add_avatar.js"></script>
<script src="/js/menu.js"></script>
</body>
</html>
