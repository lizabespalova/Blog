document.addEventListener("DOMContentLoaded", function () {
    const fileInput = document.getElementById("course-material-file");
    const uploadBtn = document.getElementById("upload-material-btn");
    const descriptionField = document.getElementById("material-description");

    const allowedTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'image/jpeg',
        'image/png',
        'text/html',
        'audio/mpeg',
    ];

    const maxSizeMB = 5;
    const maxFileSize = maxSizeMB * 1024 * 1024;
    const maxFilesCount = 5; // Максимальное количество файлов

    // Проверка файла на допустимый тип и размер
    function validateFile(file) {
        if (!allowedTypes.includes(file.type)) {
            alert(`❌ Неверный формат файла: ${file.name}`);
            return false;
        }
        if (file.size > maxFileSize) {
            alert(`❌ Файл ${file.name} слишком большой. Максимум: ${maxSizeMB}MB`);
            return false;
        }
        return true;
    }

    // Загрузка файлов
    function uploadFiles(files, description) {
        const formData = new FormData();
        Array.from(files).forEach(file => {
            formData.append('material_files[]', file); // добавляем файл
        });
        formData.append('course_id', document.getElementById('course-id').value); // отдельно добавляем course_id
        formData.append('description', description);

        fetch('/upload-material-course', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // alert('✅ Материалы успешно загружены!');
                    location.reload(); // Перезагрузить страницу для обновления списка
                } else {
                    alert('❌ Eerror while uploading: ' + data.error);
                }
            })
            .catch(err => {
                console.error(err);
                alert('❌ Error');
            });
    }

    // Обработчик нажатия кнопки загрузки
    function handleUpload() {
        const files = fileInput.files;
        const description = descriptionField.value.trim();

        if (files.length === 0) {
            alert('⚠ Please select at least one file');
            return;
        }

        if (description === '') {
            alert('⚠ Please enter a description of the material');
            return;
        }

        if (files.length > maxFilesCount) {
            alert(`⚠ You can upload up to ${maxFilesCount} files.`);
            return;
        }

        for (let i = 0; i < files.length; i++) {
            if (!validateFile(files[i])) {
                return; // Прерываем обработку при неверном файле
            }
        }

        uploadFiles(files, description); // Загружаем файлы
    }

    // Привязываем обработчик события к кнопке
    uploadBtn.addEventListener("click", handleUpload);
});
