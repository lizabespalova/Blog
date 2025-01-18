document.addEventListener('DOMContentLoaded', function() {
    // Открытие/закрытие списка языков
    document.getElementById('selected-language').addEventListener('click', function() {
        const options = document.getElementById('language-options');
        options.style.display = options.style.display === 'block' ? 'none' : 'block';
    });

    // Обработка выбора языка
    const optionElements = document.querySelectorAll('.option');
    optionElements.forEach(option => {
        option.addEventListener('click', function() {
            const value = option.getAttribute('data-value');
            const flagCode = option.getAttribute('data-flag');
            const text = option.textContent.trim();

            // Обновляем ссылку флага в выбранном элементе
            const selectedFlag = document.getElementById('selected-flag');
            if (selectedFlag) {
                selectedFlag.src = `https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/${flagCode}.svg`;
            }

            // Обновляем текст в выбранном элементе
            const selectedLanguage = document.getElementById('selected-language');
            if (selectedLanguage) {
                selectedLanguage.innerHTML = `<img class="flag" src="https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/${flagCode}.svg" alt="Flag"> ${text}`;
            }

            // Обновляем скрытое поле
            document.getElementById('language').value = value;

            // Закрываем выпадающий список
            document.getElementById('language-options').style.display = 'none';
        });
    });
});
