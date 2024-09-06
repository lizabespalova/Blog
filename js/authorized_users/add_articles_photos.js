

document.getElementById('cover_image').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const previewElement = document.getElementById('cover_image_preview');
    const removeButton = document.getElementById('remove_button');

    if (file) {
        if (!isValidImage(file)) {
            alert('Invalid file format. Only JPG and PNG are allowed.');
            previewElement.style.display = 'none'; // Скрываем превью в случае ошибки
            removeButton.style.display = 'none'; // Скрываем кнопку удаления
            return;
        }

        if (!isValidSize(file)) {
            alert('File is too large. Maximum size is 2MB.');
            previewElement.style.display = 'none'; // Скрываем превью в случае ошибки
            removeButton.style.display = 'none'; // Скрываем кнопку удаления
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            previewElement.src = e.target.result;
            previewElement.style.display = 'block'; // Показываем превью
            removeButton.style.display = 'block'; // Показываем кнопку удаления
        };
        reader.readAsDataURL(file);
    } else {
        previewElement.style.display = 'none'; // Скрываем превью если файл не выбран
        removeButton.style.display = 'none'; // Скрываем кнопку удаления
    }
});

// Обработчик клика по кнопке удаления
document.getElementById('remove_button').addEventListener('click', function() {
    const previewElement = document.getElementById('cover_image_preview');
    const fileInput = document.getElementById('cover_image');

    previewElement.src = '';
    previewElement.style.display = 'none'; // Скрываем превью
    this.style.display = 'none'; // Скрываем кнопку удаления
    fileInput.value = ''; // Очищаем input
});

// Функция проверки формата изображения
function isValidImage(file) {
    const validFormats = ['image/jpeg', 'image/png'];
    return validFormats.includes(file.type);
}

// Функция проверки размера изображения
function isValidSize(file) {
    const maxSizeMB = 2; // Максимальный размер в мегабайтах
    return file.size <= maxSizeMB * 1024 * 1024;
}
