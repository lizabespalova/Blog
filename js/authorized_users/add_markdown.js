let imageMap = {}; // Объект для хранения изображений
let uploadedFiles = []; // Массив для хранения загруженных файлов

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
                    let input = document.createElement("input");
                    input.type = "file";
                    input.accept = "image/*";

                    input.onchange = function() {
                        let file = input.files[0];

                        let reader = new FileReader();
                        reader.onload = function(e) {
                            let imageUrl = e.target.result;
                            let imageId = `image${Object.keys(imageMap).length + 1}`;
                            imageMap[imageId] = imageUrl;

                            // Сохраняем файл в массив с использованием его идентификатора
                            uploadedFiles.push(file); // Добавляем только файл в массив

                            let markdownImage = `[${imageId}]`;
                            editor.codemirror.replaceSelection(markdownImage + "\n");
                        };

                        reader.readAsDataURL(file);
                    };

                    input.click();
                },
                className: "fa fa-picture-o",
                title: "Upload image"
            },
            {
                name: "insert-link",
                action: function customLinkInsert(editor) {
                    let url = prompt("Enter the URL:");
                    let linkText = prompt("Enter the text for the link:");

                    if (url && linkText) {
                        let markdownLink = `[${linkText}](${url})`;
                        editor.codemirror.replaceSelection(markdownLink);
                    }
                },
                className: "fa fa-link",
                title: "Insert link"
            },

            {
                name: "insert-table",
                action: function customTableInsert(editor) {
                    document.getElementById('tableModal').style.display = 'block';

                    document.getElementById('insertTableBtn').onclick = function() {
                        let columns = parseInt(document.getElementById('columns').value);
                        let rows = parseInt(document.getElementById('rows').value);

                        if (rows > 20 || columns > 20 || columns < 1 || rows < 1) {
                            alert('Maximum allowed rows and columns is 20, minimum is 1');
                            return;
                        }

                        if (columns > 0 && rows > 0) {
                            let tableHeader = '| ' + 'Header '.repeat(columns).trim().replace(/ /g, ' | ') + ' |\n';
                            let tableDivider = '| ' + '--- '.repeat(columns).trim().replace(/ /g, ' | ') + ' |\n';
                            let tableBody = '';
                            for (let i = 0; i < rows; i++) {
                                tableBody += '| ' + 'Cell '.repeat(columns).trim().replace(/ /g, ' | ') + ' |\n';
                            }
                            let markdownTable = tableHeader + tableDivider + tableBody;
                            editor.codemirror.replaceSelection(markdownTable);
                        }

                        document.getElementById('tableModal').style.display = 'none';
                    };
                },
                className: "fa fa-table",
                title: "Insert table"
            },
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
            {
                name: "insert-anchor",
                action: function customAnchorInsert(editor) {
                    let anchorName = prompt("Name of anchor:");
                    if (anchorName) {
                        let markdownAnchor = `<a id="${anchorName}"></a>`;
                        editor.codemirror.replaceSelection(markdownAnchor);
                    }
                },
                className: "fa fa-anchor",
                title: "Insert Anchor"
            },
            {
                name: "insert-spoiler",
                action: function customSpoilerInsert(editor) {
                    let spoilerText = prompt("Enter your spoiler text:");
                    if (spoilerText) {
                        let markdownSpoiler = `> [!spoiler] ${spoilerText}\n`;
                        editor.codemirror.replaceSelection(markdownSpoiler);
                    }
                },
                className: "fa fa-eye-slash",
                title: "Insert Spoiler"
            },
            {
                name: "help",
                action: function() {
                    window.open('https://simplemde.com/markdown-guide', '_blank');
                },
                className: "fa fa-info-circle",
                title: "Instructions"
            },
            "preview", "side-by-side", "fullscreen"
        ],
        previewRender: function(plainText) {

            // Обработка таблиц
            const tableRegex = /((?:\|[^\n]+\|(?:\n|$))+)(\|[-:]+[-|:]*)(\n(?:\|[^\n]+\|(?:\n|$))*)+/g;
            plainText = plainText.replace(tableRegex, function(match) {
                return `<table style="width: 100%; border-collapse: collapse;">${match.replace(/\|/g, '<td>').replace(/(\n)/g, '</tr><tr>').replace(/<\/tr><tr>$/, '</tr></table>')}</tr>`;
            });

            // Обработка формул
            plainText = plainText.replace(/\$\$([\s\S]*?)\$\$/g, '<div class="formula-container">$1</div>');

            // Обработка спойлеров
            plainText = plainText.replace(/>\s*\[!spoiler\]\s*(.*)/g, '<details><summary>Spoiler</summary>$1</details>');


            // Обработка изображений
            for (const [id, url] of Object.entries(imageMap)) {
                let imageTag = `<img src="${url}" alt="${id}" style="max-width: 100%; height: auto; border-radius: 8px;">`;
                plainText = plainText.replace(new RegExp(`\\[${id}\\]`, 'g'), imageTag);
            }

            return simplemde.markdown(plainText);
        }
    });

    // Вставка фото
    simplemde.codemirror.on("paste", function(editor, event) {
        let items = (event.clipboardData || window.clipboardData).items;

        for (let item of items) {
            if (item.type.includes("image")) {
                let file = item.getAsFile();
                let reader = new FileReader();

                reader.onload = function(e) {
                    let imageId = `image${Object.keys(imageMap).length + 1}`;
                    imageMap[imageId] = e.target.result;
                    uploadedFiles.push(file); // Добавляем только файл в массив
                    simplemde.codemirror.replaceSelection(`[${imageId}]\n`);
                };

                reader.readAsDataURL(file);
            }
        }
    });
    // Закрытие модального окна при нажатии на крестик
    document.querySelector('.close').addEventListener('click', function() {
        document.getElementById('tableModal').style.display = 'none';
    });

    // Закрытие модального окна при клике вне его содержимого
    window.addEventListener('click', function(event) {
        if (event.target === document.getElementById('tableModal')) {
            document.getElementById('tableModal').style.display = 'none';
        }
    });
    document.getElementById('articleForm').addEventListener('submit', function(event) {
        var form = this;
        var formData = new FormData(form);  // Собираем данные формы
        let content = simplemde.value();  // Получаем контент из редактора

        // Подставляем ссылки на изображения в контент статьи
        for (const [id, url] of Object.entries(imageMap)) {
            content = content.replace(new RegExp(`\\[${id}\\]`, 'g'), `![${id}](${url})`);
        }

        // Заменяем значение контента в форме
        document.getElementById("markdown-editor").value = content;

        // Добавляем изображения в FormData для отправки на сервер
        uploadedFiles.forEach(item => {
            formData.append('article_images[]', item.file);
        });

    });

});
