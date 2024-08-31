<?php
session_start(); // Запуск сессии

// Проверяем, авторизован ли пользователь
$isAuthorized = isset($_SESSION['user']['user_id']);
//var_dump($isAuthorized); // Для отладки

// Устанавливаем путь к изображению профиля и ссылку
if ($isAuthorized) {
    $profileImage = $_SESSION['user']['user_avatar'] ?? '/templates/images/login.png'; // Если у пользователя нет изображения, используется изображение по умолчанию
    $profileLink = '/profile'; // Ссылка на страницу профиля
} else {
    $profileImage = '/templates/images/login.png'; // Изображение по умолчанию
    $profileLink = '/login'; // Ссылка на страницу логина
}

//var_dump($profileImage, $profileLink); // Для отладки
?>


<!-- Задний фон с изображением -->
    <div class="header-background" style="background-image: url('/templates/images/league_of_code.png');">
        <div class="container">
            <!-- Тут можно добавить контент над фоном, если нужно -->
        </div>
    </div>
    <!-- Навигационное меню -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <!-- Кнопка для мобильной версии -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Список ссылок навигации -->
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">

                    <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Home <span class="sr-only">(текущая)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Articles</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Homeworks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Archive</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">IT news</a>
                    </li>
                        <li class="nav-item profile-item">
                            <a class="nav-link profile" href="<?php echo $profileLink; ?>">
                                <img src="<?php echo $profileImage; ?>" alt="Profile Picture">
                            </a>
                        </li>
                    </ul>
            </div>
        </div>
    </nav>
</header>
