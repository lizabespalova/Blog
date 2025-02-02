document.addEventListener("DOMContentLoaded", function () {
    var privacyForm = document.getElementById("privacy-settings-form");
    if (!privacyForm) return; // Проверка на существование элемента

    privacyForm.addEventListener("change", function () {
        var profileVisibility = document.getElementById("profile-visibility");
        var showLastSeen = document.getElementById("show-last-seen");

        if (!profileVisibility || !showLastSeen) return; // Проверяем, что элементы существуют

        // Получаем значения
        var profileVisibilityValue = profileVisibility.value;
        var showLastSeenValue = showLastSeen.checked ? 1 : 0;

        // Создаем объект FormData
        var formData = new FormData();
        formData.append("profile_visibility", profileVisibilityValue);
        formData.append("show_last_seen", showLastSeenValue);

        // Отправляем данные на сервер
        fetch("/settings/privacy", {
            method: "POST",
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    console.log("Settings saved successfully");
                } else {
                    console.log("Failed to save settings");
                }
            })
            .catch((error) => {
                console.error("Error:", error);
            });
    });
});
