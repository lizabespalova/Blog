document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("articles-modal");
    const btn = document.getElementById("select-articles-btn");
    const closeBtn = document.querySelector(".close");
    const searchInput = document.getElementById("article-search");
    const articlesList = document.getElementById("articles-list");
    const selectedArticlesInput = document.getElementById("selected-articles");
    const selectedCount = document.getElementById("selected-articles-count");
    const saveBtn = document.getElementById("save-settings-btn");

    if (!modal || !btn || !closeBtn || !searchInput || !articlesList || !selectedArticlesInput || !selectedCount || !saveBtn) {
        console.error("Один или несколько элементов не найдены в DOM.");
        return;
    }

    let selectedArticles = [];

    btn.onclick = function () {
        modal.style.display = "block";
    };

    closeBtn.onclick = function () {
        modal.style.display = "none";
    };

    searchInput.addEventListener("input", function () {
        const searchText = searchInput.value.toLowerCase();
        const items = articlesList.querySelectorAll(".article-item");
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(searchText) ? "block" : "none";
        });
    });

    saveBtn.addEventListener("click", function () {
        selectedArticles = [];
        const checkboxes = articlesList.querySelectorAll("input[type='checkbox']:checked");

        checkboxes.forEach(checkbox => {
            const articleItem = checkbox.closest(".article-item");
            const title = articleItem.querySelector(".course-card h3").textContent.trim();
            const imageSrc = articleItem.querySelector(".course-card img")?.src || "/images/default-avatar.png";

            selectedArticles.push({ id: checkbox.value, title, image: imageSrc });
        });

        updateSelectedArticlesUI();
        modal.style.display = "none";
    });

    function updateSelectedArticlesUI() {
        selectedArticlesInput.value = selectedArticles.map(article => article.id).join(",");

        selectedCount.innerHTML = `
            <div class="selected-articles-container">
                ${selectedArticles.map(article => `
                    <div class="selected-article-card" data-id="${article.id}">
                        <img src="${article.image}" alt="${article.title}">
                        <p title="${article.title}">${article.title}</p>
                        <button class="remove-article-btn">&times;</button>
                    </div>
                `).join("")}
            </div>
        `;

        document.querySelectorAll(".remove-article-btn").forEach(button => {
            button.addEventListener("click", function () {
                const card = this.closest(".selected-article-card");
                const articleId = card.getAttribute("data-id");

                selectedArticles = selectedArticles.filter(article => article.id !== articleId);
                updateSelectedArticlesUI();
            });
        });
    }
});
