<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lisas Blog</title>
    <!-- Подключение Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/header.css" rel="stylesheet">
    <link href="css/footer.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">


    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">

</head>
<body>
<!-- Подключение к бд -->
<?php include('database/insert_article.php'); ?>
<!-- Подключение файла header.php -->
<?php include('base/header.php'); ?>

<!-- Основное содержимое сайта -->

    <div class="image-container">
        <img src="\templates\images\photo_for_display.png" alt="Image" class="hover-image">
        <div class="overlay"></div>
    </div>
<script src="\js\photo_movement.js"></script>
<script src="\js\header_movement.js"></script>


<?php include('base/footer.php'); ?>
</body>
</html>
