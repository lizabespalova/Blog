let imageMap = {}; // Объект для хранения изображений

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

                        // Проверка формата и размера изображения
                        if (!isValidImage(file)) {
                            alert("Invalid photo format");
                            return;
                        }

                        if (!isValidSize(file)) {
                            alert("Maximum photo size is 5 MB");
                            return;
                        }

                        let reader = new FileReader();

                        reader.onload = function(e) {
                            let imageUrl = e.target.result;
                            let imageId = `image${Object.keys(imageMap).length + 1}`;
                            imageMap[imageId] = imageUrl;

                            let markdownImage = `[${imageId}]`; // Обновлено для вставки URL изображения
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
                    let code = prompt("Введите ваш код:");
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

            // Обработка изображений
            for (const [id, url] of Object.entries(imageMap)) {
                let imageTag = `<img src="${url}" alt="${id}" style="max-width: 100%; height: auto;">`;
                plainText = plainText.replace(new RegExp(`\\[${id}\\]`, 'g'), imageTag);
            }

            // Обработка таблиц
            const tableRegex = /((?:\|[^\n]+\|(?:\n|$))+)(\|[-:]+[-|:]*)(\n(?:\|[^\n]+\|(?:\n|$))*)+/g;
            plainText = plainText.replace(tableRegex, function(match) {
                return `<table style="width: 100%; border-collapse: collapse;">${match.replace(/\|/g, '<td>').replace(/(\n)/g, '</tr><tr>').replace(/<\/tr><tr>$/, '</tr></table>')}</tr>`;
            });

            // Обработка формул
            plainText = plainText.replace(/\$\$([\s\S]*?)\$\$/g, '<span class="formula">$1</span>');

            // Обработка спойлеров
            plainText = plainText.replace(/>\s*\[!spoiler\]\s*(.*)/g, '<details><summary>Spoiler</summary>$1</details>');

            return simplemde.markdown(plainText);
        }
    });
    //Вставка фото
    simplemde.codemirror.on("paste", function(editor, event) {
        let items = (event.clipboardData || window.clipboardData).items;

        for (let item of items) {
            if (item.type.includes("image")) {
                let file = item.getAsFile();
                let reader = new FileReader();

                reader.onload = function(e) {
                    let imageId = `image${Object.keys(imageMap).length + 1}`;
                    imageMap[imageId] = e.target.result;
                    simplemde.codemirror.replaceSelection(`[${imageId}]\n`);
                };

                reader.readAsDataURL(file);
            }
        }
    });

    document.querySelector('form').addEventListener('submit', function(event) {
        var contentField = document.getElementById("markdown-editor");
        let content = simplemde.value();

        for (const [id, url] of Object.entries(imageMap)) {
            content = content.replace(new RegExp(`\\[${id}\\]`, 'g'), `![${id}](${url})`);
        }

        contentField.value = content;
    });

    document.querySelector('.close').onclick = function() {
        document.getElementById('tableModal').style.display = 'none';
    };

});