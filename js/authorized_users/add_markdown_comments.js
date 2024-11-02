// Инициализация SimpleMDE
const simplemde = new SimpleMDE({
    element: document.getElementById("markdown-comment-input"),
    toolbar: [
        "bold",            // Жирный текст
        "italic",          // Наклонный текст
        "strikethrough",   // Зачеркнутый текст
        "heading",         // Заголовки
        "|",
        "quote",           // Цитата
        "unordered-list",  // Маркированный список
        "ordered-list",    // Нумерованный список
        "|",
        "link",            // Ссылка
        "|",
        "preview",         // Предпросмотр
    ],
    spellChecker: false,
});

// Максимальное количество символов
const maxChars = 500;
const charCountElement = document.querySelector('.char-count');

// Обновление счётчика символов и визуального состояния
function updateCharCountDisplay(charCount) {
    charCountElement.textContent = `${charCount}/${maxChars}`;
    if (charCount >= maxChars) {
        charCountElement.style.color = 'red';
        simplemde.codemirror.setOption("readOnly", "nocursor");
    } else {
        charCountElement.style.color = '';
        simplemde.codemirror.setOption("readOnly", false);
    }
}

// Функция, которая отслеживает изменение текста и вызывает обновление счётчика
function handleTextChange() {
    const charCount = simplemde.value().length;
    updateCharCountDisplay(charCount);
}

// Инициализация и отслеживание изменений текста
function initCharCount() {
    handleTextChange(); // Отображение начального состояния
    simplemde.codemirror.on("change", handleTextChange);
}

// Запуск функции инициализации
initCharCount();
