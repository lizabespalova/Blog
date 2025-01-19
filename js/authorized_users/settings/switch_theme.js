document.querySelectorAll('.theme-radio').forEach(option => {
    option.addEventListener('change', function() {
        const selectedTheme = option.value;

        // Отправка данных на сервер
        fetch('/settings/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `theme=${selectedTheme}`,
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Применение новой темы на клиенте
                    document.body.classList.toggle('dark-mode', selectedTheme === 'dark');
                } else {
                    alert('Failed to save theme settings.');
                }
            })
            .catch(err => console.error('Ошибка:', err));
    });
});
