document.addEventListener('DOMContentLoaded', function() {

    const avatarInput = document.getElementById('avatar');
    // const previewElement = document.getElementById('avatar_preview');
    // const removeButton = document.getElementById('remove_avatar_button');
    // console.log(avatarInput);
    // console.log(previewElement);
    // console.log(removeButton);

    if (avatarInput/* && previewElement && removeButton*/) {
        avatarInput.addEventListener('change', function(event) {
            const file = event.target.files[0];

            if (file) {
                if (!isValidImage(file)) {
                    alert('Invalid file format. Only JPG, PNG, and GIF are allowed.');
                    // removeImage(previewElement, avatarInput, removeButton);
                    return;
                }

                if (!isValidSize(file, 5)) {
                    alert('File is too large. Maximum size is 5MB.');
                    // removeImage(previewElement, avatarInput, removeButton);
                    return;
                }

                // displayImagePreview(file, previewElement, removeButton);
            } else {
                // removeImage(previewElement, avatarInput, removeButton);
            }
        });
        uploadAvatar()
        // removeButton.addEventListener('click', function() {
        //     removeImage(previewElement, avatarInput, this);
        // });
    }
});

function uploadAvatar() {
    uploadFile('avatar', '/app/services/helpers/upload_avatar.php', function() {
        location.reload(); // Перезагружаем страницу после успешной загрузки
    });
}
