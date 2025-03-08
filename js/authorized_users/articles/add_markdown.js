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
                        handleImageUpload(file, (imageMarkdown) => {
                            // Заменяем выделение в редакторе на правильный Markdown
                            simplemde.codemirror.replaceSelection(imageMarkdown + '\n');
                        });
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
                    let anchorName = prompt("Enter anchor name (up to 50 characters, you can use spaces and letters in any language):");

                    if (anchorName) {
                        // Trim extra spaces at the beginning and end, replace spaces with hyphens inside
                        anchorName = anchorName.trim().replace(/\s+/g, "-");

                        // Check the length of the anchor name
                        if (anchorName.length > 50) {
                            alert("Error: The anchor name must not exceed 50 characters.");
                            return;
                        }

                        // Ensure the anchor name contains only letters, numbers, spaces, hyphens, and underscores
                        if (!/^[a-zA-Zа-яА-ЯёЁ0-9\s_-]+$/.test(anchorName)) {
                            alert("Error: The anchor name can only contain letters, numbers, spaces, -, _");
                            return;
                        }

                        let markdownAnchor = `<a id="${anchorName}"></a>\n\n${anchorName}](#${anchorName})`;
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
                name: "emoji",
                action: function (editor) {
                    showEmojiPicker(editor);
                },
                className: "fa fa-smile-o",
                title: "Add smile"
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

            // Обработка якорей
            plainText = plainText.replace(/\[([^\]]+)\]\(#([^)]+)\)/g, '<a href="#$2">$1</a>');

            // Обработка изображений с использованием imageMap
            for (const [id, url] of Object.entries(imageMap)) {
                // Формируем тэг картинки с корректной ссылкой
                let imageTag = `<img src="${url}" alt="${id}" style="max-width: 100%; height: auto; border-radius: 8px;">`;
                plainText = plainText.replace(new RegExp(`\\!\\[${id}\\]\\(.*?\\)`, 'g'), imageTag);
            }
            // // Определяем базовый URL для сайта
            // const baseUrl = 'http://localhost:8080/';
            //
            // // Обработка изображений в формате ![image_id](url)
            // plainText = plainText.replace(/!\[(.*?)\]\((.*?)\)/g, function(match, altText, url) {
            //     // Убираем лишние изображения, если URL похожи (по сути это дубли)
            //     let cleanUrl = url.trim();
            //     if (cleanUrl.includes('!!')) {
            //         cleanUrl = cleanUrl.replace('!!', ''); // Убираем лишние префиксы
            //     }
            //
            //     // Если URL не абсолютный и не был уже обработан, добавляем базовый путь
            //     if (!/^https?:\/\//.test(cleanUrl) && !cleanUrl.includes('/uploads/')) {
            //         cleanUrl = baseUrl + cleanUrl.replace(/^\//, ''); // убираем ведущий /, если есть
            //     }
            //
            //     return `<img src="${cleanUrl}" alt="${altText}" style="max-width: 100%; height: auto; border-radius: 8px;">`;
            // });

            return simplemde.markdown(plainText);
        }
    });

    // Вставка фото
    simplemde.codemirror.on("paste", function(editor, event) {
        let items = (event.clipboardData || window.clipboardData).items;

        for (let item of items) {
            if (item.type.includes("image")) {
                let file = item.getAsFile();
                handleImageUpload(file, (imageId) => {
                    simplemde.codemirror.replaceSelection(`${imageId}\n`);
                });
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
    document.getElementById('articleForm').addEventListener('submit', function (event) {
        var form = this;
        var formData = new FormData(form);
        let content = simplemde.value(); // Получаем контент из редактора

        // Подставляем ссылки на изображения в контент статьи
        for (const [id, url] of Object.entries(imageMap)) {
            content = content.replace(new RegExp(`\\!\\[${id}\\]\\(.*?\\)`, 'g'), `![${id}](${url})`);
        }

        // Заменяем значение контента в форме
        document.getElementById("markdown-editor").value = content;

        // Добавляем файлы в FormData без дублирования
        let uniqueFiles = new Set(uploadedFiles.map(f => f.name)); // Фильтруем уникальные файлы по имени
        uniqueFiles.forEach(fileName => {
            let file = uploadedFiles.find(f => f.name === fileName);
            formData.append('article_images[]', file);
        });

    });
    // Функция проверки изображения
    function handleImageUpload(file, callback) {
        if (!file) return;

        if (uploadedFiles.length >= 5) {
            showError("You can upload up to 5 images only.");
            return;
        }

        if (!isValidImage(file)) {
            showError("Invalid file format. Only JPEG, PNG, or GIF images are allowed.");
            return;
        }

        if (!isValidSize(file)) {
            showError("File size exceeds the limit of 5 MB.");
            return;
        }

        // // Проверим, не загружено ли уже такое изображение
        // const existingImage = uploadedFiles.find(f => f.name === file.name);
        // if (existingImage) {
        //     showError("This image is already uploaded.");
        //     return;
        // }

        let reader = new FileReader();
        reader.onload = function(e) {
            let imageId = `image${Date.now()}`;
            imageMap[imageId] = e.target.result;
            uploadedFiles.push(file); // Добавляем только файл в массив
            let userId = document.getElementById("user_id").value;
            let articleId = document.getElementById("article_id").value;

            // Строим ссылку на изображение (можно использовать относительный путь или base64, если нужно)
            let imageUrl = `http://localhost:8080/uploads/${userId}/article_photos/${articleId}/${imageId}.jpg`;

            console.log(`Image ID: ${imageId}, Image URL: ${imageUrl}`);

            // Вызываем callback с уникальным ID изображения и ссылкой, чтобы использовать в редакторе
            callback(`![${imageId}](${imageUrl})`);
        };
        reader.readAsDataURL(file);
    }


    function showEmojiPicker(editor) {
        const toolbarButton = document.querySelector(".fa-smile-o");
        if (!toolbarButton) return;

        // Удаляем старый, если он уже есть
        let oldPicker = document.querySelector(".emoji-picker");
        if (oldPicker) oldPicker.remove();

        // Создаем новый контейнер со смайликами
        const emojiList = [
            "😃", "😂", "😍", "👍", "🔥", "🎉", "😎", "😜", "🤔", "🥺", "😢", "😇", "🙄", "🤩", "🤗", "😷", "😈", "💩",
            "🥳", "🥰", "🤡", "💪", "🖤", "💖", "🤰", "💘", "🦄", "🦋", "🍀", "🌈", "⭐", "🌟", "🌻", "🌹", "🌺", "🍓",
            "🍉", "🍇", "🍍", "🍒", "🍊", "🍋", "🍔", "🍕", "🌮", "🍣", "🍩", "🍪", "☕", "🍷", "🍻", "🥂", "🍾", "🍺",
            "🍻", "🍾", "🎂", "🍰", "🍦", "🍫", "🍬", "🍪", "🍿", "🎬", "🎮", "🎧", "🎼", "🎷", "🎸", "🎺", "🎻", "🎲",
            "🎯", "🎳", "🎮", "🏀", "⚽", "🏈", "🏉", "🎱", "🏆", "🥇", "🥈", "🥉", "🏅", "💍", "💎", "🕹️", "🎮", "🛸",
            "🚀", "🛶", "🚢", "⛵", "🚲", "🚶", "🏍️", "🚗", "🚙", "🚓", "🚕", "🚚", "🚛", "🚜", "🚔", "🚨", "🚓", "🚍",
            "🚘", "🚖", "🚃", "🚞", "🚉", "🚄", "🚅", "🚈", "🚂", "🛳️", "🛺", "🛴", "🚁", "✈️", "🛩️", "🚀", "🛸", "🛷",
            "🧳", "🎒", "💼", "💻", "🖥️", "📱", "⌨️", "🖱️", "💾", "🖥️", "🌐", "🔧", "🔒", "🔑", "🖋️", "🔋", "📡", "📶",
            "⚙️", "🤖", "💡", "🌟", "🔌", "🛰️", "🧑‍💻", "👩‍💻", "👨‍💻", "🔋", "💻", "🕹️", "👾", "🖥️", "💡", "🧠", "🖥️"
        ];

        let picker = document.createElement("div");
        picker.className = "emoji-picker";
        picker.style.position = "absolute";
        picker.style.background = "#fff";
        picker.style.border = "1px solid #ccc";
        picker.style.padding = "5px";
        picker.style.display = "flex";
        picker.style.flexWrap = "wrap";  // Чтобы смайлики располагались в несколько рядов
        picker.style.gap = "5px";
        picker.style.cursor = "pointer";
        picker.style.boxShadow = "0px 2px 10px rgba(0,0,0,0.1)";
        picker.style.borderRadius = "5px";
        picker.style.zIndex = "1000";

        // Позиционируем палитру под кнопкой
        let rect = toolbarButton.getBoundingClientRect();
        picker.style.top = rect.bottom + window.scrollY + "px";
        picker.style.left = rect.left + "px";

        emojiList.forEach(emoji => {
            let span = document.createElement("span");

            span.innerHTML = emoji;
            span.style.fontSize = "24px";
            span.style.cursor = "pointer";
            span.onclick = function () {
                editor.codemirror.replaceSelection(emoji);
                picker.remove();
            };
            picker.appendChild(span);
        });

        document.body.appendChild(picker);

        // Закрытие при клике вне палитры
        setTimeout(() => {
            document.addEventListener("click", function hidePicker(event) {
                if (!picker.contains(event.target) && !toolbarButton.contains(event.target)) {
                    picker.remove();
                    document.removeEventListener("click", hidePicker);
                }
            });
        }, 100);
    }



    function initializeImageMap() {
        let content = simplemde.value(); // Получаем текущее содержимое редактора
        let imageRegex = /!\[.*?\]\((https?:\/\/[^)]+)\)/g; // Регулярка для поиска изображений в Markdown

        uploadedFiles = []; // Используем массив для хранения файлов

        for (let match of content.matchAll(imageRegex)) {
            let imageUrl = match[1]; // URL изображения

            if (imageUrl) {
                uploadedFiles.push({ url: imageUrl }); // Добавляем объект с URL в массив
            }
        }
        console.log(uploadedFiles.length); // Выводим количество изображений в массиве
    }

// Вызываем функцию после инициализации редактора
    initializeImageMap();
});