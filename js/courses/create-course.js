document.addEventListener("DOMContentLoaded", function() {

    const form = document.getElementById('create-course-form');
    const submitButton = document.getElementById('submit-course-btn');
    const selectedArticlesInput = document.getElementById('selected-articles');
    const selectedCount = document.getElementById('selected-articles-count');

    submitButton.addEventListener('click', function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        const selectedArticles = Array.from(document.querySelectorAll('input[type="checkbox"]:checked')).map(checkbox => checkbox.value);
        formData.append('articles', selectedArticles.join(','));

        // Проверяем, что файл действительно добавляется
        console.log("Отправляемые данные:");
        for (let pair of formData.entries()) {
            console.log(pair[0], pair[1]);
        }

        fetch('/create-course', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = "/my-courses";
                } else {
                    console.error("Ошибка сервера:", data.message);
                    window.location.href = "/error?message=" + encodeURIComponent(data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка сети:', error);
                window.location.href = "/error?message=" + encodeURIComponent(error.message);
            });
    });


});