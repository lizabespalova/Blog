<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Article</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="/css/profile/article_form.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
</head>
<body>
<!-- Проверка, что ошибки PHP выводятся на экран -->
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<!-- Header Section -->
<?php include __DIR__ . '/../../views/base/profile_header.php'; ?>

<div class="form-container">
    <h2>Create Article</h2>
    <form action="/create-article" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="content">Content:</label>
        <textarea id="content" name="content" rows="10" cols="30" required></textarea>

        <label for="author">Author:</label>
        <input type="text" id="author" name="author">

        <!-- Поле для загрузки обложки статьи -->
        <label for="cover_image">Upload Cover Image:</label>
        <input type="file" id="cover_image" name="cover_image" accept="image/jpeg, image/png">
        <div id="cover-preview-container" class="image-preview-container">
            <!-- Preview for the cover image will be displayed here -->
        </div>

        <label for="image_path">Upload Images:</label>
        <input type="file" id="image_path" name="image_path[]" accept="image/*" multiple>
        <input type="text" id="image_names" readonly placeholder="No files selected">

        <div id="image-preview-container" class="image-preview-container">
            <!-- Preview images will be displayed here -->
        </div>

        <p id="image-limit-message" class="image-limit-message"></p>

        <label for="youtube_link">YouTube Link:</label>
        <input type="text" id="youtube_link" name="youtube_link">


        <input type="submit" value="Save Article">
    </form>
</div>

<!-- Footer Section -->
<?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>
<script src="/js/authorized_users/menu.js"></script>
<script src="/js/authorized_users/add_articles_photos.js"></script>

</body>
</html>
