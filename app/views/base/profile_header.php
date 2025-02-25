<?php
//use controllers\authorized_users_controllers\ProfileController;
//if (session_status() === PHP_SESSION_NONE) {
//    session_start();
//}
$currentUser = $_SESSION['user'] ?? null; // Получаем данные пользователя из сессии
?>
<header class="header">
    <div class="header-content">
        <div class="header-left">
            <?php if ($currentUser): ?>
            <div class="avatar-container" onclick="document.getElementById('avatar').click();">
                    <?php if (!empty($currentUser['user_avatar'])): ?>
                        <img src="<?= htmlspecialchars($currentUser['user_avatar']) ?>" alt="Your Avatar">
                    <?php else: ?>
                        <img src="/templates/images/profile.jpg" alt="Default Avatar">
                    <?php endif; ?>
                <input type="file" name="avatar" id="avatar">
            </div>
            <?php endif; ?>
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
        <?php if ($currentUser): ?>
            <button class="edit-button" onclick="window.location.href='/edit'">✎</button>
                <div class="menu header-menu">
                    <button class="menu-toggle" onclick="toggleMenu(this)">☰</button>
                    <div class="menu-content">
                        <a href="/profile/<?php echo $currentUser['user_login']; ?>">
                            <?php echo $translations['my_profile']; ?> <i class="fas fa-user"></i>
                        </a>
                        <a href="/users-articles/<?php echo $currentUser['user_login']; ?>">
                            <?php echo $translations['my_articles']; ?> <i class="fas fa-newspaper"></i>
                        </a>
                        <a href="/create-article">
                            <?php echo $translations['write_article']; ?> <i class="fas fa-pen"></i>
                        </a>
                        <a href="/favourites/<?php echo $currentUser['user_login']; ?>">
                            <?php echo $translations['favourites']; ?> <i class="fas fa-star"></i>
                        </a>
                        <a href="/my-courses/<?php echo $currentUser['user_login']; ?>">
                            <?php echo $translations['my_courses']; ?> <i class="fas fa-book"></i>
                        </a>
                        <a href="/notifications">
                            <?php echo $translations['notifications']; ?> <i class="fas fa-bell"></i>
                        </a>
                        <a href="/settings">
                            <?php echo $translations['settings']; ?> <i class="fas fa-cog"></i>
                        </a>
                        <a href="/logout">
                            <?php echo $translations['logout']; ?> <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Если не авторизован, показываем значок входа -->
            <div class="avatar-container">
                <a href="/login" title="Login">
                    <img src="/templates/images/login.png" alt="Login">
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Контейнер для всплывающих уведомлений -->
    <div id="notification-container" class="notification-container">
        <!-- Уведомления добавляются здесь через JavaScript -->
    </div>
</header>
