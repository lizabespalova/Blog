document.addEventListener("DOMContentLoaded", function() {
    var markdownElement = document.getElementById('markdown-content');

    if (markdownElement) {
        var markdownContent = markdownElement.value;

        // Создаем элемент для отображения SimpleMDE
        var renderedElement = document.getElementById('rendered-content');

        // Используем существующее textarea для SimpleMDE
        var simplemde = new SimpleMDE({
            element: markdownElement,
            autoDownloadFontAwesome: false,
            initialValue: markdownContent,
            spellChecker: false,
            toolbar: false
        });

        // Рендерим Markdown в HTML и вставляем его в контейнер
        renderedElement.innerHTML = simplemde.options.previewRender(markdownContent);
    } else {
        console.warn("No markdown content element found.");
    }
});