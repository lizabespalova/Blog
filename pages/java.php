<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Java Page</title>
</head>
<body>
<h1>Welcome to the Java Page</h1>
<div id="viewsInfo">Views: N/A</div>
<script>
    // Функция для получения количества просмотров по ссылке
    function fetchViewsCount(link) {
        console.log(`Fetching views for link: ${link}`); // Логирование пути

        fetch(`/api/views.php?link=${encodeURIComponent(link)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log(`Response data: ${JSON.stringify(data)}`); // Логирование ответа
                const viewsInfo = document.getElementById('viewsInfo');
                viewsInfo.textContent = `Views: ${data.views} 👁️`;
                console.log(`Views updated for link: ${link}`); // Логирование успешного обновления
            })
            .catch(error => {
                console.error('Error fetching views count:', error);
                const viewsInfo = document.getElementById('viewsInfo');
                viewsInfo.textContent = `Views: N/A`;
            });
    }

    // Получение относительного пути
    function getRelativePath(url) {
        const parsedUrl = new URL(url);
        return parsedUrl.pathname; // Возвращает только путь, например, "/pages/java.php"
    }

    // Вызов функции для получения количества просмотров для текущей страницы
    const path = getRelativePath(window.location.href);
    fetchViewsCount(path);
</script>
</body>
</html>
