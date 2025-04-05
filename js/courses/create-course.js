document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById('create-course-form');
    const submitButton = document.getElementById('submit-course-btn');
    const titleInput = document.getElementById('course-title');
    const descInput = document.getElementById('course-description');
    const selectedArticlesInput = document.getElementById('selected-articles');
    const selectedCount = document.getElementById('selected-articles-count');

    // Восстанавливаем данные из localStorage
    if (localStorage.getItem("course_title")) {
        titleInput.value = localStorage.getItem("course_title");
    }
    if (localStorage.getItem("course_description")) {
        descInput.value = localStorage.getItem("course_description");
    }
    if (localStorage.getItem("selected_articles")) {
        selectedArticlesInput.value = localStorage.getItem("selected_articles");
        selectedCount.textContent = `${selectedArticlesInput.value.split(',').length} статей выбрано`;
    }

    // Сохраняем данные при вводе
    titleInput.addEventListener("input", function () {
        localStorage.setItem("course_title", this.value);
    });

    descInput.addEventListener("input", function () {
        localStorage.setItem("course_description", this.value);
    });

    // Обработчик отправки формы
    submitButton.addEventListener("click", function (e) {
        e.preventDefault();

        const title = titleInput.value.trim();
        const description = descInput.value.trim();

        // Валидация
        if (title.length === 0 || title.length > 100) {
            alert("Course title must be between 1 and 100 characters.");
            titleInput.focus();
            return;
        }

        if (description.length === 0 || description.length > 1000) {
            alert("Course description must be between 1 and 1000 characters.");
            descInput.focus();
            return;
        }

        // Формируем данные формы
        const formData = new FormData(form);
        const selectedArticles = Array.from(document.querySelectorAll('input[type="checkbox"]:checked'))
            .map(checkbox => checkbox.value);
        formData.append('articles', selectedArticles.join(','));
        localStorage.setItem("selected_articles", selectedArticles.join(','));

        // Отладка
        console.log("Sending form data:");
        for (let pair of formData.entries()) {
            console.log(pair[0], pair[1]);
        }

        // Отправка данных через fetch
        fetch('/create-course', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    localStorage.removeItem("course_title");
                    localStorage.removeItem("course_description");
                    localStorage.removeItem("selected_articles");

                    const userLogin = document.getElementById('user-info').getAttribute('data-login');
                    window.location.href = "/my-courses/" + userLogin;
                } else {
                    console.error("Server error:", data.message);
                    window.location.href = "/error?message=" + encodeURIComponent(data.message);
                }
            })
            .catch(error => {
                console.error('Network error:', error);
                window.location.href = "/error?message=" + encodeURIComponent(error.message);
            });
    });
});
