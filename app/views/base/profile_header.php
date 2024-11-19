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
                <input type="file" name="avatar" id="avatar">
            </div>
        </div>
        <div class="header-right">
            <form action="/search" method="GET" class="d-inline">
                <div class="search-container">
                    <!-- Кнопка отправки формы -->
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            <button class="edit-button" onclick="window.location.href='/edit'">✎</button>
            <div class="menu header-menu">
                <button class="menu-toggle" onclick="toggleMenu(this)">☰</button>
                <div class="menu-content">
                    <a href="/profile/<?php echo $user['user_login']; ?>">My profile <i class="fas fa-user"></i></a>
                    <a href="#my_article">My articles <i class="fas fa-newspaper"></i></a>
                    <a href="#subscription">My subscriptions <i class="fas fa-bell"></i></a>
                    <a href="/create-article">Write an article <i class="fas fa-pen"></i></a>
                    <a href="/favourites/<?php echo $user['user_login']; ?>">Favorites <i class="fas fa-star"></i></a>
                    <a href="#settings">Settings <i class="fas fa-cog"></i></a>
                    <a href="/logout">Logout <i class="fas fa-sign-out-alt"></i></a>
                </div>
            </div>
        </div>
    </div>
</header>
