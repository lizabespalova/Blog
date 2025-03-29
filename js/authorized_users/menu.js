function updateSocialLinks() {
    const currentUrl = window.location.href;

    // Список всех ссылок для обновления
    const socialLinks = [
        { id: 'share-facebook', baseUrl: 'https://www.facebook.com/sharer/sharer.php?u=' },
        { id: 'share-twitter', baseUrl: 'https://twitter.com/intent/tweet?url=' },
        { id: 'share-telegram', baseUrl: 'https://t.me/share/url?url=' },
        { id: 'share-linkedin', baseUrl: 'https://www.linkedin.com/sharing/share-offsite/?url=' },
        { id: 'share-whatsapp', baseUrl: 'https://api.whatsapp.com/send?text=' }
    ];

    // Проходим по каждому объекту и обновляем ссылку
    socialLinks.forEach(link => {
        const element = document.getElementById(link.id);
        if (element) {
            // Для Telegram и WhatsApp добавляем корректный формат текста и ссылки
            if (link.id === 'share-telegram' || link.id === 'share-whatsapp') {
                element.href = link.baseUrl + encodeURIComponent(currentUrl) + "&text=" + encodeURIComponent("Check this out!");
            } else {
                element.href = link.baseUrl + encodeURIComponent(currentUrl);
            }
        }
    });
}

// Вызываем метод при загрузке страницы
updateSocialLinks();
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
