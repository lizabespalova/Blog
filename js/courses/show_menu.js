// Функция для открытия/закрытия меню
function toggleSettingsMenu(event) {
    event.stopPropagation(); // Останавливаем всплытие события

    const settingsMenu = document.getElementById('settings-menu');

    // Переключаем класс для управления видимостью
    settingsMenu.classList.toggle('show');

    // Позиционируем меню рядом с кнопкой шестерёнки
    const buttonRect = event.target.getBoundingClientRect();
    settingsMenu.style.left = buttonRect.left + window.scrollX + 'px';
    settingsMenu.style.top = buttonRect.bottom + window.scrollY + 5 + 'px';
}

// Закрываем меню при клике вне его
document.addEventListener('click', function(event) {
    const settingsMenu = document.getElementById('settings-menu');
    const settingsButton = document.querySelector('.settings-btn');

    if (settingsMenu && settingsButton && !settingsMenu.contains(event.target) && !settingsButton.contains(event.target)) {
        settingsMenu.classList.remove('show');
    }
});

// Закрытие модального окна и возврат меню настроек
function closeVisibilityModal() {
    document.getElementById('visibilityModal').style.display = 'none';

    // Показываем меню настроек снова после закрытия модального окна
    document.getElementById('settings-menu').classList.add('show');
}
