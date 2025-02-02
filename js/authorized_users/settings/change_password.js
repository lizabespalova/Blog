document.addEventListener("DOMContentLoaded", function () {
    var securityForm = document.getElementById("security-settings-form");
    if (!securityForm) return; // Проверяем, что форма существует

    securityForm.addEventListener("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(securityForm);

        fetch("/settings/update-password", {
            method: "POST",
            body: formData
        })
            .then(response => response.json()) // Получаем JSON
            .then(data => {
                if (data.success) {
                    // Успех → очищаем форму и перенаправляем
                    securityForm.reset();
                    window.location.href = "/success?message=" + encodeURIComponent(data.message) + "&user_login=" + encodeURIComponent(data.user_login);
                } else {
                    // Ошибка → перенаправляем на страницу ошибки
                    window.location.href = "/error?message=" + encodeURIComponent(data.message) + "&user_login=" + encodeURIComponent(data.user_login);
                }
            })
            .catch(error => console.error("Error:", error));
    });
});
