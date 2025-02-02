document.querySelectorAll('.logout-session').forEach(button => {
    button.addEventListener('click', function() {
        const sessionId = this.getAttribute('data-session-id');

        // Отправляем AJAX-запрос для удаления сессии
        fetch('/settings/delete-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ session_id: sessionId })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Удаляем карточку сессии из DOM
                    this.closest('.session-card').remove();
                    window.location.href = "/success?message=" + 'Session closed successfully';

                    // alert('Session closed successfully');
                } else {
                    window.location.href = "/error?message=" + 'Failed to close session';

                    // alert('Failed to close session');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error closing session');
            });
    });
});