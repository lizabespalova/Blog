// Универсальная функция для фильтрации и выделения текста
function filterSearch(inputSelector, cardSelector, textSelector) {
    const query = document.querySelector(inputSelector).value.toLowerCase().trim(); // Ввод текста
    const cards = document.querySelectorAll(cardSelector); // Все карточки

    // Если поле поиска пустое — показываем все карточки
    if (!query) {
        cards.forEach(card => {
            // Восстанавливаем исходное значение display из атрибута data-display
            if (card.dataset.display) {
                card.style.display = card.dataset.display;
            } else {
                card.style.display = ''; // Используем значение по умолчанию
            }

            // Убираем выделение, если есть
            card.querySelectorAll('.highlight').forEach(el => {
                el.outerHTML = el.innerText; // Убираем span и восстанавливаем текст
            });
        });
        return; // Завершаем выполнение функции
    }

    // Если поле поиска не пустое — фильтруем карточки
    cards.forEach(card => {
        // Получаем текст из указанных элементов карточки
        const searchableText = Array.from(card.querySelectorAll(textSelector))
            .map(field => field.textContent.toLowerCase())
            .join(' ');

        // Если текст карточки содержит запрос
        if (searchableText.includes(query)) {
            // Восстанавливаем исходное значение display из атрибута data-display
            if (card.dataset.display) {
                card.style.display = card.dataset.display;
            } else {
                card.style.display = ''; // Используем значение по умолчанию
            }
            highlightText(card, query); // Выделяем совпавший текст
        } else {
            card.style.display = 'none'; // Скрываем карточку
        }
    });
}


// Функция для выделения найденного текста
function highlightText(card, query) {
    // Удаляем старые выделения
    card.querySelectorAll('.highlight').forEach(el => {
        el.outerHTML = el.innerText; // Убираем span и восстанавливаем текст
    });

    // Преобразуем запрос в регулярное выражение
    const regex = new RegExp(`(${query})`, 'gi');

    // Перебираем элементы, которые содержат текст
    const textElements = card.querySelectorAll('.card-title, .card-meta');

    textElements.forEach(el => {
        // Подсвечиваем текст
        el.innerHTML = el.textContent.replace(regex, '<span class="highlight">$1</span>');
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
        filterSearch('.searching-input', '.card', '.card-title, .card-meta, .additional-info');
    });
} else {
    console.warn('Search button or input not found.');
}
