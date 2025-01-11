// Универсальная функция фильтрации
function filterSearch(inputSelector, cardSelector, textSelector) {
    const input = document.querySelector(inputSelector).value.toLowerCase();
    const cards = document.querySelectorAll(cardSelector);
    cards.forEach(card => {
        const text = card.querySelector(textSelector).textContent.toLowerCase();
        card.style.display = text.includes(input) ? "flex" : "none";
    });
}

// Проверяем, существует ли кнопка поиска и поле ввода
const searchButton = document.getElementById('searchButton');
const searchInput = document.querySelector('.searching-input');

// Связываем с кнопкой и полем ввода
if (searchButton && searchInput) {
    // Связываем с кнопкой
    searchButton.addEventListener('click', () => {
        filterSearch('.searching-input', '.follower-card', '.follower-login');
    });

    // Связываем с вводом текста
    searchInput.addEventListener('input', () => {
        filterSearch('.searching-input', '.follower-card', '.follower-login');
    });
} else {
    console.warn('Search button or input not found on the page.');
}

