document.addEventListener('DOMContentLoaded', function () {
    const selectedLanguage = document.getElementById('selected-language');
    const languageOptions = document.getElementById('language-options');
    const optionElements = document.querySelectorAll('.option');
    const selectedFlag = document.getElementById('selected-flag');
    const hiddenLanguageField = document.getElementById('language');

    // Проверяем, существуют ли основные элементы
    if (selectedLanguage && languageOptions && hiddenLanguageField) {
        // Открытие/закрытие списка языков
        selectedLanguage.addEventListener('click', function () {
            languageOptions.style.display = languageOptions.style.display === 'block' ? 'none' : 'block';
        });

        // Обработка выбора языка
        optionElements.forEach(option => {
            option.addEventListener('click', function () {
                const value = option.getAttribute('data-value');
                const flagCode = option.getAttribute('data-flag');
                const text = option.textContent.trim();

                // Обновляем ссылку флага в выбранном элементе
                if (selectedFlag) {
                    selectedFlag.src = `https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/${flagCode}.svg`;
                }

                // Обновляем текст в выбранном элементе
                selectedLanguage.innerHTML = `<img class="flag" src="https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/${flagCode}.svg" alt="Flag"> ${text}`;

                // Обновляем скрытое поле
                hiddenLanguageField.value = value;

                // Закрываем выпадающий список
                languageOptions.style.display = 'none';
            });
        });
    } else {
        console.warn('One or more required elements are missing: selected-language, language-options, or language.');
    }
});
