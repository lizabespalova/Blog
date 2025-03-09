document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById('create-course-form');
    const submitButton = document.getElementById('submit-course-btn');
    const selectedArticlesInput = document.getElementById('selected-articles');
    const selectedCount = document.getElementById('selected-articles-count');

    submitButton.addEventListener('click', function (e) {
        e.preventDefault();

        const titleInput = document.getElementById('course-title');
        const descInput = document.getElementById('course-description');

        const title = titleInput.value.trim();
        const description = descInput.value.trim();

        // Validate title length
        if (title.length === 0 || title.length > 100) {
            alert("Course title must be between 1 and 100 characters.");
            titleInput.focus();
            return;
        }

        // Validate description length
        if (description.length === 0 || description.length > 1000) {
            alert("Course description must be between 1 and 1000 characters.");
            descInput.focus();
            return;
        }

        // Prepare form data
        const formData = new FormData(form);
        const selectedArticles = Array.from(document.querySelectorAll('input[type="checkbox"]:checked'))
            .map(checkbox => checkbox.value);
        formData.append('articles', selectedArticles.join(','));

        // Debug: Log the data before sending
        console.log("Sending form data:");
        for (let pair of formData.entries()) {
            console.log(pair[0], pair[1]);
        }

        // Submit the form via fetch
        fetch('/create-course', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
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
