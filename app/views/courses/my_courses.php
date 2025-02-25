<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои курсы</title>
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="/css/courses/courses.css">
</head>
<body
        class="<?=
        (isset($_SESSION['settings']['theme']) && $_SESSION['settings']['theme'] === 'dark' ? 'dark-mode ' : '') .
        (isset($_SESSION['settings']['font_style']) ? htmlspecialchars($_SESSION['settings']['font_style']) : 'sans-serif'); ?>"
        style="font-size: <?= isset($_SESSION['settings']['font_size']) ? htmlspecialchars($_SESSION['settings']['font_size']) : '16' ?>px;"
        data-lang="<?= isset($_SESSION['settings']['language']) ? htmlspecialchars($_SESSION['settings']['language']) : 'en'; ?>">

<!-- Header Section -->
<?php include __DIR__ . '/../../views/base/profile_header.php'; ?>

<main class="courses-container">
    <h1>Мои курсы</h1>

    <div class="courses-grid">
        <div class="course-card">
            <h2>Пример курса</h2>
            <p>Описание курса. Здесь будет информация о курсе.</p>
            <a href="#">Перейти</a>
        </div>
    </div>

    <!-- Форма создания курса -->
    <section class="course-form">
        <h2>Создать новый курс</h2>
        <form action="/create-course" method="POST">
            <label for="course-title">Название курса</label>
            <input type="text" id="course-title" name="course_title" required>

            <label for="course-description">Описание курса</label>
            <textarea id="course-description" name="course_description" rows="4" required></textarea>

            <button type="submit">Создать курс</button>
        </form>
    </section>
</main>

<!-- Footer Section -->
<?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>

</body>
</html>
