<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($course['title']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/default.min.css">
    <link rel="stylesheet" href="/css/cards.css">
    <link rel="stylesheet" href="/css/courses/scroll_buttons.css">
    <link rel="stylesheet" href="/css/courses/courses_view.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="/css/settings/themes.css">
    <link rel="stylesheet" href="/css/settings/font-size.css">
    <link rel="stylesheet" href="/css/settings/font-style.css">
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

<div class="container">
    <h1><?= htmlspecialchars($course['title']) ?></h1>
    <img src="/<?= htmlspecialchars($course['cover_image']) ?>" alt="Обложка курса" class="course-cover">
    <p class="course-description"><?= nl2br(htmlspecialchars($course['description'])) ?></p>
    <?php if ($userId === $course['user_id']): ?>


        <!-- Контейнер для кнопок -->
        <div class="courses-buttons-container">
            <!-- Кнопка для открытия окна редактирования -->
            <button type="button" class="courses-button" id="select-articles-btn"><?= $translations['edit'] ?></button>

            <!-- Кнопка для удаления -->
            <button type="button" class="courses-button" id="delete-course-btn"><?= $translations['delete'] ?></button>
        </div>

        <!-- Скрытое поле для отправки данных -->
        <input type="hidden" name="articles" id="selected-articles">
        <!-- Скрытое поле для ID курса -->
        <input type="hidden" id="course-id" value="<?= $course['course_id'] ?>">
        <!-- Поля внутри модального окна -->
        <input type="hidden" id="modal-course-title" value="<?= $course['title'] ?>">
        <input type="hidden" id="modal-course-description" value="<?= $course['description'] ?>">
        <input type="hidden" id="modal-course-cover-image" value="<?= $course['cover_image'] ?>">


    <?php endif; ?>

    <!-- Модальное окно -->
    <?php include __DIR__ . '/../../views/courses/modal_window.php'; ?>

    <h2><?= $translations['courses_articles']?></h2>
    <div class="articles-wrapper">
        <div class="articles-container">
            <div class="articles">
                <?php if (!empty($articlesInCourses)): ?>
                    <?php foreach ($articlesInCourses as $article): ?>
                        <div class="article-item">
                            <div class="course-card">
                                <?php include __DIR__ . '/../../views/partials/card.php'; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p><?= $translations['no_articles']?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Кнопки для прокрутки -->
        <button class="scroll-btn left"><i class="fas fa-chevron-left"></i></button>
        <button class="scroll-btn right"><i class="fas fa-chevron-right"></i></button>
    </div>

    <!-- Блок с прокруткой видео -->
    <h2><?= $translations['videos'] ?></h2>
    <div class="articles-wrapper">
        <div class="articles-container">
            <div class="articles">
            <?php foreach ($articles as $article): ?>
                <?php if (!empty($article['youtube_link'])): ?>
                    <div class="video-item">
                        <iframe width="300" height="200" src="https://www.youtube.com/embed/<?= htmlspecialchars($article['youtube_link']) ?>" frameborder="0" allowfullscreen></iframe>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            </div>
        </div>

        <!-- Кнопки для прокрутки -->
        <button class="scroll-btn left"><i class="fas fa-chevron-left"></i></button>
        <button class="scroll-btn right"><i class="fas fa-chevron-right"></i></button>
    </div>

</div>
<!-- Footer Section -->
<?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>

<!--<script src="/js/courses/show_courses_window.js"></script>-->
<script src="/js/courses/save-course-article.js"></script>
<script src="/js/courses/delete_course.js"></script>
<script src="/js/authorized_users/menu.js"></script>
<script src="/js/courses/add_scroll_buttons.js"></script>
<script src="/js/courses/scroll_video.js"></script>

</body>
</html>
