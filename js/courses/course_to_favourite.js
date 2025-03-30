document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".btn-favorite").forEach(button => {
        button.addEventListener("click", function () {
            let courseId = this.getAttribute("data-course-id");
            let action = this.classList.contains("added") ? "remove" : "add";

            console.log(`Sending request: course_id=${courseId}, action=${action}`);

            fetch("/favorite/course", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ course_id: courseId, action: action })
            })
                .then(response => response.json())
                .then(data => {
                    console.log("Server response:", data);

                    if (data.success) {
                        // Переключаем класс
                        if (data.action === "added") {
                            this.classList.add("added");
                            this.style.borderColor = "#ffa500";
                            this.title = "Remove from Favorites";
                        } else {
                            this.classList.remove("added");
                            this.style.borderColor = "";
                            this.title = "Add to Favorites";
                        }
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch(error => console.error("Fetch error:", error));
        });
    });
});
