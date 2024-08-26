function validateFile() {
    var fileInput = document.getElementById('avatar');
    var file = fileInput.files[0];
    var allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
    var maxSize = 5 * 1024 * 1024; // 5 MB

    if (file) {
        var fileSize = file.size;
        var fileName = file.name;
        var fileExtension = fileName.split('.').pop().toLowerCase();

        if (fileSize > maxSize) {
            alert("File size exceeds the maximum allowed limit of 5 MB.");
            return false;
        }

        if (!allowedExts.includes(fileExtension)) {
            alert("Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.");
            return false;
        }
    }

    return true;
}

function uploadAvatar() {
    if (validateFile()) {
        var form = new FormData();
        var fileInput = document.getElementById('avatar');
        form.append('avatar', fileInput.files[0]);

        fetch('/app/views/authorized_users/upload_avatar.php', {
            method: 'POST',
            body: form
        })
            .then(response => response.json())
            .then(result => {
                // alert(result.message);
                if (result.success) {
                    location.reload(); // Refresh the page to show the new avatar
                }
            })
            .catch(error => {
                alert("An error occurred while uploading the image.");
                console.error('Error:', error);
            });
    }
}