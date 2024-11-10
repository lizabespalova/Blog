document.getElementById('cover_image').addEventListener('change', function (event) {
    const file = event.target.files[0];
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

// Функция для удаления изображения
document.getElementById('remove_button').addEventListener('click', function (event) {
    event.preventDefault();  // Предотвращаем отправку формы

    const preview = document.getElementById('cover_image_preview');
    const fileInput = document.getElementById('cover_image');

    preview.src = '';  // Очистить изображение
    preview.style.display = 'none';  // Скрыть превью
    this.style.display = 'none';  // Скрыть кнопку удаления
    fileInput.value = '';  // Сбросить выбранное изображение в input
});
