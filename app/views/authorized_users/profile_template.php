<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/default.min.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="/css/profile/profile_template.css">
    <link rel="stylesheet" href="/css/profile/navigation.css">
    <link rel="stylesheet" href="/css/profile/content.css">
    <link rel="stylesheet" href="/css/profile/markdown.css">
    <link rel="stylesheet" href="/css/cards.css">
    <link rel="stylesheet" href="/css/repost.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="/css/settings/themes.css">
    <link rel="stylesheet" href="/css/settings/font-style.css">
    <link rel="stylesheet" href="/css/settings/font-size.css">

</head>
<body
        class="<?=
        isset($_SESSION['settings']['theme']) && $_SESSION['settings']['theme'] === 'dark' ? 'dark-mode' : '';
        ?>
    <?= isset($_SESSION['settings']['font_style']) ? htmlspecialchars($_SESSION['settings']['font_style']) : 'sans-serif'; ?>"
        style="font-size: <?= isset($_SESSION['settings']['font_size']) ? htmlspecialchars($_SESSION['settings']['font_size']) : '16' ?>px;">

<!-- Header Section -->
<?php include __DIR__ . '/../../views/base/profile_header.php'; ?>

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

        <div class="profile-container">

            <div class="profile-info" id="profile-info">
                <!-- Секция для подписчиков и подписок -->
                <div class="profile-header">
                    <h1>
                        <?= htmlspecialchars($user['user_login']) ?>
                        <?php if ($profileStatus): ?> <!-- добавлено условие для проверки видимости профиля -->
                            <?php if ($user['is_online']): ?>
                                <span class="status-indicator online" title="Online"></span>
                            <?php else: ?>
                                <span class="status-indicator offline" title="Offline"></span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </h1>

                    <div class="profile-stats">
                        <button class="stat" onclick="navigateTo('/user/<?= urlencode($user['user_id']) ?>/followers')">
                            <?= $translations['followers']; ?>: <span id="followers-count"><?= htmlspecialchars($followersCount) ?></span>
                        </button>
                        <button class="stat" onclick="navigateTo('/user/<?= urlencode($user['user_id']) ?>/followings')">
                            <?= $translations['followings']; ?>: <span id="following-count"><?= htmlspecialchars($followingCount) ?></span>
                        </button>
                    </div>
                </div>
                <p>
                    <strong><?= $translations['registered'] ?>:</strong>
                    <span id="created-display" data-full-text="<?= htmlspecialchars($user['created_at']) ?>"><?= htmlspecialchars($user['created_at']) ?></span>
                </p>
                <p>
                    <strong><?= $translations['specialization'] ?>:</strong>
                    <span id="specialisation-display" data-full-text="<?= htmlspecialchars($user['user_specialisation']) ?>"><?= htmlspecialchars($user['user_specialisation']) ?></span>
                </p>
                <p><strong><?= $translations['company'] ?>:</strong>
                    <span id="company-display" data-full-text="<?= htmlspecialchars($user['user_company']) ?>"><?= htmlspecialchars($user['user_company']) ?>
                    </span>
                </p>
                <p>
                    <strong><?= $translations['experience'] ?>:</strong>
                    <?= htmlspecialchars($user['user_experience']) ?> <?= $translations['years'] ?>
                </p>
                <p>
                    <strong><?= $translations['articles'] ?>:</strong>
                    <?= htmlspecialchars($userArticlesCount) ?>
                </p>
                <?php if ($currentUser['user_id'] === $user['user_id']): ?>
                <button class="edit-description-button" onclick="toggleEditForm()">✎</button>
                <?php else: ?>
                <?php if ($profileVisibility === 'private'): ?>
                    <!-- Для приватного профиля -->
                    <?php if ($followStatus === 'awaiting_approval'): ?>
                        <button
                                class="follow-button"
                                data-action="/cancel-follow-request/<?= htmlspecialchars($user['user_id']) ?>"
                                data-followed-user-id="<?= htmlspecialchars($user['user_id']) ?>"
                                data-private="true">
                            <?= $translations['cancel_request'] ?>
                        </button>
                    <?php elseif ($isFollowing): ?>
                        <button
                                class="follow-button"
                                data-action="/unfollow/<?= htmlspecialchars($user['user_id']) ?>"
                                data-followed-user-id="<?= htmlspecialchars($user['user_id']) ?>"
                                data-private="true">
                            <?= $translations['unfollow'] ?>
                        </button>
                    <?php else: ?>
                        <button
                                class="follow-button"
                                data-action="/follow/<?= htmlspecialchars($user['user_id']) ?>"
                                data-followed-user-id="<?= htmlspecialchars($user['user_id']) ?>"
                                data-private="true">
                            <?= $translations['follow'] ?>
                        </button>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Для публичного профиля -->
                    <?php if ($isFollowing): ?>
                        <button
                                class="follow-button"
                                data-action="/unfollow/<?= htmlspecialchars($user['user_id']) ?>"
                                data-followed-user-id="<?= htmlspecialchars($user['user_id']) ?>"
                                data-private="false">  <?= $translations['unfollow'] ?>
                        </button>
                    <?php else: ?>
                        <button
                                class="follow-button"
                                data-action="/follow/<?= htmlspecialchars($user['user_id']) ?>"
                                data-followed-user-id="<?= htmlspecialchars($user['user_id']) ?>"
                                data-private="false">  <?= $translations['follow'] ?>
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
                <?php endif; ?>
            </div>

            <div id="edit-form" style="display: none;">
                <form id="profile-edit-form">
                    <label for="specialisation"><?= $translations['specialization'] ?>:</label>
                    <input type="text" id="specialisation" name="user_specialisation" value="<?= htmlspecialchars($user['user_specialisation']) ?>" required>

                    <label for="company"><?= $translations['company'] ?>:</label>
                    <input type="text" id="company" name="user_company" value="<?= htmlspecialchars($user['user_company']) ?>" required>

                    <label for="experience"><?= $translations['experience'] ?>:</label>
                    <input type="number" id="experience" name="user_experience" value="<?= htmlspecialchars($user['user_experience']) ?>" required>

                    <!-- Модальное окно для отображения полного текста -->
                    <div id="modal" class="modal" style="display: none;">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <p id="modal-text"></p>
                        </div>
                    </div>

                    <div class="button-group">
                        <button type="button" onclick="submitEditForm()"><?= $translations['save_changes'] ?></button>
                        <button type="button" onclick="toggleEditForm()" class="cancel"><?= $translations['cancel'] ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
if ($profileVisibility === 'public' || $profileUserId === $_SESSION['user']['user_id'] || $isFollowing): ?>
<!-- Navigation Menu -->
<div class="profile-navigation">
    <div class="menu-items">
        <a href="#" class="navigation-item active" data-page="profile">
            <?= $translations['profile'] ?>
        </a>
        <a href="#" class="navigation-item" data-page="publication">
            <?= $translations['my_publications'] ?>
        </a>
        <a href="#" class="navigation-item" data-page="courses">
            <?= $translations['my_courses'] ?>
        </a>
    </div>
    <div class="menu-indicator"></div>
</div>

<!-- Content Section -->
<div class="content-section">
    <div class="content-description">
        <div class="content-text">

            <?php if (!empty($user['user_description'])): ?>
                <p><?php echo htmlspecialchars($user['user_description']); ?></p>
            <?php else: ?>
                <div class="description-container">
                    <p><?= $translations['add_description'] ?></p>
                    <div class="content-image">
                        <img src="/templates/images/woman-thinking-concept-illustration.png" alt="Profile Description Image">
                    </div>
                </div>
            <?php endif; ?>

            <!-- Reposts -->
            <div class="parent-container">
                <div class="reposts-articles-container">
                    <?php include __DIR__ . '/../../views/partials/repost.php'; ?>
                </div>
            </div>

        </div>
    </div>

    <div class="content-page hidden" id="publication-content">
        <?php if (!empty($publications)): ?>
            <!-- Publications -->
            <div class="parent-container">
                <div class="reposts-articles-container">
                    <?php include __DIR__ . '/../../views/partials/publication.php'; ?>
                </div>
            </div>
        <?php else: ?>
            <p>Here are your publications. Upload your work here!</p>
        <?php endif; ?>
    </div>
    <div class="content-page hidden" id="courses-content">
        <p>These are your courses. Start learning now!</p>
    </div>
</div>
<?php else: ?>
    <!-- Сообщение о приватности -->
    <div class="profile-restricted">
        <div class="restricted-message">
            <img src="/templates/images/locked-profile.png" alt="Locked Profile" class="restricted-icon">
            <p class="restricted-text">
                This profile is private. Subscribe to view the content.
            </p>
        </div>
    </div>
<?php endif; ?>
<!-- Footer Section -->
<?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/showdown/dist/showdown.min.js"></script>

<script src="/js/authorized_users/add_dialog_window.js"></script>
<script src="/js/authorized_users/show_edit_form.js"></script>
<script src="/js/authorized_users/files_uploads/add_avatar.js"></script>
<script src="/js/authorized_users/files_uploads/file_upload.js"></script>
<script src="/js/authorized_users/menu.js"></script>
<script src="/js/authorized_users/follow.js"></script>
<script src="/js/authorized_users/articles/repost_article.js"></script>
<script src="/js/authorized_users/turn_profile_page_parts.js"></script>
<script src="/js/async_tasks/send_notifications.js"></script>
<script src="/js/async_tasks/delete_notification.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>


</body>
</html>
