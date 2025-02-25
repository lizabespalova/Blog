document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("articles-modal");
    const btn = document.getElementById("select-articles-btn");
    const closeBtn = document.querySelector(".close");
    const searchInput = document.getElementById("article-search");
    const articlesList = document.getElementById("articles-list");
    const selectedArticlesInput = document.getElementById("selected-articles");
    const selectedCount = document.getElementById("selected-articles-count");

    if (!modal || !btn || !closeBtn || !searchInput || !articlesList || !selectedArticlesInput || !selectedCount) {
        console.error("Один или несколько элементов не найдены в DOM.");
        return;
    }

    let selectedArticles = [];

    // Закрытие бокового окна (если оно существует)
    const sideModal = document.querySelector('.side-modal');
    if (sideModal) {
        sideModal.style.display = 'none';
    }

    // Открытие модального окна
    btn.onclick = function () {
        modal.style.display = "block"; // Показываем модальное окно
        if (sideModal) {
            sideModal.style.display = 'none'; // Прячем боковое окно, если оно существует
        }
    };

    // Закрытие модального окна
    closeBtn.onclick = function () {
        modal.style.display = "none";
    };

    // Поиск статей
    searchInput.addEventListener("input", function () {
        const searchText = searchInput.value.toLowerCase();
        const items = articlesList.querySelectorAll(".article-item");
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(searchText) ? "block" : "none";
        });
    });
});
