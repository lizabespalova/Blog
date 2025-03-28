<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Followers</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">

    <link rel="stylesheet" href="/css/about.css">

    <link rel="stylesheet" href="/css/settings/themes.css">
    <link rel="stylesheet" href="/css/settings/font-size.css">
    <link rel="stylesheet" href="/css/settings/font-style.css">

</head>
<body
    class="<?=
    isset($_SESSION['settings']['theme']) && $_SESSION['settings']['theme'] === 'dark' ? 'dark-mode' : '';
    ?>
    <?= isset($_SESSION['settings']['font_style']) ? htmlspecialchars($_SESSION['settings']['font_style']) : 'sans-serif'; ?>"
    style="font-size: <?= isset($_SESSION['settings']['font_size']) ? htmlspecialchars($_SESSION['settings']['font_size']) : '16' ?>px;">
    <!-- Хедер профиля -->
    <?php include __DIR__ . '/../../views/base/profile_header.php'; ?>
    <!-- Страница "О сайте" -->
    <div class="about-page">
        <div class="about-content">
            <h1><?= $translations['about'] ?></h1>
            <p><?= $translations['website_description'] ?></p>
            <div><?= $translations['website_goals'] ?></div>
            <div><?= $translations['website_contacts'] ?></div>

        </div>
    </div>

    <!-- Footer Section -->
    <?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>

<script src="/js/authorized_users/follow.js"></script>
<script src="/js/authorized_users/show_modal_window_to_delete_followers.js"></script>
<script src="/js/authorized_users/menu.js"></script>
<script src="/js/filter_follower-ings_search_field.js"></script>

</body>
</html>
