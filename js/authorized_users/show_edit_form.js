function toggleEditForm() {
    const profileInfo = document.getElementById('profile-info');
    const editForm = document.getElementById('edit-form');

    if (profileInfo.style.display === 'none') {
        profileInfo.style.display = 'block';
        editForm.style.display = 'none';
    } else {
        profileInfo.style.display = 'none';
        editForm.style.display = 'block';
    }
}

function submitEditForm() {
    const form = document.getElementById('profile-edit-form');
    const formData = new FormData(form);

    fetch('/update-description', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            // Проверяем, был ли запрос успешным
            if (response.ok) {
                console.log("Response true");
                location.reload(); // Перезагружаем страницу после успешного обновления
            } else {
                return response.text().then(text => {
                    throw new Error(text);
                });
            }
        })
        .catch(error => {
            // Выводим сообщение об ошибке в консоль
            console.error('Error:', error.message);
        });
}
