<?php
// Получение сообщения из URL параметра или сессии
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message'], ENT_QUOTES, 'UTF-8') : 'Operation completed successfully.';
$safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
$userLogin = $_GET['user_login'] ?? ''; // Получаем логин пользователя из URL
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <link rel="stylesheet" href="/css/success.css">
</head>
<body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Получаем сообщение из PHP
        const message = <?php echo json_encode($safeMessage); ?>;
        let htmlContent = `
            <div style="text-align: center;">
                <p>${message}</p>
        `;

        // Проверяем, содержит ли сообщение успешное действие
        if (message === 'Email updated successfully') {
            htmlContent += `
                <br><br>
                <a href="/profile/<?php echo htmlspecialchars($userLogin); ?>" style="color: #007bff; text-decoration: underline;">Go to your profile</a>
            `;
        }

        htmlContent += '</div>';

        Swal.fire({
            icon: 'success',
            title: 'Success!',
            html: htmlContent,
            confirmButtonText: 'OK',
            customClass: {
                popup: 'swal2-popup',
                title: 'swal2-title',
                htmlContainer: 'swal2-html-container',
                confirmButton: 'swal2-confirm'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '/'; // Перенаправление на главную страницу или другую страницу по вашему выбору
            }
        });
    });
</script>
</body>
</html>
