// Функция для открытия формы репоста
function openRepostForm() {
    document.getElementById('repost-form').classList.add('show');
}

// Функция для закрытия формы репоста
function closeRepostForm() {
    document.getElementById('repost-form').classList.remove('show');
}

// Функция для отправки репоста
function submitRepost() {
    const message = document.getElementById('repost-message').value;
    const userId = document.getElementById('user-id').value;  // Получаем реальный userId
    const articleId = document.getElementById('article-id').value;  // Получаем реальный articleId
    console.log("message: " + message);
    console.log("userId: " + userId);
    console.log("articleId: " + articleId);

    if (message.trim() !== '') {
        // Отправка данных на сервер через fetch
        fetch('/repost', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                user_id: userId,
                article_id: articleId,
                message: message
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // alert('Repost successful!');
                    closeRepostForm();
                    displayRepost(message); // Отображаем репост на странице
                    // closeRepostForm(); // Закрываем форму
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('An error occurred: ' + error);
            });
    } else {
        alert('Please enter a message.');
    }
}

