document.addEventListener('DOMContentLoaded', function () {
    const clearForm = document.getElementById('clearNotificationsForm');
    clearForm?.addEventListener('submit', function (e) {
        e.preventDefault(); // отменить стандартную отправку формы

        if (!confirm('Are you sure you want to clear all notifications?')) return;

        const formData = new FormData(clearForm);

        fetch(clearForm.action, {
            method: 'POST',
            body: formData,
        })
            .then(response => {
                if (response.ok) {
                    // Успешно — перезагружаем страницу
                    location.reload();
                } else {
                    alert('Failed to clear notifications.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error while clearing notifications.');
            });
    });
});