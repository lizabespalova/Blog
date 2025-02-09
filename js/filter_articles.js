// Универсальная функция для фильтрации и выделения текста
function filterSearch(inputSelector, cardSelectors, textSelectors) {
    const query = document.querySelector(inputSelector).value.toLowerCase().trim();
    const cards = document.querySelectorAll(cardSelectors);

    if (!query) {
        cards.forEach(card => {
            if (card.dataset.display) {
                card.style.display = card.dataset.display;
            } else {
                card.style.display = '';
            }
            card.querySelectorAll('.highlight').forEach(el => {
                el.outerHTML = el.innerText;
            });
        });
        return;
    }

    cards.forEach(card => {
        const searchableText = Array.from(card.querySelectorAll(textSelectors))
            .map(field => field.textContent.toLowerCase())
            .join(' ');

        if (searchableText.includes(query)) {
            if (card.dataset.display) {
                card.style.display = card.dataset.display;
            } else {
                card.style.display = '';
            }
            highlightText(card, query);
        } else {
            card.style.display = 'none';
        }
    });
}

// Функция для выделения найденного текста
function highlightText(card, query) {
    card.querySelectorAll('.highlight').forEach(el => {
        el.outerHTML = el.innerText;
    });

    const regex = new RegExp(`(${query})`, 'gi');
    const textElements = card.querySelectorAll('h3, p');

    textElements.forEach(el => {
        el.innerHTML = el.textContent.replace(regex, '<span class="highlight">$1</span>');
    });
}

// Подключаем обработчики событий
const searchButton = document.getElementById('searchButton');
const searchInput = document.querySelector('.searching-input');

if (searchButton && searchInput) {
    searchButton.addEventListener('click', () => {
        filterSearch('.searching-input', '.card, .writer-card', '.card-title, .card-meta, h3, p');
    });

    searchInput.addEventListener('input', () => {
        filterSearch('.searching-input', '.card, .writer-card', '.card-title, .card-meta, h3, p');
    });
} else {
    console.warn('Search button or input not found.');
}
