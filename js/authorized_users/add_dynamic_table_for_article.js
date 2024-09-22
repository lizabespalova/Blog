document.getElementById('insertTableBtn').addEventListener('click', function() {
    document.getElementById('tableModal').style.display = 'block';
});

document.querySelector('.close').addEventListener('click', function() {
    document.getElementById('tableModal').style.display = 'none';
});

// document.getElementById('create-table').addEventListener('click', function() {
//     const rows = parseInt(document.getElementById('rows').value);
//     const columns = parseInt(document.getElementById('columns').value);
//     let markdownTable = '';
//     console.log(`Rows: ${rows}, Columns: ${columns}`);
//     if (rows > 20 || columns > 20) {
//         alert('Maximum allowed rows and columns is 20.');
//         return;
//     }
//     // Создание заголовка
//     markdownTable += '| ' + 'Заголовок '.repeat(columns).trim().replace(/ /g, ' | ') + ' |\n';
//     markdownTable += '| ' + '--- '.repeat(columns).trim().replace(/ /g, ' | ') + ' |\n';
//
//     // Создание строк
//     for (let i = 0; i < rows; i++) {
//         markdownTable += '| ' + 'Ячейка '.repeat(columns).trim().replace(/ /g, ' | ') + ' |\n';
//     }
//
//     // Вставляем таблицу в редактор
//     simplemde.codemirror.replaceSelection(markdownTable);
//     document.getElementById('table-modal').style.display = 'none'; // Закрываем модальное окно
// });
