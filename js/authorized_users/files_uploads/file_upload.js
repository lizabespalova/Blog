// Общая функция проверки формата изображения
function isValidImage(file) {
    const validFormats = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    return validFormats.includes(file.type);
}

// Общая функция проверки размера изображения
function isValidSize(file, maxSizeMB = 5) {
    return file.size <= maxSizeMB * 1024 * 1024;
}

// // Функция для отображения превью изображения
// function displayImagePreview(file, previewElement, removeButton) {
//     const reader = new FileReader();
//     reader.onload = function(e) {
//         previewElement.src = e.target.result;
//         previewElement.style.display = 'block'; // Показываем превью
//         removeButton.style.display = 'block'; // Показываем кнопку удаления
//     };
//     reader.readAsDataURL(file);
// }
//
// // Функция удаления изображения
// function removeImage(previewElement, fileInput, removeButton) {
//     previewElement.src = '';
//     previewElement.style.display = 'none'; // Скрываем превью
//     removeButton.style.display = 'none'; // Скрываем кнопку удаления
//     fileInput.value = ''; // Очищаем input
// }

// Общая функция загрузки файла
function uploadFile(fileInputId, url, successCallback) {
    const fileInput = document.getElementById(fileInputId);
    const file = fileInput.files[0];

    if (file) {
        var form = new FormData();
        form.append(fileInputId, file);

        fetch(url, {
            method: 'POST',
            body: form
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    successCallback();
                } else {
                    alert(result.message);
                }
            })
            .catch(error => {
                alert("An error occurred while uploading the file.");
                console.error('Error:', error);
            });
    }
}
