<?php

use controllers\authorized_users_controllers\ProfileController;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = $_SESSION['user'] ?? null; // Получаем данные пользователя из сессии

?>
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

            <button class="edit-button" onclick="window.location.href='/edit'">✎</button>
            <div class="menu">
                <button class="menu-toggle" onclick="toggleMenu()">☰</button>
                <div class="menu-content">
                    <a href="#my_article"> My articles <i class="fas fa-newspaper"></i></a>
                    <a href="#subscription">My subscriptions <i class="fas fa-bell"></i></a>
                    <a href="#new_article"> Write an article <i class="fas fa-pen"></i></a>
                    <a href="#favourites"> Favourites <i class="fas fa-heart"></i></a>
                    <a href="#settings"> Settings <i class="fas fa-cog"></i></a>
                    <a href="/app/controllers/authorized_users_controllers/logout.php"> Logout <i class="fas fa-sign-out-alt"></i></a>
                </div>
            </div>
        </div>
    </div>
</header>
