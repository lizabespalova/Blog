document.addEventListener("DOMContentLoaded", function () {
    const menuItems = document.querySelectorAll(".menu-item");
    const contentContainer = document.getElementById("content-container");

    // Функция для загрузки секции
    function loadSection(section, pushState = true) {
        fetch(`/sections/${section}`, { method: "POST" })
            .then(response => {
                if (!response.ok) throw new Error(`Ошибка HTTP: ${response.status}`);
                return response.text();
            })
            .then(data => {
                contentContainer.innerHTML = data;

                // Если необходимо, добавляем историю
                if (pushState) {
                    history.pushState({ section }, "", `?section=${section}`);
                }

                updateActiveMenu(section);
                processMarkdownContent();
            })
            .catch(error => console.error("Ошибка загрузки секции:", error));
    }

    // Функция для обновления активного пункта меню
    function updateActiveMenu(section) {
        menuItems.forEach(item => {
            item.classList.toggle("active", item.getAttribute("data-section") === section);
        });
    }

    // Обработка кликов по меню
    menuItems.forEach(item => {
        item.addEventListener("click", function (event) {
            event.preventDefault();
            const section = this.getAttribute("data-section");
            loadSection(section);
        });
    });

    // Обработчик события изменения истории браузера
    window.addEventListener("popstate", function (event) {
        if (event.state && event.state.section) {
            loadSection(event.state.section, false);
        }
    });

    // Проверка параметра `section` в URL
    const urlParams = new URLSearchParams(window.location.search);
    const initialSection = urlParams.get("section") || 'popular-articles'; // Если нет параметра, используем 'popular-articles'

    loadSection(initialSection, false); // Загружаем секцию по умолчанию
});
