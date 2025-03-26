let isLoading = false;  // Флаг для предотвращения повторных запросов

function loadMorePopularCourses() {
    if (getSearchType() !== 'courses') return;

    if (isLoading) return;  // Если уже загружается, не делаем запрос

    const offset = document.querySelectorAll('.course').length;  // Сколько уже загружено курсов

    isLoading = true;  // Устанавливаем флаг, что запрос выполняется

    // Получаем новые популярные курсы с сервером, передавая смещение
    fetch(`/sections/popular-courses?offset=${offset}`)
        .then(response => response.text())
        .then(data => {
            console.log(data);  // Для отладки

            // Проверяем, есть ли данные
            if (data.trim()) {
                document.getElementById('popular-courses-content').innerHTML += data;
            } else {
                console.log("Нет новых курсов для загрузки");
                window.removeEventListener('scroll', onScrollPopularCourses); // Скрываем область загрузки
            }

            isLoading = false;  // Сбрасываем флаг после загрузки
        })
        .catch(() => {
            isLoading = false;  // Сбрасываем флаг, если произошла ошибка
        });
}

function onScrollPopularCourses() {
    // Проверяем, достиг ли пользователь нижней части страницы
    if (window.innerHeight + window.scrollY >= document.body.scrollHeight) {
        loadMorePopularCourses();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Добавляем слушатель прокрутки для популярных курсов
    window.addEventListener('scroll', onScrollPopularCourses);
});
