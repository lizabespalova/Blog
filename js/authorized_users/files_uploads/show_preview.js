// Обработчик изменения изображения
document.getElementById('cover_image').addEventListener('change', function (event) {
    const file = event.target.files[0];

    if (!isValidImage(file)) {
        showError("Invalid file format. Only JPEG, PNG, or GIF images are allowed.");
        return;
    }

    if (!isValidSize(file)) {
        showError("File size exceeds the limit of 5 MB.");
        return;
    }

    const preview = document.getElementById('cover_image_preview');
    const removeButton = document.getElementById('remove_button');

    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            removeButton.style.display = 'inline-block';
        };
        reader.readAsDataURL(file);
    }
});

// Обработчик для удаления изображения
document.getElementById('remove_button').addEventListener('click', function (event) {
    event.preventDefault();  // Предотвращаем отправку формы

    const preview = document.getElementById('cover_image_preview');
    const fileInput = document.getElementById('cover_image');
    const removeInput = document.getElementById('remove_cover_image');  // Скрытое поле

    preview.src = '';  // Очистить изображение
    preview.style.display = 'none';  // Скрыть превью
    this.style.display = 'none';  // Скрыть кнопку удаления
    fileInput.value = '';  // Сбросить выбранное изображение в input
    removeInput.value = '1';  // Установить флаг для удаления изображения при отправке формы
});

// Функция для валидации формата изображения
function isValidImage(file) {
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    return allowedTypes.includes(file.type);
}

// Функция для валидации размера файла
function isValidSize(file) {
    const maxSize = 5 * 1024 * 1024;  // 5 MB
    return file.size <= maxSize;
}

// Функция для отображения ошибок
function showError(message) {
    alert(message);  // Можно заменить на отображение ошибок в UI
}
