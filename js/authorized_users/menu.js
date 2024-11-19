function toggleMenu(button) {
    // Находим меню, связанное с кнопкой
    const menu = button.closest('.menu').querySelector('.menu-content');
    const allMenus = document.querySelectorAll('.menu-content');

    // Закрываем все открытые меню
    allMenus.forEach((menuItem) => {
        if (menuItem !== menu) {
            menuItem.classList.remove('show');
        }
    });

    // Переключаем видимость текущего меню
    menu.classList.toggle('show');
}

// Закрытие меню при клике вне его области
window.addEventListener('click', function (event) {
    const allMenus = document.querySelectorAll('.menu-content');
    allMenus.forEach((menu) => {
        if (!menu.closest('.menu').contains(event.target)) {
            menu.classList.remove('show');
        }
    });
});

// При скролле плавно убирать меню
let lastScrollY = window.scrollY;

window.addEventListener('scroll', () => {
    const menuContent = document.querySelector('.header-menu .menu-content');

    if (menuContent && menuContent.classList.contains('show')) {
        if (window.scrollY > lastScrollY) {
            // Пользователь прокручивает вниз, скрываем меню
            menuContent.classList.remove('show');
        }
    }

    lastScrollY = window.scrollY;
});
