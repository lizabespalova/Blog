document.addEventListener("DOMContentLoaded", function () {
    let descElement = document.querySelector(".course-description");
    let editButton = document.querySelector(".desc-edit-btn");
    let modal = document.getElementById("desc-modal");
    let descInput = document.getElementById("desc-input");
    let saveButton = document.getElementById("save-desc");
    let cancelButton = document.getElementById("cancel-desc");
    let coverContainer = document.querySelector(".course-cover-container");

    let courseId = coverContainer.dataset.courseId;
    if (!descElement || !editButton) return;

    // Открытие модального окна с текущим описанием
    editButton.addEventListener("click", function () {
        descInput.value = descElement.firstChild.textContent.trim();
        modal.style.display = "flex";
    });

    // Закрытие модального окна без сохранения
    cancelButton.addEventListener("click", function () {
        modal.style.display = "none";
    });

    // Сохранение нового описания
    saveButton.addEventListener("click", function () {
        let newDesc = descInput.value.trim();
        if (!newDesc || newDesc === descElement.firstChild.textContent.trim()) {
            modal.style.display = "none";
            return;
        }

        let formData = new FormData();
        formData.append("description", newDesc);
        formData.append("course_id", courseId);

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "/update_course_description", true);

        xhr.onload = function () {
            try {
                let response = JSON.parse(xhr.responseText);
                if (response.success) {
                    descElement.innerHTML = newDesc.replace(/\n/g, "<br>"); // Обновляем текст на странице
                    modal.style.display = "none";
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
