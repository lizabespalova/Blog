document.addEventListener('DOMContentLoaded', function() {
    var simplemde = new SimpleMDE({
        element: document.getElementById("markdown-editor"),
        placeholder: "Write your article here...",
        spellChecker: false,
        autosave: {
            enabled: true,
            uniqueId: "MyUniqueID",
            delay: 1000
        }
    });

    // Обработка отправки формы
    document.querySelector('form').addEventListener('submit', function(event) {
        // Убедиться, что содержимое редактора синхронизировано с textarea
        var contentField = document.getElementById("markdown-editor");
        contentField.value = simplemde.value(); // Синхронизировать содержимое SimpleMDE с textarea
    });
});