document.addEventListener("DOMContentLoaded", function () {
    const deleteButton = document.getElementById("delete-course-btn");
    const courseId = document.getElementById("course-id").value; // ID курса

    deleteButton.addEventListener("click", function () {
        // Запрашиваем подтверждение у пользователя
        const confirmDelete = confirm("Are you sure that you want to delete the course?");

        if (confirmDelete) {
            // Если пользователь подтвердил, отправляем запрос на сервер
            const formData = new URLSearchParams();
            formData.append('course_id', courseId); // Добавляем ID курса

            fetch('/delete-course', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData.toString() // Отправляем данные через форму
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.href = "/success?message=" + 'The course was successfully deleted';
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch(error => {
                    console.error("Ошибка:", error);
                    alert("Error");
                });
        }
    });
});
