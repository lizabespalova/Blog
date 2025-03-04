document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("articles-modal");
    const btn = document.getElementById("select-articles-btn");
    const closeBtn = document.querySelector(".close");
    const searchInput = document.getElementById("article-search");
    const articlesList = document.getElementById("articles-list");
    const selectedArticlesInput = document.getElementById("selected-articles");
    // const selectedCount = document.getElementById("selected-articles-count");
    const saveBtn = document.getElementById("save-settings-btn");

    if (!modal || !btn || !closeBtn || !searchInput || !articlesList || !selectedArticlesInput || /*!selectedCount ||*/ !saveBtn) {
        return;
    }

    let selectedArticles = [];

    // Открытие модального окна
    btn.onclick = function () {
        modal.style.display = "block";
    };

    // Закрытие модального окна
    closeBtn.onclick = function () {
        modal.style.display = "none";
    };

    // Фильтрация статей по поисковому запросу
    searchInput.addEventListener("input", function () {
        const searchText = searchInput.value.toLowerCase();
        const items = articlesList.querySelectorAll(".article-item");
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(searchText) ? "block" : "none";
        });
    });

    // Определение чекбоксов статей
    const articleCheckboxes = articlesList.querySelectorAll('input[type="checkbox"]');

    // Сохранение выбора статей
    saveBtn.addEventListener("click", () => {
        const selectedIds = Array.from(articleCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        selectedArticlesInput.value = selectedIds.join(",");

        // Получаем данные о курсе из модального окна
        const courseId = document.getElementById("course-id").value; // ID курса
        const courseTitle = document.getElementById("modal-course-title").value; // Название курса
        const courseDescription = document.getElementById("modal-course-description").value; // Описание курса
        const courseCoverImage = document.getElementById("modal-course-cover-image").value; // Путь к изображению курса

        // Отправка данных в БД через AJAX
        fetch("/update-course", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `course_id=${encodeURIComponent(courseId)}&title=${encodeURIComponent(courseTitle)}&description=${encodeURIComponent(courseDescription)}&cover_image=${encodeURIComponent(courseCoverImage)}&articles=${encodeURIComponent(selectedArticlesInput.value)}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    modal.style.display = "none";
                    // selectedCount.textContent = `${selectedIds.length}`;
                    location.reload(); // Обновляем список статей
                } else {
                    alert("Ошибка при сохранении! " + data.error);
                }
            })
            .catch(error => console.error("Ошибка:", error));
    });
});
