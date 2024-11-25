<?php
require_once __DIR__ . '/config/routes/authorization_routes.php';
require_once __DIR__ . '/config/routes/profile_routes.php';
require_once __DIR__ . '/config/routes/article_routes.php';
require_once __DIR__ . '/config/routes/comments_routes.php';
require_once __DIR__ . '/config/routes/favourites_routes.php';
require_once __DIR__ . '/config/routes/users_articles_routes.php';


// Подключение к базе данных
$dbConnection = getDbConnection();

// Получаем URI и метод запроса
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

//// Обработка запроса по id
//if (isset($_GET['id'])) {
//    $id = intval($_GET['id']); // Преобразуем id в целое число
//
//    // Подготовка и выполнение запроса
//    $query = "SELECT * FROM articles WHERE id = ?";
//    $stmt = $dbConnection->prepare($query);
//    $stmt->bind_param("i", $id);
//    $stmt->execute();
//    $result = $stmt->get_result();
//
//    if ($result && $result->num_rows > 0) {
//        $article = $result->fetch_assoc();
//        require_once __DIR__ . '/app/views/authorized_users/article_template.php';
//
//        exit();
//    } else {
//        echo "Article not found.";
//        exit();
//    }
//}

// Запускаем маршрутизатор
authorization_route($uri, $method);
profile_route($uri, $method);

// Рендерим основную страницу
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lisas Blog</title>
    <!-- Подключение Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
    <link href="css/header.css" rel="stylesheet">
    <link href="css/footer.css" rel="stylesheet">
    <link href="css/cards.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>

<!-- Подключение файла header.php -->
<?php include('base/header.php'); ?>
<?php include('pages/main_page.php'); ?>

<!-- Основное содержимое сайта -->
<div id="mainContent"></div>
<div class="container">
    <div class="text-container">
        <h1><?php echo $text['title']; ?></h1>
        <p class="main_text">
            <?php echo $text['content']; ?>
        </p>
    </div>
    <div class="image-container">
        <img src="\templates\images\photo_for_display.png" alt="Image" class="hover-image">
        <div class="overlay"></div>
    </div>
</div>
<!-- Новый контейнер для динамических элементов -->

<div class="dynamic-elements-wrapper">
    <div class="header-container">
    <h1>Videos</h1>
    </div>
    <div id="dynamicElementsContainer" class="dynamic-elements-container">
    </div>
</div>
<script src="\js\photo_movement.js"></script>
<script src="\js\header_movement.js"></script>
<script src="\js\add_languages_cards.js"></script>

<!-- Подключение файла footer.php -->
<?php include('base/footer.php'); ?>

</body>
</html>
