// Подсчет введенных символов
const messageInput = document.getElementById('repost-message');
const charCount = document.getElementById('repost-char-count');
const maxLength = 250;
if (messageInput) {
    messageInput.addEventListener('input', () => {
        if (messageInput.value.length > maxLength) {
            // Убираем лишние символы
            messageInput.value = messageInput.value.substring(0, maxLength);
        }
        // Обновляем счетчик символов
        charCount.textContent = `${messageInput.value.length}/${maxLength}`;
    });
}

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
                // displayRepost(message); // Отображаем репост на странице
                // closeRepostForm(); // Закрываем форму
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('An error occurred: ' + error);
        });
}
// Функция для удаления репоста
function deleteRepost(button) {
    const repostId = button.getAttribute('data-repost-id');

    if (repostId && confirm('Are you sure you want to delete this repost?')) {
        fetch('/repost-delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `repost_id=${encodeURIComponent(repostId)}`,
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Failed to connect to the server.');
                }
                return response.json();
            })
            .then((data) => {
                if (data.success) {
                    const repostContainer = document.querySelector('.reposts-articles-container');
                    if (repostContainer) {
                        repostContainer.innerHTML = ''; // Очистить контейнер
                        location.reload(); // Обновить страницу (если хотите перезагрузить страницу, если хотите)
                    } else {
                        console.warn('Could not find the card element for deletion.');
                    }
                } else {
                    alert(data.error || 'Failed to delete the repost.');
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('An error occurred while deleting the repost.');
            });
    }
}
function viewStatistics(articleId) {
    // Перенаправление на страницу со статистикой статьи
    window.location.href = `/articles/statistics/${articleId}`;
}
