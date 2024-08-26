function toggleMenu() {
    const menuContent = document.querySelector('.menu-content');
    menuContent.classList.toggle('show');
}

// Закрытие меню при клике вне его области
window.addEventListener('click', function(event) {
    const menu = document.querySelector('.menu');
    const menuContent = document.querySelector('.menu-content');
    if (!menu.contains(event.target)) {
        if (menuContent.classList.contains('show')) {
            menuContent.classList.remove('show');
        }
    }
});
