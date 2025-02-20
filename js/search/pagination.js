document.addEventListener("DOMContentLoaded", function () {
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("pagination-link")) {
            event.preventDefault();
            let url = event.target.getAttribute("href");

            fetch(url, {
                method: "POST",
                headers: { "X-Requested-With": "XMLHttpRequest" }
            })
                .then(response => response.text())
                .then(html => {
                    console.log("Ответ от сервера:", html); // Логируем ответ

                    let feedContent = document.querySelector("#feed-content");
                    if (feedContent) {
                        feedContent.innerHTML = html;
                        history.pushState(null, "", url);
                    } else {
                        console.error("Ошибка: контейнер #feed-content не найден!");
                    }
                })
                .catch(error => console.error("Ошибка загрузки:", error));
        }
    });
});
