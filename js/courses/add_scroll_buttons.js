// Находим кнопки и контейнер
const leftButton = document.querySelector('.scroll-btn.left');
const rightButton = document.querySelector('.scroll-btn.right');
const articlesContainer = document.querySelector('.articles');

// Функции для прокрутки
leftButton.addEventListener('click', () => {
        articlesContainer.scrollBy({
                left: -200,  // Прокрутка влево на 200px
                behavior: 'smooth'  // Плавная прокрутка
        });
});

rightButton.addEventListener('click', () => {
        articlesContainer.scrollBy({
                left: 200,  // Прокрутка вправо на 200px
                behavior: 'smooth'  // Плавная прокрутка
        });
});
