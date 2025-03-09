<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $translations['my_courses']?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/default.min.css">
    <link rel="stylesheet" href="/css/cards.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="/css/cover_image_preview.css">
    <link rel="stylesheet" href="/css/settings/themes.css">
    <link rel="stylesheet" href="/css/settings/font-size.css">
    <link rel="stylesheet" href="/css/settings/font-style.css">
    <link rel="stylesheet" href="/css/courses/courses_form.css">

    <link rel="stylesheet" href="/css/courses/modal_form.css">

</head>
<body
        class="<?=
        isset($_SESSION['settings']['theme']) && $_SESSION['settings']['theme'] === 'dark' ? 'dark-mode' : '';
        ?>
    <?= isset($_SESSION['settings']['font_style']) ? htmlspecialchars($_SESSION['settings']['font_style']) : 'sans-serif'; ?>"
        style="font-size: <?= isset($_SESSION['settings']['font_size']) ? htmlspecialchars($_SESSION['settings']['font_size']) : '16' ?>px;">

<!-- Header Section -->
<?php include __DIR__ . '/../../views/base/profile_header.php'; ?>

<main class="courses-container">
    <h1><?= $translations['create_course']?></h1>

    <section class="course-form">
        <h2><?= $translations['create_course'] ?></h2>
        <form id="create-course-form" action="/create-course" method="POST" enctype="multipart/form-data">
            <label for="course-title"><?= $translations['course_title']?></label>
            <input type="text" id="course-title" name="course_title" maxlength="100" required>

            <label for="course-description"><?= $translations['course_description']?></label>
            <textarea id="course-description" name="course_description" rows="4" maxlength="1000" required></textarea>

            <!-- Кнопка для открытия окна -->
            <button type="button" class="courses-button" id="select-articles-btn"><?= $translations['select_articles_button'] ?></button>
            <span id="selected-articles-count">0 <?= $translations['articles_selected'] ?></span>

            <!-- Скрытое поле для отправки данных -->
            <input type="hidden" name="articles" id="selected-articles">

            <!-- Модальное окно -->
            <?php include __DIR__ . '/../../views/courses/modal_window.php'; ?>

            <!-- Загрузка обложки -->
            <label for="cover_image"><?= $translations['cover_image'] ?></label>
            <div class="image-preview-container">
                <input type="file" id="cover_image" name="cover_image" accept="image/*">
                <img id="cover_image_preview" class="cover-image-preview">
                <button id="remove_button" class="remove-button" style="display: <?= !empty($article['cover_image']) ? 'block' : 'none' ?>;">×</button>
            </div>

            <button type="submit" id="submit-course-btn" class="courses-button"><?= $translations['create_course_button']?></button>
        </form>
    </section>
</main>
<!-- Передаем логин пользователя через data-атрибут -->
<div id="user-info" data-login="<?php echo htmlspecialchars($_SESSION['user']['user_login'], ENT_QUOTES, 'UTF-8'); ?>"></div>

<!-- Footer Section -->
<?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>

<script src="/js/courses/show_courses_window.js"></script>
<script src="/js/courses/create-course.js"></script>
<script src="/js/authorized_users/files_uploads/file_upload.js"></script>
<script src="/js/authorized_users/files_uploads/show_preview.js"></script>

<script src="/js/authorized_users/menu.js"></script>
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
</body>
</html>
