document.addEventListener('DOMContentLoaded', () => {
    const fontStyleSelect = document.getElementById('font-style');
    if (fontStyleSelect) {
        // Проверяем, если в localStorage сохранён выбранный шрифт
        const savedFontStyle = localStorage.getItem('font_style') || 'sans-serif'; // Значение по умолчанию 'sans-serif'

        // Применяем сохранённый стиль шрифта к body
        document.body.style.fontFamily = savedFontStyle;

        // Устанавливаем выбранный шрифт в селекторе
        fontStyleSelect.value = savedFontStyle;

        // Обработчик для изменения стиля шрифта
        fontStyleSelect.addEventListener('change', () => {
            const fontStyle = fontStyleSelect.value;

            // Устанавливаем новый стиль шрифта для body
            document.body.style.fontFamily = fontStyle;

            // Сохраняем выбранный шрифт в localStorage
            localStorage.setItem('font_style', fontStyle);

            // Отправляем данные о выбранном шрифте на сервер через fetch
            fetch('/settings/font-style', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `font_style=${fontStyle}`,
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(err => {
                    console.error('Error saving font style:', err);
                });
        });
    }
});
