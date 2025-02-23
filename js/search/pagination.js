document.addEventListener("DOMContentLoaded", function () {
    const menuItems = document.querySelectorAll(".menu-item");
    const contentContainer = document.getElementById("content-container");

    function loadSection(section, page = 1, pushState = true) {
        console.log(`Загрузка секции: ${section}, страница: ${page}`);

        fetch(`?section=${section}&page=${page}`, {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest" // <- говорим серверу, что это AJAX
            }
        })
            .then(response => {
                if (!response.ok) throw new Error(`Ошибка HTTP: ${response.status}`);
                return response.text();
            })
            .then(data => {
                console.log("Ответ сервера:", data);

                if (data.includes("<html")) {
                    console.error("Ошибка: сервер отдаёт полный HTML-документ!");
                    return;
                }

                contentContainer.innerHTML = data;

                if (pushState) {
                    history.pushState({ section, page }, "", `?section=${section}&page=${page}`);
                }

                updateActiveMenu(section);

                if (typeof processMarkdownContent === "function") {
                    processMarkdownContent();
                } else {
                    console.warn("processMarkdownContent() не найдена!");
                }
            })
            .catch(error => console.error("Ошибка загрузки секции:", error));
    }

    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("pagination-link")) {
            event.preventDefault();
            const page = event.target.getAttribute("data-page");
            const section = new URLSearchParams(window.location.search).get('section') || 'feed'; // Получаем текущую секцию
            loadSection(section, page);
        }
    });

    function updateActiveMenu(section) {
        menuItems.forEach(item => {
            item.classList.toggle("active", item.getAttribute("data-section") === section);
        });
    }

    menuItems.forEach(item => {
        item.addEventListener("click", function (event) {
            event.preventDefault();
            const section = this.getAttribute("data-section");
            loadSection(section);
        });
    });

    window.addEventListener("popstate", function (event) {
        if (event.state && event.state.section) {
            loadSection(event.state.section, event.state.page, false);
        }
    });

    const urlParams = new URLSearchParams(window.location.search);
    const initialSection = urlParams.get("section") || 'popular-articles';
    const initialPage = urlParams.get("page") || 1;

    loadSection(initialSection, initialPage, false);
});
