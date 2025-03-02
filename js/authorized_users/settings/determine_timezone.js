document.addEventListener("DOMContentLoaded", function () {
    const timezoneSelect = document.getElementById("timezone");
    if (!timezoneSelect) return;

    const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    // Проверяем, есть ли такая таймзона в списке
    const optionExists = Array.from(timezoneSelect.options).some(option => option.value === userTimezone);

    if (optionExists) {
        timezoneSelect.value = userTimezone;
    }
});
