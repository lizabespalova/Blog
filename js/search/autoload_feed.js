let isLoading = false; // Флаг загрузки

function loadMoreArticles() {
    if (getSearchType() !== 'feed' || isLoading) return;

    isLoading = true;
    const offset = document.querySelectorAll('.article-feed-container').length;
    console.log("Offset before request:", offset); // Проверяем значение offset перед запросом

    fetch(`/sections/feed?offset=${offset}`)
        .then(response => response.text())
        .then(data => {
            console.log("Server response:", data);

            if (data.trim()) {
                document.getElementById('content-container').insertAdjacentHTML('beforeend', data);
                processMarkdownContent();
            } else {
                window.removeEventListener('scroll', onScroll);
            }
        })
        .finally(() => {
            isLoading = false;
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
