document.querySelectorAll('.btn-favorite').forEach(button => {
    button.addEventListener('click', function () {
        const articleId = this.dataset.articleId;

        fetch('/favourites/toggle', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `article_id=${articleId}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Переключаем класс, который отвечает за стиль
                    this.classList.toggle('added', data.action === 'added');
                    this.title = data.action === 'added'
                        ? 'Remove from Favorites'
                        : 'Add to Favorites';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    });
});
