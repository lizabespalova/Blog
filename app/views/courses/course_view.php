<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($course['title']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/default.min.css">
    <link rel="stylesheet" href="/css/cards.css">
    <link rel="stylesheet" href="/css/reactions_buttons.css">
    <link rel="stylesheet" href="/css/courses/scroll_buttons.css">
    <link rel="stylesheet" href="/css/courses/courses_view.css">
    <link rel="stylesheet" href="/css/courses/progress_bar.css">
    <link rel="stylesheet" href="/css/courses/modal_window.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="/css/settings/themes.css">
    <link rel="stylesheet" href="/css/settings/font-size.css">
    <link rel="stylesheet" href="/css/settings/font-style.css">
    <link rel="stylesheet" href="/css/courses/modal_form.css">
    <link rel="stylesheet" href="/css/profile/article_template.css">
    <link rel="stylesheet" href="/css/reactions_buttons.css">
    <link rel="stylesheet" href="/css/courses/my_courses.css">

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
    <div class="author-info">
        <img src="<?= htmlspecialchars($course['author_avatar']) ?>"
             alt="Author Avatar"
             class="author-avatar">
        <a href="/profile/<?= urlencode($userlogin); ?>" class="article-author">
            <?= htmlspecialchars($userlogin); ?>
        </a>
        <h1 class="course-title" style="margin: 0; padding-top: 5px;">
            <?= htmlspecialchars($course['title']) ?>
            <?php if ($userId === $course['user_id']): ?>
                <button class="edit-btn title-edit-btn">✏️</button>
            <?php endif; ?>
        </h1>
        <!-- Кнопка шестерёнки для настроек -->
        <?php if ($userId === $course['user_id']): ?>
            <button class="settings-btn" onclick="toggleSettingsMenu(event)">
                ⚙️
            </button>
        <?php endif; ?>
    </div>

    <div class="course-details">
        <div class="course-cover-container" data-course-id="<?= $course['course_id'] ?>">
            <img src="<?= htmlspecialchars($course['cover_image']) ?>" alt="Обложка курса" class="course-cover" id="course-cover">
            <?php if ($userId === $course['user_id']): ?>
                <button class="edit-btn cover-edit-btn" onclick="triggerCoverUpload()">✏️</button>
                <input type="file" id="cover-upload" accept="image/*" style="display: none;">
            <?php endif; ?>
        </div>

        <h2 class="course-details-title"><?= $translations['course_information']?></h2>
        <div class="details-item">
            <span class="icon">📚</span>
            <strong><?= $translations['article_amount']?>:</strong> <?= count($articlesInCourses) ?>
        </div>
        <div class="details-item">
            <span class="icon">⏳</span>
            <strong><?= $translations['course_time'] ?>:</strong><?= $course['$totalReadTime'] ?> <?= $translations['hours'] ?>
        </div>
        <div class="details-item">
            <span class="icon">📑</span>
            <strong><?= $translations['materials_count']?>:</strong> <?= $course['materials_count'] ?>
        </div>

        <div class="details-item">
            <span class="icon">📊</span>
            <strong><?= $translations['course_difficulty']?>:</strong> <?= ucfirst($course['difficulty']) ?>
        </div>

    </div>

    <div class="author-avatar-block">

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
                <li><a href="javascript:void(0);" onclick="openVisibilityModal()">
                        <?= $translations['course_visibility']; ?> <i class="fas fa-eye"></i>
                    </a>
                </li>
                <!-- Просмотр подписчиков курса с иконкой справа -->
                <li>
                    <a href="javascript:void(0);" onclick="openSubscribersModal(<?= urlencode((string)$course['course_id']) ?>)">
                        <?= $translations['view_course_subscribers']; ?> <i class="fas fa-users"></i>
                    </a>
                </li>
            </ul>
        </div>

    </div>
    <!-- Модальное окно для отображения подписчиков -->
    <div id="subscribersModal" class="modal">
        <div class="modal-content-container">
            <span class="close-btn" onclick="closeSubscriberModal()">&times;</span>
            <h2><?= $translations['followers']; ?></h2>
            <ul id="subscribersList">
                <!-- Список подписчиков будет загружен сюда -->
            </ul>
        </div>
    </div>

    <div class="course-header">
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

    <?php if (!empty($userId)): ?>
    <div class="progress-container">
        <div class="course-progress">
            <label for="course-progress-bar"><?= $translations['course_progress']?>:</label>
            <progress id="course-progress-bar" value="<?= $progress ?>" max="100"></progress>
            <span><?= $progress ?>%</span>
        </div>
    </div>
    <?php endif; ?>

    <div class="articles-wrapper">
        <div class="articles-container">
            <div class="articles">
                <?php if (!empty($articlesInCourses)): ?>
                    <?php foreach ($articlesInCourses as $article): ?>
                        <div class="article-item">
                            <?php if (!empty($userId)): ?>
                                <label class="progress-item">
                                    <!-- Чекбокс виден только для авторизованных пользователей -->
                                    <input type="checkbox" class="progress-checkbox"
                                           data-course-id="<?= $course['course_id'] ?>"
                                           data-id="<?= $article['id'] ?>"
                                           data-video-link="<?= htmlspecialchars($article['youtube_link']) ?>"
                                        <?= in_array($article['id'], $completedArticles) ? 'checked' : ''; ?>>
                                    <?= $translations['course_passed'] ?>
                                </label>
                            <?php endif; ?>
                                <?php include __DIR__ . '/../../views/partials/card.php'; ?>
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
                                <a href="<?= htmlspecialchars($material['file_name']) ?>" target="_blank" class="material-link">
                                    📄 <?= htmlspecialchars($material['original_name']) ?>
                                </a>

                                <div class="material-date"><?= date('d.m.Y H:i', strtotime($material['uploaded_at'])) ?></div>

                                <?php if ($userId == $course['user_id']): ?>
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
            <!-- Избранное-->
            <?php if (!empty($userId)): ?>
                <button class="btn-favorite <?= $is_favorite ? 'added' : '' ?>"
                    data-course-id="<?= $course['course_id'] ?>"
                    title="<?= $is_favorite ? 'Remove from Favorites' : 'Add to Favorites' ?>">
                <i class="fas fa-star"></i>
            </button>
            <?php endif; ?>

            <!-- Поделиться -->
            <?php include __DIR__ . '/../../views/partials/share_modal_menu.php'; ?>
        </div>

    </div>

    <input type="hidden" id="course-id" value=<?= $course['course_id'] ?>>
    <div id="visibilityModal" class="modal">
        <div class="modal-content-container">
            <span class="close-btn" onclick="closeVisibilityModal()">&times;</span>
            <h3><?= $translations['course_visibility']; ?></h3>

            <form id="visibilityForm">
                <label>
                    <?= $translations['visibility_public']; ?>
                    <input type="radio" name="visibility" value="public" <?= ($course['visibility'] === 'public') ? 'checked' : ''; ?>>
                </label>

                <label>
                    <?= $translations['visibility_subscribers']; ?>
                    <input type="radio" name="visibility" value="subscribers" <?= ($course['visibility'] === 'subscribers') ? 'checked' : ''; ?>>
                </label>

                <label>
                    <?= $translations['visibility_custom']; ?>
                    <input type="radio" name="visibility" value="custom" <?= ($course['visibility'] === 'custom') ? 'checked' : ''; ?>>
                </label>

                <div id="customUsersBlock" style="display: <?= ($course['visibility'] === 'custom') ? 'block' : 'none'; ?>;">
                    <input type="text" id="userSearch" placeholder="<?= $translations['search']; ?>" oninput="searchUsers(this.value)">
                    <ul id="userSearchResults"></ul>

                    <h4><?= $translations['selected_users']; ?>:</h4>
                    <ul id="selectedUsers"></ul>
                </div>

                <button type="submit"><?= $translations['save']; ?></button>
            </form>

        </div>
    </div>
    <!-- Блок рекомендаций -->
    <div class="recommendations">
        <h2><?= $translations['recommendatios']; ?></h2>
        <?php
        // Преобразуем массив $similarCourses в переменные, чтобы использовать их в шаблоне
        $courses = $similarCourses;
        include __DIR__ . '/../../views/courses/courses.php';
        ?>

    </div>

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
<script src="/js/courses/modal_window_actions.js"></script>
<script src="/js/courses/modal_subscribers.js"></script>
<script src="/js/authorized_users/articles/repost_article.js"></script>
<script src="/js/courses/course_to_favourite.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>
