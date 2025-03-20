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
    <link rel="stylesheet" href="/css/courses/progress_bar.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="/css/settings/themes.css">
    <link rel="stylesheet" href="/css/settings/font-size.css">
    <link rel="stylesheet" href="/css/settings/font-style.css">
    <link rel="stylesheet" href="/css/courses/modal_form.css">
    <link rel="stylesheet" href="/css/profile/article_template.css">
    <link rel="stylesheet" href="/css/reactions_buttons.css">

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
    <div class="author-avatar-block">
        <div class="author-info">
            <img src="<?= htmlspecialchars($course['author_avatar']) ?>"
                 alt="Author Avatar"
                 class="author-avatar">
            <a href="/profile/<?= urlencode($userlogin); ?>" class="article-author">
                <?= htmlspecialchars($userlogin); ?>
            </a>
        </div>
        <!-- Кнопка шестерёнки для настроек -->
        <?php if ($userId === $course['user_id']): ?>
            <button class="settings-btn" onclick="toggleSettingsMenu(event)">
                ⚙️
            </button>
        <?php endif; ?>
        <!-- Выпадающее меню настроек -->
        <div id="settings-menu" class="settings-menu">
            <ul>
                <!-- Статистика курса с иконкой справа -->
                <li>
                    <a href="/courses/statistics/<?= urlencode((string)$course['course_id']) ?>">
                        <?= sprintf(htmlspecialchars($translations['statistics_for']), htmlspecialchars($course['title'])) ?>
                        <i class="fas fa-chart-line"></i>
                    </a>
                </li>
                <!-- Видимость курса с иконкой справа -->
                <li><a href="#"><?= $translations['course_visibility']; ?><i class="fas fa-eye"></i></a></li>

            </ul>
        </div>

    </div>


    <h1 class="course-title" style="margin: 0; padding-top: 5px;">
        <?= htmlspecialchars($course['title']) ?>
        <?php if ($userId === $course['user_id']): ?>
            <button class="edit-btn title-edit-btn">✏️</button>
        <?php endif; ?>
    </h1>
    <div class="course-header">
        <div class="course-cover-container" data-course-id="<?= $course['course_id'] ?>">
            <img src="/<?= htmlspecialchars($course['cover_image']) ?>" alt="Обложка курса" class="course-cover" id="course-cover">
            <?php if ($userId === $course['user_id']): ?>
                <button class="edit-btn cover-edit-btn" onclick="triggerCoverUpload()">✏️</button>
                <input type="file" id="cover-upload" accept="image/*" style="display: none;">
            <?php endif; ?>
        </div>



        <div class="course-description-container">
            <p class="course-description" data-course-id="<?= $course['course_id'] ?>">
                <?= nl2br(htmlspecialchars($course['description'])) ?>
                <?php if ($userId === $course['user_id']): ?>
                    <button class="edit-btn desc-edit-btn">✏️</button>
                <?php endif; ?>
            </p>
        </div>
        <!-- Модальное окно для редактирования -->
        <div id="desc-modal" class="modal">
            <div class="modal-content">
                <textarea id="desc-input"></textarea>
                <button id="save-desc"><?= $translations['save']?></button>
                <button id="cancel-desc"><?= $translations['cancel']?></button>
            </div>
        </div>
    </div>


    <!-- Контейнер для кнопок -->
    <?php if ($userId === $course['user_id']): ?>

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
    <div class="progress-container">
        <div class="course-progress">
            <label for="course-progress-bar"><?= $translations['course_progress']?>:</label>
            <progress id="course-progress-bar" value="<?= $progress ?>" max="100"></progress>
            <span><?= $progress ?>%</span>
        </div>

    </div>

    <div class="articles-wrapper">
        <div class="articles-container">
            <div class="articles">
                <?php if (!empty($articlesInCourses)): ?>
                    <?php foreach ($articlesInCourses as $article): ?>
                        <div class="article-item">
                            <label class="progress-item">
                                <!-- Проверяем, завершена ли статья -->
                                <input type="checkbox" class="progress-checkbox"
                                       data-course-id="<?= $course['course_id'] ?>"
                                       data-id="<?= $article['id'] ?>"
                                       data-video-link="<?= htmlspecialchars($article['youtube_link']) ?>"
                                    <?= in_array($article['id'], $completedArticles) ? 'checked' : ''; ?>>

                                <?= $translations['course_passed'] ?>
                            </label>
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

    <div class="course-materials-section">
        <h2><?= $translations['course_materials'] ?></h2>

        <?php if ($userId === $course['user_id']): ?>
            <div class="materials-upload-box">
                <input type="file" id="course-material-file" multiple class="file-input">
                <textarea id="material-description" placeholder="Описание материала..." class="material-textarea"></textarea>
                <button type="button" id="upload-material-btn" class="upload-btn"><?= $translations['upload'] ?></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($materials)): ?> <!-- Проверка на наличие материалов -->
        <div class="course-materials-block">
            <?php
            // Группируем материалы по описанию
            $groupedMaterials = [];
            foreach ($materials as $material) {
                $desc = $material['description'] ;
                $groupedMaterials[$desc][] = $material;
            }
            ?>

            <?php foreach ($groupedMaterials as $description => $group): ?>
                <div class="material-group">
                    <div class="material-description-toggle">
                        <div class="material-description-text">📝 <?= htmlspecialchars($description) ?></div>
                        <button class="toggle-materials-btn">📂 Show materials ⬇</button>
                    </div>

                    <div class="materials-list hidden">
                        <?php foreach ($group as $material): ?>
                            <div class="material-item">
                                <a href="/uploads/<?= $userId ?>/courses/<?= $courseId ?>/materials/<?= htmlspecialchars($material['file_name']) ?>" target="_blank" class="material-link">
                                    📄 <?= htmlspecialchars($material['original_name']) ?>
                                </a>

                                <div class="material-date"><?= date('d.m.Y H:i', strtotime($material['uploaded_at'])) ?></div>

                                <?php if ($_SESSION['user']['user_id'] == $course['user_id']): ?>
                                    <button class="delete-material-btn" data-material-id="<?= $material['material_id'] ?>">🗑️ <?= $translations['delete'] ?></button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <hr class="article-divider">
        <div class="reaction-buttons">
            <!-- Лайки для курса -->
            <button class="btn-like" data-url="/courses/react" title="Like"
                    data-user_id="<?= htmlspecialchars($course['user_id']); ?>"
                    data-course_id="<?= htmlspecialchars($course['course_id']); ?>">
                <i class="fas fa-thumbs-up"></i>
                <span class="like-count"><?= htmlspecialchars($course['likes']); ?></span>
            </button>

            <!-- Дизлайки для курса -->
            <button class="btn-dislike" data-url="/courses/react" title="Dislike"
                    data-user_id="<?= htmlspecialchars($course['user_id']); ?>"
                    data-course_id="<?= htmlspecialchars($course['course_id']); ?>">
                <i class="fas fa-thumbs-down"></i>
                <span class="dislike-count"><?= htmlspecialchars($course['dislikes']); ?></span>
            </button>
        </div>

    </div>

    <input type="hidden" id="course-id" value=<?= $course['course_id'] ?>>

</div>
<!-- Footer Section -->
<?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>

<!--<script src="/js/courses/show_courses_window.js"></script>-->
<script src="/js/courses/save-course-article.js"></script>
<script src="/js/courses/delete_course.js"></script>
<script src="/js/courses/update_course_cover.js"></script>
<script src="/js/courses/update_course_title.js"></script>
<script src="/js/courses/update_course_description.js"></script>
<script src="/js/courses/show_progress.js"></script>
<script src="/js/authorized_users/files_uploads/file_upload.js"></script>
<script src="/js/authorized_users/menu.js"></script>
<script src="/js/courses/add_scroll_buttons.js"></script>
<script src="/js/courses/scroll_video.js"></script>
<script src="/js/authorized_users/files_uploads/upload_big_files.js"></script>
<script src="/js/authorized_users/files_uploads/delete_material.js"></script>
<script src="/js/courses/reverse_material.js"></script>
<script src="/js/authorized_users/articles/articles_reactions.js"></script>
<script src="/js/courses/show_menu.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>
