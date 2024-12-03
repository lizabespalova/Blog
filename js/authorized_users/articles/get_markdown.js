document.addEventListener("DOMContentLoaded", function() {
    // 3 потому что начинаю с 3 ходить по репостам, решение не оптимальное, надо изменить
    let index = 3;
    const markdownContents = [
        document.getElementById('rendered-content-1'),
        document.getElementById('rendered-content-2'),
        document.getElementById(`rendered-content-${index + 1}`)
    ];

    markdownContents.forEach((markdownContent, index) => {
        // console.log(`Original content for rendered-content-${index + 1}:`, markdownContent);
        // console.log(`Original content for rendered-content-2:`, markdownContent.innerHTML);

        if (markdownContent) {
            console.log(`Обработка: rendered-content-${index + 1}`);

            // Извлекаем текстовое содержимое
            const content = markdownContent.innerHTML.trim();
            const converter = new showdown.Converter({
                literalMidWordUnderscores: true,
                simpleLineBreaks: true,
                tables: true
            });

            // Преобразуем Markdown в HTML
            let htmlContent = converter.makeHtml(content);

            // Обработка изображений
            htmlContent = htmlContent.replace(/<img\s+[^>]*src="(\/?uploads\/[^"]+)"[^>]*>/g, (match, p1) => {
                let relativePath = p1.replace('/articles/', '/');
                let altText = 'Uploaded Image';
                return `<img src="/${relativePath}" alt="${altText}" />`;
            });

            // Обработка спойлеров
            htmlContent = htmlContent.replace(/\[!spoiler\]\s*(.*?)\s*(\n|$)/g, '<details><summary>Spoiler</summary>$1</details>');

            // Обработка формул
            htmlContent = htmlContent.replace(/\$\$([\s\S]*?)\$\$/g, (_, formula) => {
                return `<div class="formula-container">${formula}</div>`;
            });
            console.log(`Processed HTML Content for rendered-content-${index + 1}:`, htmlContent);

            // Обновляем содержимое элемента
            markdownContent.innerHTML = htmlContent;

            // Запускаем подсветку кода
            markdownContent.querySelectorAll('pre code').forEach((block) => {
                hljs.highlightBlock(block);
                copy_to_clipboard(block);
                add_line_numbers(block);
            });

            console.log(`Обработка завершена: rendered-content-${index + 1}`);
        } else {
            console.warn(`Элемент не найден: rendered-content-${index + 1}`);
        }
    });
});

function add_line_numbers(block) {
    const lines = block.innerHTML.split('\n');
    const numberedLines = lines.map((line, index) => {
        return `<span class="hljs-line"><span class="hljs-line-numbers">${index + 1}</span>${line}</span>`;
    });
    block.innerHTML = numberedLines.join('\n');
}

function copy_to_clipboard(block) {
    const button = document.createElement('button');
    button.className = 'copy-code-btn';
    button.innerHTML = '<i class="fas fa-copy copy-icon"></i><span class="copy-text">Copy</span>';

    block.parentElement.style.position = 'relative';
    button.style.position = 'absolute';
    button.style.top = '10px';
    button.style.right = '10px';
    block.parentElement.insertBefore(button, block);

    button.addEventListener('click', () => {
        const code = block.textContent;

        navigator.clipboard.writeText(code).then(() => {
            button.classList.add('copied');
            button.querySelector('.copy-text').textContent = 'Copied!';
            setTimeout(() => {
                button.classList.remove('copied');
                button.querySelector('.copy-text').textContent = 'Copy';
            }, 2000);
        }).catch(err => {
            console.error('Ошибка копирования: ', err);
        });
    });
}
