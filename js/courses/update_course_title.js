document.addEventListener("DOMContentLoaded", function () {
    let titleElement = document.querySelector(".course-title");
    let editButton = document.querySelector(".title-edit-btn");

    if (!titleElement || !editButton) return;
    let coverContainer = document.querySelector(".course-cover-container");
    let courseId = coverContainer.dataset.courseId;

    editButton.addEventListener("click", function () {
        let currentTitle = titleElement.firstChild.textContent.trim();
        let newTitle = prompt("Enter new course title (maximum 100 characters):", currentTitle);

        if (newTitle === null) return; // user canceled
        newTitle = newTitle.trim();

        if (newTitle === "" || newTitle === currentTitle) return;

        if (newTitle.length > 100) {
            alert("Course title must not exceed 100 characters.");
            return;
        }

        let formData = new FormData();
        formData.append("title", newTitle);
        formData.append("course_id", courseId);

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "/update_course_title", true);

        xhr.onload = function () {
            try {
                let response = JSON.parse(xhr.responseText);
                if (response.success) {
                    titleElement.firstChild.textContent = newTitle;
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