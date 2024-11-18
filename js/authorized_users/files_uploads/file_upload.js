// Общая функция проверки формата изображения
function isValidImage(file) {
    const validFormats = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    return validFormats.includes(file.type);
}

// Общая функция проверки размера изображения
function isValidSize(file, maxSizeMB = 5) {
    return file.size <= maxSizeMB * 1024 * 1024;
}
function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Oops!',
        text: message,
        confirmButtonText: 'OK',
        customClass: {
            popup: 'swal2-popup',
            title: 'swal2-title',
            htmlContainer: 'swal2-html-container',
            confirmButton: 'swal2-confirm'
        }
    });
}

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
