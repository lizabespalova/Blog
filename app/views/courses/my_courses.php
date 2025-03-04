<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $translations['my_courses']?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/default.min.css">
    <link rel="stylesheet" href="/css/courses/my_courses.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
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

<!-- Header Section -->
<?php include __DIR__ . '/../../views/base/profile_header.php'; ?>

<div class="container">
    <h1><?= $translations['my_courses']?></h1>

    <?php if (empty($courses)): ?>
        <p><?= $translations['no_courses']?></p>
    <a href="/course-form/<?php echo $currentUser['user_login']; ?>" class="btn"><?= $translations['create_course']?></a>
    <?php else: ?>
        <?php include __DIR__ . '/../../views/courses/courses.php'; ?>
    <?php endif; ?>
</div>


<!-- Footer Section -->
<?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>


<script src="/js/authorized_users/menu.js"></script>
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
</body>
</html>
