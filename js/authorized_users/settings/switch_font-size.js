document.addEventListener('DOMContentLoaded', () => {
    const fontSizeSlider = document.getElementById('font-size');
    const fontSizeValue = document.getElementById('font-size-value'); // Элемент для отображения значения
    if(fontSizeSlider && fontSizeValue) {
        // Применение нового размера шрифта к body и обновление отображения значения при движении ползунка
        fontSizeSlider.addEventListener('input', () => {
            const fontSize = fontSizeSlider.value;

            // Обновление текста отображаемого значения
            fontSizeValue.textContent = `${fontSize}px`;

            // Изменение размера шрифта на странице
            document.body.style.fontSize = `${fontSize}px`;

            // Убираем все возможные классы с body
            document.body.classList.remove('small-font', 'large-font');

            // Добавляем новый класс в зависимости от значения ползунка
            if (fontSize < 14) {
                document.body.classList.add('small-font');
            } else if (fontSize > 18) {
                document.body.classList.add('large-font');
            }
        });

        // Сохранение нового размера шрифта на сервере через Ajax
        fontSizeSlider.addEventListener('change', () => {
            const fontSize = fontSizeSlider.value;

            fetch('/settings/font-size', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `font_size=${fontSize}`,
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(err => {
                    console.error('Error saving font size:', err);
                });
        });
    }
});
