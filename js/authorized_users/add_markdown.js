document.addEventListener('DOMContentLoaded', function() {
    var simplemde = new SimpleMDE({
        element: document.getElementById("markdown-editor"),
        placeholder: "Write your article here...",
        spellChecker: false,
        autosave: {
            enabled: true,
            uniqueId: "MyUniqueID",
            delay: 1000
        },
        toolbar: [
            "bold", "italic", "heading", "|", "quote", "unordered-list", "ordered-list", "|",
            {
                name: "upload-image",
                action: function customImageUpload(editor) {
                    // Создаем input для выбора файла
                    let input = document.createElement("input");
                    input.type = "file";
                    input.accept = "image/*";

                    input.onchange = function() {
                        let file = input.files[0];
                        let formData = new FormData();
                        formData.append("file", file);

                        // Отправляем файл на сервер
                        fetch('/upload_image.php', { // Этот URL нужно заменить на ваш реальный обработчик на сервере
                            method: 'POST',
                            body: formData
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Вставляем ссылку на изображение в редактор
                                    let imageUrl = data.url;
                                    editor.value(editor.value() + `![Image](${imageUrl})\n`);
                                } else {
                                    alert('Error uploading image: ' + data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Ошибка:', error);
                                alert('Error while uploading image.');
                            });
                    };

                    input.click();
                },
                className: "fa fa-picture-o",
                title: "Upload image"
            },
            "preview", "side-by-side", "fullscreen"
        ]
    });

    // Обработка отправки формы
    document.querySelector('form').addEventListener('submit', function(event) {
        // Убедиться, что содержимое редактора синхронизировано с textarea
        var contentField = document.getElementById("markdown-editor");
        contentField.value = simplemde.value(); // Синхронизировать содержимое SimpleMDE с textarea
    });
});
