// let isLoading = false;  // Флаг загрузки

function loadMorePopularArticles() {
    if (getSearchType() !== 'articles' || isLoading) return;

    isLoading = true; // Устанавливаем флаг, что запрос выполняется

    // Получаем количество уже загруженных статей
    const offset = document.querySelectorAll('.article-feed-container').length;
    console.log("Offset before request:", offset); // Проверяем значение offset перед запросом

    fetch(`/sections/popular-articles?offset=${offset}`)
        .then(response => response.text())
        .then(data => {
            console.log("Server response:", data);

            const contentContainer = document.getElementById('content-container');

            if (contentContainer) {  // Проверяем, существует ли контейнер
                if (data.trim()) {
                    // Добавляем новые статьи в контейнер
                    contentContainer.insertAdjacentHTML('beforeend', data);
                } else {
                    console.log("Нет новых статей для загрузки");
                    window.removeEventListener('scroll', onScrollPopular); // Убираем слушатель при отсутствии данных
                }
            } else {
                console.error('Контейнер с id "article-feed-container" не найден.');
            }
        })
        .finally(() => {
            isLoading = false; // Сбрасываем флаг после загрузки
        });
}

function onScrollPopular() {
    // Проверяем, достиг ли пользователь нижней части страницы
    if (window.innerHeight + window.scrollY >= document.body.scrollHeight - 100) { // Немного добавил отступ для более точной реакции
        loadMorePopularArticles();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Добавляем слушатель прокрутки для популярных статей
    window.addEventListener('scroll', onScrollPopular);
});
