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

    // Open modal with current description
    editButton.addEventListener("click", function () {
        // Заменяем 'textContent' на 'innerText', чтобы не захватывать символы, такие как кнопки
        descInput.value = descElement.innerText.trim();
        modal.style.display = "flex";
    });

    // Close modal without saving
    cancelButton.addEventListener("click", function () {
        modal.style.display = "none";
    });

    // Save new description
    saveButton.addEventListener("click", function () {
        let newDesc = descInput.value.trim();
        let currentDesc = descElement.innerText.trim();  // Используем innerText для получения только текста без HTML

        if (!newDesc || newDesc === currentDesc) {
            modal.style.display = "none";
            return;
        }

        if (newDesc.length > 1000) {
            alert("Description must not exceed 1000 characters.");
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
                    // После сохранения, обновляем описание без включения кнопки редактирования
                    descElement.innerHTML = newDesc.replace(/\n/g, "<br>");
                    modal.style.display = "none";
                } else {
                    alert("Error: " + response.error);
                }
            } catch (e) {
                alert("Error processing server response.");
            }
        };

        xhr.send(formData);
    });
});