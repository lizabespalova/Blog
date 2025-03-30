
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Description</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="/css/profile/edit_description.css">
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

<main>
    <div class="edit-container">
        <h1><?= $translations['edit_description'] ?></h1>
        <form action="/update-main-description" method="POST">
            <label for="description"><?= $translations['description'] ?>:</label>
            <textarea id="description" name="description" rows="10" cols="50" maxlength="1000" placeholder="<?= $translations['enter_description_placeholder'] ?>"></textarea>
            <br>
            <span id="char-count">0/1000</span> <!-- Можно добавить перевод подсказки, если требуется -->
            <br>

            <!-- Кнопки внутри формы -->
            <div class="form-actions">
                <button type="submit" class="save-button"><?= $translations['save'] ?></button>
                <button type="button" class="back-button" onclick="window.location.href='/profile';"><?= $translations['back'] ?></button>
            </div>
        </form>
    </div>
</main>


<!-- Footer Section -->
<?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>
<script src="/js/authorized_users/menu.js"></script>
<script src="/js/authorized_users/count_symbols.js"></script>
</body>
</html>
