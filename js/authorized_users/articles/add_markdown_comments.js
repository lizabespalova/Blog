
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
        {
            name: "insert-code",
            action: function customCodeInsert(editor) {
                let code = prompt("Enter your code:");
                if (code) {
                    let markdownCode = `\`\`\`\n${code}\n\`\`\``;
                    editor.codemirror.replaceSelection(markdownCode + "\n");
                }
            },
            className: "fa fa-code",
            title: "Insert Code"
        },
        {
            name: "insert-formula",
            action: function customFormulaInsert(editor) {
                let formula = prompt("Enter your formula (Example:a^2 + b^2 = c^2):");
                if (formula) {
                    let markdownFormula = `$$${formula}$$`;
                    editor.codemirror.replaceSelection(markdownFormula);
                }
            },
            className: "fa fa-superscript",
            title: "Insert Formula"
        },
        "|",
        "preview", // Предпросмотр
    ],
    previewRender: function(plainText) {

        // Обработка формул
        plainText = plainText.replace(/\$\$([\s\S]*?)\$\$/g, '<div class="formula-container">$1</div>');
        // Обработка спойлеров
        plainText = plainText.replace(/>\s*\[!spoiler\]\s*(.*)/g, '<details><summary>Spoiler</summary>$1</details>');

        return simplemde.markdown(plainText);
    },
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
