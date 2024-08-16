function toggleMenu() {
    const menuContent = document.querySelector('.menu-content');
    menuContent.style.display = (menuContent.style.display === 'block' ? 'none' : 'block');
}

// Закрытие меню при клике вне его области
window.addEventListener('click', function(event) {
    const menu = document.querySelector('.menu');
    if (!menu.contains(event.target)) {
        const menuContent = document.querySelector('.menu-content');
        if (menuContent.style.display === 'block') {
            menuContent.style.display = 'none';
        }
    }
});
