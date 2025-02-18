document.addEventListener("DOMContentLoaded", function () {
    function loadPage(url, pushState = true) {
        fetch(url, {
            method: "GET",
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
            .then(response => response.text())
            .then(html => {
                console.log("Ответ сервера:", html);  // Добавить для отладки
                let feedContent = document.querySelector("#feed-content");
                if (feedContent) {
                    let parser = new DOMParser();
                    let doc = parser.parseFromString(html, "text/html");
                    let newContent = doc.querySelector("#feed-content");

                    if (newContent) {
                        feedContent.innerHTML = newContent.innerHTML;
                        let newPagination = doc.querySelector(".pagination");
                        let paginationContainer = document.querySelector(".pagination");

                        if (paginationContainer && newPagination) {
                            paginationContainer.innerHTML = newPagination.innerHTML;
                        }

                        if (pushState) {
                            history.pushState({ url }, "", url);
                        }
                    } else {
                        console.error("Ошибка: новый контент не найден!");
                    }
                }
            })

    }

    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("pagination-link")) {
            event.preventDefault();
            let url = event.target.getAttribute("href");
            loadPage(url);
        }
    });

    window.addEventListener("popstate", function (event) {
        if (event.state && event.state.url) {
            loadPage(event.state.url, false);
        }
    });
});
