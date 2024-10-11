const MAX_TAGS = 10;

document.getElementById('tags-input').addEventListener('keydown', function(event) {
    if (event.key === 'Enter' || event.key === ',') {
        event.preventDefault(); // Отключаем стандартное поведение Enter и запятой

        const input = this.value.trim();
        if (input) {
            const currentTags = getTags(); // Получаем текущие теги
            if (currentTags.length < MAX_TAGS) {
                addTag(input); // Добавляем тег
                this.value = ''; // Очищаем поле ввода
                updateTagsField(); // Обновляем скрытое поле с тегами
                document.getElementById('tag-warning').style.display = 'none'; // Скрываем предупреждение
            } else {
                document.getElementById('tag-warning').style.display = 'inline'; // Показываем предупреждение
            }
        }
    }
});

function getTags() {
    return Array.from(document.querySelectorAll('.tag')).map(tag => tag.textContent.replace('×', '').trim());
}

function addTag(tagText) {
    const tagContainer = document.querySelector('.tag-container');

    const tagElement = document.createElement('span');
    tagElement.classList.add('tag');
    tagElement.textContent = tagText;

    const removeButton = document.createElement('button');
    removeButton.classList.add('remove-tag');
    removeButton.innerHTML = '&times;';
    removeButton.onclick = function() {
        tagContainer.removeChild(tagElement);
        updateTagsField(); // Обновляем скрытое поле после удаления тега
    };

    tagElement.appendChild(removeButton);
    tagContainer.insertBefore(tagElement, document.getElementById('tags-input'));
    updateTagsField(); // Обновляем скрытое поле после добавления тега
}

function updateTagsField() {
    const tags = getTags();
    document.getElementById('tags').value = tags.join(','); // Обновляем значение скрытого поля
}
