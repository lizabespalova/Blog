let Loading = false;  // Флаг для предотвращения повторных запросов

function loadMorePopularWriters() {
    if (getSearchType() !== 'writers') return;

    if (Loading) return;  // Если уже загружается, не делаем запрос
    Loading = true;  // Устанавливаем флаг, что запрос выполняется

    const offset = document.querySelectorAll('.writer-card').length; // Сколько уже загружено писателей

    // Получаем новых популярных писателей с сервером, передавая смещение
    fetch(`/sections/popular-writers?offset=${offset}`)
        .then(response => response.text())
        .then(data => {
            console.log(data);  // Для отладки

            // Если данные пришли
            if (data.trim()) {
                document.getElementById('writers-grid').innerHTML += data;
            } else {
                // Если данных больше нет, скрываем область загрузки
                window.removeEventListener('scroll', onScrollWriters);
            }

            Loading = false;  // Сбрасываем флаг после загрузки
        })
        .catch(error => {
            console.error("Ошибка при загрузке писателей:", error);
            Loading = false;  // Сбрасываем флаг после ошибки
        });
}

function onScrollWriters() {
    // Проверяем, достаточно ли прокрутки до конца страницы
    if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 50) {
        loadMorePopularWriters();  // Загружаем больше писателей, если достигнут низ страницы
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Добавляем слушатель прокрутки для загрузки новых писателей
    window.addEventListener('scroll', onScrollWriters);
});
