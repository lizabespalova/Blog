// Функция для открытия/закрытия меню
function toggleSettingsMenu(event) {
    const settingsMenu = document.getElementById('settings-menu');

    // Переключаем класс для управления видимостью
    settingsMenu.classList.toggle('show');

    // Позиционируем меню рядом с кнопкой шестерёнки
    const buttonRect = event.target.getBoundingClientRect();
    settingsMenu.style.left = buttonRect.left + window.scrollX + 'px';  // Меню будет появляться рядом с кнопкой по горизонтали
    settingsMenu.style.top = buttonRect.bottom + window.scrollY + 5 + 'px';  // Меню будет располагаться сразу под кнопкой
}

// Добавляем обработчик клика по документу
document.addEventListener('click', function(event) {
    const settingsMenu = document.getElementById('settings-menu');
    const settingsButton = document.querySelector('.settings-btn');

    // Проверяем, был ли клик внутри меню или кнопки, если нет - скрываем меню
    if (!settingsMenu.contains(event.target) && event.target !== settingsButton) {
        settingsMenu.classList.remove('show');
    }
});
