document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filter-form');
    console.log(form.action)
    const resultsContainer = document.getElementById('filter-results');

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        // Собираем данные формы
        const formData = new FormData(form);
        const queryString = new URLSearchParams(formData).toString();
        console.log(queryString);
        // Получаем путь действия из атрибута action формы
        const actionUrl = form.getAttribute('action');
        console.log(actionUrl);

        // Отправляем запрос с параметрами
        fetch(`${actionUrl}?${queryString}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then((response) => response.text())
            .then((data) => {
                try {
                    // Преобразуем строку в JSON
                    const jsonData = JSON.parse(data);

                    // Если data - это массив, проверяем его длину
                    if (Array.isArray(jsonData.data)) {
                        if (jsonData.data.length === 0) {
                            // Если массив пустой, показываем сообщение "Ничего не найдено"
                            resultsContainer.innerHTML = '<p>No results found.</p>';
                        } else {
                            resultsContainer.innerHTML = renderResults(jsonData.data);
                        }
                    } else {
                        throw new Error('Expected data to be an array');
                    }
                } catch (error) {
                    console.error('Error parsing JSON:', error);
                    resultsContainer.innerHTML = '<p>An error occurred while filtering data.</p>';
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                resultsContainer.innerHTML = '<p>An error occurred while filtering data.</p>';
            });
    });

    function renderResults(data) {
        return data
            .map((article) => {
                return `
                <div class="card">
                    <div class="card-image">
                        <img src="/${article.cover_image}" alt="${article.title}">
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">
                            <a href="/articles/${article.slug}">
                                ${article.title}
                            </a>
                        </h3>
                        <p class="card-meta">
                            Author: ${article.author} | Date: ${new Date(article.created_at).toLocaleDateString()}
                        </p>
                    </div>
                </div>
                `;
            })
            .join('');
    }
});
