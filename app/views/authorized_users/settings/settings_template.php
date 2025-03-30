<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/default.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="/css/settings/settings.css">
    <link rel="stylesheet" href="/css/settings/themes.css">
    <link rel="stylesheet" href="/css/settings/font-size.css">
    <link rel="stylesheet" href="/css/settings/font-style.css">
    <link rel="stylesheet" href="/css/settings/sections/appearance.css">
    <link rel="stylesheet" href="/css/settings/sections/personal.css">
    <link rel="stylesheet" href="/css/settings/sections/privacy.css">
    <link rel="stylesheet" href="/css/settings/sections/security.css">
    <link rel="stylesheet" href="/css/settings/sections/integrations.css">
    <link rel="stylesheet" href="/css/settings/sections/preferences.css">
    <link rel="stylesheet" href="/css/settings/sections/credentionals_icon.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">


</head>
<body
        class="<?=
        (isset($_SESSION['settings']['theme']) && $_SESSION['settings']['theme'] === 'dark' ? 'dark-mode ' : '') .
        (isset($_SESSION['settings']['font_style']) ? htmlspecialchars($_SESSION['settings']['font_style']) : 'sans-serif'); ?>"
        style="font-size: <?= isset($_SESSION['settings']['font_size']) ? htmlspecialchars($_SESSION['settings']['font_size']) : '16' ?>px;"
        data-lang="<?= isset($_SESSION['settings']['language']) ? htmlspecialchars($_SESSION['settings']['language']) : 'en'; ?>">

<!-- Header Section -->
<?php include __DIR__ . '/../../../views/base/profile_header.php'; ?>

<!-- Main Section -->
<div class="settings-container">
    <aside class="settings-menu">
        <ul>
            <li>
                <a href="?section=appearance" class="<?= $page === 'appearance' ? 'active' : '' ?>">
                    <?= $translations['appearance_settings'] ?>
                </a>
            </li>
            <li>
                <a href="?section=personal" class="<?= $page === 'personal' ? 'active' : '' ?>">
                    <?= $translations['personal_data'] ?>
                </a>
            </li>
            <li>
                <a href="?section=privacy" class="<?= $page === 'privacy' ? 'active' : '' ?>">
                    <?= $translations['privacy_settings'] ?>
                </a>
            </li>
            <li>
                <a href="?section=security" class="<?= $page === 'security' ? 'active' : '' ?>">
                    <?= $translations['security'] ?>
                </a>
            </li>
            <li>
                <a href="?section=preferences" class="<?= $page === 'preferences' ? 'active' : '' ?>">
                    <?= $translations['preferences'] ?>
                </a>
            </li>
            <li>
                <a href="?section=delete_account" class="<?= $page === 'delete_account' ? 'active' : '' ?>">
                    <?= $translations['delete_account'] ?>
                </a>
            </li>
        </ul>


    </aside>
    <main class="settings-content">
        <h1><?= htmlspecialchars($sections[$page]) ?></h1>
        <div class="settings-section">
            <?php include "sections/{$page}.php"; ?>
        </div>
    </main>
</div>
<!-- Footer Section -->
<?php include __DIR__ . '/../../../views/base/profile_footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/showdown/dist/showdown.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="/js/authorized_users/menu.js"></script>
<script src="/js/authorized_users/settings/switch_language.js"></script>
<script src="/js/authorized_users/settings/switch_theme.js"></script>
<script src="/js/authorized_users/settings/switch_font-size.js"></script>
<script src="/js/authorized_users/settings/switch_font-style.js"></script>
<script src="/js/authorized_users/settings/switch_privacy_settings.js"></script>
<script src="/js/authorized_users/settings/change_password.js"></script>
<script src="/js/authorized_users/settings/delete_account.js"></script>
<script src="/js/authorized_users/settings/delete_session.js"></script>
<script src="/js/authorized_users/settings/set_alert_to_show_updates.js"></script>
<script src="/js/authorized_users/settings/select_country.js"></script>
<script src="/js/authorized_users/settings/set_alert_to_send_confirmation_email.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>


</body>
</html>
