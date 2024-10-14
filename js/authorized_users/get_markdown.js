document.addEventListener("DOMContentLoaded", function() {
    const markdownContent = document.getElementById('rendered-content').innerHTML.trim();
    console.log(markdownContent);

    // Настройки Showdown-конвертера
    const converter = new showdown.Converter({
        literalMidWordUnderscores: true,
        simpleLineBreaks: true,
        tables: true
    });


    // Преобразуем Markdown в HTML
    let htmlContent = converter.makeHtml(markdownContent);

    // // Принудительно заменяем <code> теги, чтобы избежать ошибок с блоками кода
    // htmlContent = htmlContent.replace(/<code>/g, '<pre><code class="hljs">').replace(/<\/code>/g, '</code></pre>');

    // Обработка спойлеров
    htmlContent = htmlContent.replace(/\[!spoiler\]\s*(.*?)\s*(\n|$)/g, '<details><summary>Spoiler</summary>$1</details>');

    // Обработка формул
    htmlContent = htmlContent.replace(/\$\$([\s\S]*?)\$\$/g, '<div class="formula-container">$1</div>');

    // Обновляем содержимое элемента
    document.getElementById('rendered-content').innerHTML = htmlContent;

    // Запускаем подсветку кода
    document.querySelectorAll('pre code').forEach((block) => {
        // Подсветка кода
        hljs.highlightBlock(block);

        // Добавление кнопки копирования
        copy_to_clipboard(block);

        // Добавление нумерации строк
        add_line_numbers(block);    });

    // Инициализируем MathJax для формул
    if (typeof MathJax !== 'undefined') {
        MathJax.typeset();
    }
});

function add_line_numbers(block) {
    const lines = block.innerHTML.split('\n');
    const numberedLines = lines.map((line, index) => {
        return `<span class="hljs-line"><span class="hljs-line-numbers">${index + 1}</span>${line}</span>`;
    });
    block.innerHTML = numberedLines.join('\n');
}

// Функция копирования кода
function copy_to_clipboard(block) {
    const button = document.createElement('button');
    button.className = 'copy-code-btn';
    button.innerHTML = '<i class="fas fa-copy copy-icon"></i><span class="copy-text">Copy</span>';

    // Размещаем кнопку в правом верхнем углу блока <pre>
    block.parentElement.style.position = 'relative';
    button.style.position = 'absolute';
    button.style.top = '10px';
    button.style.right = '10px';
    block.parentElement.insertBefore(button, block);

    // Обработчик для копирования текста кода без номеров строк
    button.addEventListener('click', () => {
        // Извлекаем текст только из элемента <code>
        const code = block.textContent; // Получаем текст из элемента <code>

        navigator.clipboard.writeText(code).then(() => {
            // Меняем текст кнопки на "Скопировано!"
            button.classList.add('copied');
            button.querySelector('.copy-text').textContent = 'Copied!';

            // Через 2 секунды возвращаем текст "Copy"
            setTimeout(() => {
                button.classList.remove('copied');
                button.querySelector('.copy-text').textContent = 'Copy';
            }, 2000); // Показываем текст "Скопировано!" в течение 2 секунд
        }).catch(err => {
            console.error('Ошибка копирования: ', err);
        });
    });
}