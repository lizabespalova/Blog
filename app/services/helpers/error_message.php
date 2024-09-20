<?php
// Получение сообщения из URL параметра или сессии
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message'], ENT_QUOTES, 'UTF-8') : 'Undefined error';
$safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Кастомные стили для улучшения внешнего вида */
        .swal2-popup {
            font-family: 'Arial', sans-serif;
        }
        .swal2-title {
            font-size: 1.5rem;
            color: #d9534f; /* Цвет заголовка */
        }
        .swal2-html-container {
            font-size: 1rem;
            color: #333; /* Цвет текста */
        }
        .swal2-confirm {
            background-color: #d9534f; /* Цвет кнопки */
            border: none;
            border-radius: 4px;
        }
        .swal2-confirm:hover {
            background-color: #c9302c; /* Цвет кнопки при наведении */
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
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

        // Проверяем, содержит ли сообщение "You entered the wrong password."
        if (message === 'You entered the wrong password.') {
            htmlContent += `
                <br><br>
                <a href="/app/views/auth/form_forget.php" style="color: #007bff; text-decoration: underline;">Forgot your password?</a>
            `;
        }

        htmlContent += '</div>';

        Swal.fire({
            icon: 'error',
            title: 'Oops!',
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
                window.location.href = 'app/views/auth/form_login.php'; // Путь к форме логина
            }
        });
    });
</script>
</body>
</html>
