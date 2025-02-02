document.addEventListener('DOMContentLoaded', function () {
    const selectedLanguage = document.getElementById('selected-language');
    const languageOptions = document.getElementById('language-options');
    const optionElements = document.querySelectorAll('.option');
    const selectedFlag = document.getElementById('selected-flag');
    const hiddenLanguageField = document.getElementById('language');

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

                if (selectedFlag) {
                    selectedFlag.src = `https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/${flagCode}.svg`;
                }

                selectedLanguage.innerHTML = `<img class="flag" src="https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/${flagCode}.svg" alt="Flag"> ${text}`;
                hiddenLanguageField.value = value;
                languageOptions.style.display = 'none';

                // Отправляем новый язык на сервер
                fetch('/settings/change-language', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `language=${value}`,
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload(); // Перезагрузка страницы для применения языка
                        } else {
                            console.error('Failed to change language:', data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    } else {
        console.warn('One or more required elements are missing: selected-language, language-options, or language.');
    }
});
