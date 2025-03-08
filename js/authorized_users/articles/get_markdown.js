document.addEventListener("DOMContentLoaded", function() {
    processMarkdownContent();
});

function processMarkdownContent() {
    const markdownContents = Array.from(document.querySelectorAll('[id^="rendered-content-"]'));
    console.log("Найдено элементов с ID, начинающимся на 'rendered-content-':", markdownContents.length);

    markdownContents.forEach((markdownContent) => {
        if (markdownContent) {
            console.log(`Обработка: ${markdownContent.id}`);

            const content = markdownContent.innerHTML.trim();
            const htmlContent = convertMarkdownToHtml(content);
            const processedContent = processSpecialElements(htmlContent);

            markdownContent.innerHTML = processedContent;

            highlightCodeBlocks(markdownContent);

            console.log(`Обработка завершена: ${markdownContent.id}`);
        } else {
            console.warn(`Элемент не найден: ${markdownContent.id}`);
        }
    });
}

function convertMarkdownToHtml(content) {
    const converter = new showdown.Converter({
        literalMidWordUnderscores: true,
        simpleLineBreaks: true,
        tables: true
    });
    return converter.makeHtml(content);
}

function processSpecialElements(htmlContent) {
    // htmlContent = htmlContent.replace(/<img\s+[^>]*src="(\/?uploads\/[^"]+)"[^>]*>/g, (match, p1) => {
    //     let relativePath = p1.replace('/articles/', '/');
    //     let altText = 'Uploaded Image';
    //     return `<img src="/${relativePath}" alt="${altText}" />`;
    // });

    htmlContent = htmlContent.replace(/\[!spoiler\]\s*(.*?)\s*(\n|$)/g, '<details><summary>Spoiler</summary>$1</details>');
    htmlContent = htmlContent.replace(/\$\$([\s\S]*?)\$\$/g, (_, formula) => {
        return `<div class="formula-container">${formula}</div>`;
    });

    return htmlContent;
}

function highlightCodeBlocks(markdownContent) {
    markdownContent.querySelectorAll('pre code').forEach((block) => {
        hljs.highlightBlock(block);
        // addLineNumbers(block);
        addCopyButton(block);
    });
}

function addLineNumbers(block) {
    const lines = block.innerHTML.split('\n');
    const numberedLines = lines.map((line, index) => {
        return `<span class="hljs-line"><span class="hljs-line-numbers">${index + 1}</span>${line}</span>`;
    });
    block.innerHTML = numberedLines.join('\n');
}

function addCopyButton(block) {
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
