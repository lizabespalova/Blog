document.addEventListener("DOMContentLoaded", function () {
    let coverContainer = document.querySelector(".course-cover-container");
    let courseId = coverContainer.dataset.courseId;
    let fileInput = document.getElementById("cover-upload");
    let coverImg = document.getElementById("course-cover");

    // Функция вызывается по нажатию на кнопку "✏️"
    window.triggerCoverUpload = function () {
        fileInput.click();
    };

    fileInput.addEventListener("change", function () {
        let file = fileInput.files[0];
        if (!file) return;

        // Проверяем формат
        if (!isValidImage(file)) {
            showError("Invalid file format. Only JPEG, PNG, or GIF images are allowed.");
            return;
        }

        // Проверяем размер (5MB)
        if (!isValidSize(file, 5)) {
            showError("File size exceeds the limit of 5 MB.");
            return;
        }

        // Показываем подтверждение перед загрузкой
        if (!confirm("Change cover")) return;

        let formData = new FormData();
        formData.append("cover_image", file);
        formData.append("course_id", courseId);

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "/update_cover", true);

        xhr.onload = function () {
            try {
                let response = JSON.parse(xhr.responseText);
                if (response.success) {
                    coverImg.src = response.new_cover;
                    location.reload(); // Без задержки

                } else {
                    alert("Ошибка: " + response.error);
                }
            } catch (e) {
                alert("Ошибка обработки ответа сервера.");
            }
        };

        xhr.send(formData);
    });
});

// Общая функция проверки формата изображения
function isValidImage(file) {
    const validFormats = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    return validFormats.includes(file.type);
}

// Общая функция проверки размера изображения
function isValidSize(file, maxSizeMB = 5) {
    return file.size <= maxSizeMB * 1024 * 1024;
}
