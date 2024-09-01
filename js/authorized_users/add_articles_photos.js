const imagePathInput = document.getElementById('image_path');
const previewContainer = document.getElementById('image-preview-container');
const namesInput = document.getElementById('image_names');
const message = document.getElementById('image-limit-message');
const maxImages = 10;
let fileList = []; // Для хранения выбранных файлов

// Обработчик изменения для загрузки нескольких изображений
imagePathInput.addEventListener('change', function(event) {
    const files = Array.from(event.target.files);
    const validFiles = [];
    let errorMessage = '';

    // Очистка предыдущих сообщений
    message.textContent = '';

    // Проверка и фильтрация файлов
    files.forEach(file => {
        if (!isValidImage(file)) {
            errorMessage = `Invalid file format for ${file.name}. Only JPG and PNG are allowed.`;
        } else if (!isValidSize(file)) {
            errorMessage = `${file.name} is too large. Maximum size is 2MB.`;
        } else {
            validFiles.push(file);
        }
    });

    // Отображение сообщения об ошибке, если есть
    if (errorMessage) {
        displayErrorMessage(errorMessage);
        return;
    }

    // Проверка лимита файлов
    if (validFiles.length + previewContainer.children.length > maxImages) {
        displayErrorMessage('You can only upload up to 10 images.');
        return;
    }

    // Обновление списка файлов и интерфейса
    fileList = [...fileList, ...validFiles];
    updatePreviews();
    updateFileNames();
    updateImageLimitMessage();
});

// Обработчик изменения для загрузки обложки статьи
document.getElementById('cover_image').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const container = document.getElementById('cover-preview-container');
    container.innerHTML = ''; // Очистка контейнера

    if (file) {
        if (!isValidImage(file)) {
            alert('Invalid file format. Only JPG and PNG are allowed.');
            return;
        }

        if (!isValidSize(file)) {
            alert('File is too large. Maximum size is 2MB.');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const imgElement = document.createElement('img');
            imgElement.src = e.target.result;

            const previewDiv = createPreviewElement(imgElement, container, 'cover_image');
            container.appendChild(previewDiv);
        };

        reader.readAsDataURL(file);
    }
});

// Функции проверки
function isValidImage(file) {
    const validFormats = ['image/jpeg', 'image/png'];
    return validFormats.includes(file.type);
}

function isValidSize(file) {
    const maxSizeMB = 2; // Максимальный размер в мегабайтах
    return file.size <= maxSizeMB * 1024 * 1024;
}

// Функция для отображения сообщения об ошибке
function displayErrorMessage(messageText) {
    message.textContent = messageText;
    message.style.color = 'red';
}

// Функция для создания превью изображения
function createPreviewElement(imgElement, container, inputId, file) {
    const previewDiv = document.createElement('div');
    previewDiv.classList.add('image-preview');

    const removeButton = document.createElement('button');
    removeButton.textContent = '×';
    removeButton.classList.add('remove');
    removeButton.addEventListener('click', function() {
        // Удаление конкретного изображения из списка файлов
        if (file) {
            fileList = fileList.filter(f => f !== file); // Удаляем конкретное изображение
            updatePreviews();
            updateFileNames();
            updateImageLimitMessage();
        } else {
            container.innerHTML = ''; // Удаляем изображение
            document.getElementById(inputId).value = ''; // Очищаем input
        }
    });

    previewDiv.appendChild(imgElement);
    previewDiv.appendChild(removeButton);

    return previewDiv;
}

// Обновление превью изображений
function updatePreviews() {
    previewContainer.innerHTML = ''; // Очищаем контейнер превью

    fileList.forEach(file => {
        const reader = new FileReader();

        reader.onload = function(e) {
            const imgElement = document.createElement('img');
            imgElement.src = e.target.result;

            const previewDiv = createPreviewElement(imgElement, previewContainer, 'image_path', file);
            previewDiv.dataset.name = file.name; // Сохраняем имя файла в data-атрибуте
            previewContainer.appendChild(previewDiv);
        };

        reader.readAsDataURL(file);
    });
}

// Обновление отображения имен файлов
function updateFileNames() {
    const fileNames = fileList.map(file => file.name); // Извлечение имен файлов
    namesInput.value = fileNames.join(', ');
}

// Обновление сообщения о лимите изображений
function updateImageLimitMessage() {
    if (fileList.length >= maxImages) {
        displayErrorMessage('You have reached the maximum limit of 10 images.');
    } else {
        message.textContent = '';
    }
}