<?php
//session_start();
//
//// Проверка, что данные пользователя есть в сессии
//if (isset($_SESSION['user'])) {
//    $user = $_SESSION['user']; // Получаем данные из сессии
////    print_r($user);
//} else {
//    // Если пользователь не аутентифицирован
//    header('Location: /login');
//    exit();
//}
//?>
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
</head>
<body>
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
                    <h1><?= htmlspecialchars($user['user_login']) ?></h1>
                    <div class="profile-stats">
                        <button class="stat">
                            Followers: <span id="followers-count">123</span>
                        </button>
                        <button class="stat">
                            Following: <span id="following-count">45</span>
                        </button>
                    </div>
                </div>
                <p>
                    <strong>Registered:</strong>
                    <span id="created-display" data-full-text="<?= htmlspecialchars($user['created_at']) ?>">
                        <?= htmlspecialchars($user['created_at']) ?>
                    </span>
                </p>
                <p>
                    <strong>Specialization:</strong>
                    <span id="specialisation-display" data-full-text="<?= htmlspecialchars($user['user_specialisation']) ?>">
                        <?= htmlspecialchars($user['user_specialisation']) ?>
                    </span>
                </p>
                <p>
                    <strong>Company:</strong>
                    <span id="company-display" data-full-text="<?= htmlspecialchars($user['user_company']) ?>">
                        <?= htmlspecialchars($user['user_company']) ?>
                    </span>
                </p>
                <p><strong>Experience:</strong> <?= htmlspecialchars($user['user_experience']) ?> years</p>
                <p><strong>Articles:</strong> <?= htmlspecialchars($userArticlesCount) ?></p>
                <button class="edit-description-button" onclick="toggleEditForm()">✎</button>
            </div>

            <div id="edit-form" style="display: none;">
                <form id="profile-edit-form">
                    <label for="specialisation">Specialization:</label>
                    <input type="text" id="specialisation" name="user_specialisation" value="<?= htmlspecialchars($user['user_specialisation']) ?>" required>

                    <label for="company">Company:</label>
                    <input type="text" id="company" name="user_company" value="<?= htmlspecialchars($user['user_company']) ?>" required>

                    <label for="experience">Experience:</label>
                    <input type="number" id="experience" name="user_experience" value="<?= htmlspecialchars($user['user_experience']) ?>" required>

                    <!-- Модальное окно для отображения полного текста -->
                    <div id="modal" class="modal" style="display: none;">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <p id="modal-text"></p>
                        </div>
                    </div>

                    <div class="button-group">
                        <button type="button" onclick="submitEditForm()">Save Changes</button>
                        <button type="button" onclick="toggleEditForm()" class="cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Menu -->
<div class="profile-navigation">
    <div class="menu-items">
        <a href="#" class="navigation-item active" data-page="profile">Profile</a>
        <a href="#" class="navigation-item" data-page="publication">My publications</a>
        <a href="#" class="navigation-item" data-page="video">My courses</a>
    </div>
    <div class="menu-indicator"></div>
</div>

<!-- Content Section -->
<div class="content-section">
    <div class="content-text">
        <?php if (!empty($user['user_description'])): ?>
            <p><?php echo htmlspecialchars($user['user_description']); ?></p>
        <?php else: ?>
            <p>For adding a description, click on the edit button above.</p>
        <?php endif; ?>
    </div>
    <div class="content-image">
        <img src="/templates/images/woman-thinking-concept-illustration.png" alt="Profile Description Image">
    </div>
    <div class="content-page hidden" id="publication-content">
        <p>Here are your publications. Upload your work here!</p>
    </div>
    <div class="content-page hidden" id="courses-content">
        <p>These are your courses. Start learning now!</p>
    </div>
</div>


<!-- Reposts -->
<div class="parent-container">
    <div class="reposts-articles-container">
        <?php include __DIR__ . '/../../views/partials/repost.php'; ?>
    </div>
</div>






<!-- Footer Section -->
<?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/showdown/dist/showdown.min.js"></script>

<script src="/js/authorized_users/add_dialog_window.js"></script>
<script src="/js/authorized_users/show_edit_form.js"></script>
<script src="/js/authorized_users/files_uploads/add_avatar.js"></script>
<script src="/js/authorized_users/files_uploads/file_upload.js"></script>
<script src="/js/authorized_users/menu.js"></script>
<script src="/js/authorized_users/articles/repost_article.js"></script>
<script src="/js/authorized_users/turn_profile_page_parts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>


</body>
</html>
