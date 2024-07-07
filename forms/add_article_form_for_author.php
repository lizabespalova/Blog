<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавление статьи</title>
</head>
<body>
<h2>Добавление новой статьи</h2>
<form action="add_article.php" method="POST">
    <label for="title">Заголовок:</label><br>
    <input type="text" id="title" name="title" required><br><br>

    <label for="content">Содержимое:</label><br>
    <textarea id="content" name="content" rows="5" required></textarea><br><br>

    <label for="author">Автор:</label><br>
    <input type="text" id="author" name="author" required><br><br>

    <input type="submit" value="Добавить статью">
</form>
</body>
</html>
