// Универсальная функция для фильтрации и выделения текста
function filterSearch(inputSelector, cardSelector, textSelector) {
    const query = document.querySelector(inputSelector).value.toLowerCase(); // Ввод текста
    const cards = document.querySelectorAll(cardSelector); // Все карточки

    cards.forEach(card => {
        // Получаем текст из указанных элементов карточки
        const searchableText = Array.from(card.querySelectorAll(textSelector))
            .map(field => field.textContent.toLowerCase())
            .join(' ');

        // Если текст карточки содержит запрос
        if (searchableText.includes(query)) {
            card.style.display = 'block'; // Показываем карточку
            // Выделяем совпавший текст
            highlightText(card, query);
        } else {
            card.style.display = 'none'; // Скрываем карточку
        }
    });
}

// Функция для выделения найденного текста
function highlightText(card, query) {
    // Сбрасываем предыдущие выделения
    card.querySelectorAll('.highlight').forEach(el => {
        el.classList.remove('highlight');
        el.innerHTML = el.innerText; // Восстанавливаем исходный текст
    });

    // Преобразуем query в регулярное выражение для поиска
    const regex = new RegExp(`(${query})`, 'gi');

    // Находим все элементы с текстом в карточке
    const textElements = card.querySelectorAll('.card-title, .card-meta');

    textElements.forEach(el => {
        // Выделяем совпадения в тексте, не меняя остальной HTML
        const newText = el.innerHTML.replace(regex, (match) => {
            return `<span class="highlight">${match}</span>`; // Добавляем выделение
        });
        el.innerHTML = newText; // Обновляем HTML
    });
}

// Обработчики событий для кнопки и поля ввода
const searchButton = document.getElementById('searchButton');
const searchInput = document.querySelector('.searching-input');

// Если кнопка и поле ввода существуют
if (searchButton && searchInput) {
    // При клике на кнопку поиска
    searchButton.addEventListener('click', () => {
        filterSearch('.searching-input', '.card', '.card-title, .card-meta');
    });

    // При каждом вводе текста в поле поиска
    searchInput.addEventListener('input', () => {
        filterSearch('.searching-input', '.card', '.card-title, .card-meta');
    });
} else {
    console.warn('Search button or input not found.');
}
