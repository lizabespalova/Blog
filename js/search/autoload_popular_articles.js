let isLoading = false;  // Флаг для предотвращения повторных запросов

function loadMorePopularArticles() {
    if (getSearchType() !== 'articles') return;

    if (isLoading) return;  // Если уже загружается, не делаем запрос

    const offset = document.querySelectorAll('.article').length;  // Сколько уже загружено статей

    isLoading = true;  // Устанавливаем флаг, что запрос выполняется

    // Получаем новые популярные статьи с сервером, передавая смещение
    fetch(`/sections/popular-articles?offset=${offset}`)
        .then(response => response.text())
        .then(data => {
            console.log(data);  // Для отладки

            // Проверяем, есть ли данные
            if (data.trim()) {
                document.getElementById('popular-articles-content').innerHTML += data;
            } else {
                console.log("Нет новых статей для загрузки");
                window.removeEventListener('scroll', onScrollPopular); // Скрываем область загрузки
            }

            isLoading = false;  // Сбрасываем флаг после загрузки
        })
        .catch(() => {
            isLoading = false;  // Сбрасываем флаг, если произошла ошибка
        });
}

function onScrollPopular() {
    // Проверяем, достиг ли пользователь нижней части страницы
    if (window.innerHeight + window.scrollY >= document.body.scrollHeight) {
        loadMorePopularArticles();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Добавляем слушатель прокрутки для популярных статей
    window.addEventListener('scroll', onScrollPopular);
});
