function loadMoreArticles() {
    // Проверяем, находимся ли мы на странице "feed"
    if (getSearchType() !== 'feed') return;

    const offset = document.querySelectorAll('.article').length; // Сколько уже загружено статей

    // Получаем новые статьи с сервером, передавая смещение
    fetch(`/sections/feed?offset=${offset}`)
        .then(response => response.text())
        .then(data => {
            console.log(data);  // Для отладки
            document.getElementById('feed-content').innerHTML += data;
            processMarkdownContent();

            if (!data.trim()) {
                // Если данных больше нет, скрываем область загрузки
                window.removeEventListener('scroll', onScroll);
            }
        });
}

function onScroll() {
    // Проверяем, достиг ли пользователь нижней части страницы
    if (window.innerHeight + window.scrollY >= document.body.scrollHeight) {
        loadMoreArticles();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Добавляем слушатель прокрутки
    window.addEventListener('scroll', onScroll);
});
