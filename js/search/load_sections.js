document.addEventListener("DOMContentLoaded", function () {
    const menuItems = document.querySelectorAll(".menu-item");
    const contentContainer = document.getElementById("content-container");

    const tagSearchForm = document.querySelector(".tag-search-form");

    // Функция для загрузки секции
    function loadSection(section, params = {}, pushState = true) {
        let url = `/sections/${section}`;
        let queryParams = new URLSearchParams(params).toString();
        if (queryParams) url += `?${queryParams}`;

        fetch(url, { method: "GET" })
            .then(response => {
                if (!response.ok) throw new Error(`Ошибка HTTP: ${response.status}`);
                return response.text();
            })
            .then(data => {
                contentContainer.innerHTML = data;

                if (pushState) {
                    history.pushState({ section, params }, "", `?section=${section}&${queryParams}`);
                }

                updateActiveMenu(section);
                processMarkdownContent();
            })
            .catch(error => console.error("Ошибка загрузки секции:", error));
    }

    // Обновление активного меню
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

    // Обработчик отправки формы поиска по тегам
    tagSearchForm.addEventListener("submit", function (event) {
        event.preventDefault();
        const tag = this.querySelector("input[name='tag']").value.trim();
        if (tag) {
            loadSection("tag-search", { tag });
        }
    });

    // Обработка истории браузера
    window.addEventListener("popstate", function (event) {
        if (event.state && event.state.section) {
            loadSection(event.state.section, event.state.params || {}, false);
        }
    });

    // Загружаем начальную секцию
    const urlParams = new URLSearchParams(window.location.search);
    const initialSection = urlParams.get("section") || "popular-articles";
    const initialTag = urlParams.get("tag") || "";

    if (initialSection === "tag-search" && initialTag) {
        loadSection("tag-search", { tag: initialTag }, false);
    } else {
        loadSection(initialSection, {}, false);
    }
});
