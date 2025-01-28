document.getElementById('privacy-settings-form').addEventListener('change', function () {
    var profileVisibility = document.getElementById('profile-visibility').value;
    var showLastSeen = document.getElementById('show-last-seen').checked ? 1 : 0;

    // Создаем объект FormData для отправки данных формы
    var formData = new FormData();
    formData.append('profile_visibility', profileVisibility);
    formData.append('show_last_seen', showLastSeen);

    // Отправляем данные на сервер с помощью fetch
    fetch('/settings/privacy', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Settings saved successfully');
            } else {
                console.log('Failed to save settings');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
});
