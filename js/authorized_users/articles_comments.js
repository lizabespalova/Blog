document.addEventListener('DOMContentLoaded', function () {
    const commentForm = document.querySelector('.add-comment-form');
    const commentsContainer = document.querySelector('.comments-container');

    commentForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const commentText = commentForm.querySelector('.comment-input').value;
        const articleSlug = commentForm.querySelector('.article-slug').value;
        const userId = commentForm.querySelector('.user-id').value;
        const parentId = commentForm.querySelector('.parent-id') ? commentForm.querySelector('.parent-id').value : null;

        // Отправка данных на сервер
        fetch('/articles/add_comment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                comment_text: commentText,
                article_slug: articleSlug,
                user_id: userId,
                parent_id: parentId
            }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Успешно добавлено — обновляем комментарии
                    alert('Comment added successfully!');
                    commentForm.reset(); // Очистка формы
                    loadComments(articleSlug); // Обновляем комментарии
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });

    // Функция для динамической загрузки комментариев
    function loadComments(articleSlug) {
        fetch('/articles/get_comments?article_slug=' + articleSlug)
            .then(response => response.text())
            .then(html => {
                commentsContainer.innerHTML = html;
            });
    }
    document.querySelectorAll('.btn-reply').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('.comment').querySelector('.reply-comment-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none'; // Показываем/скрываем форму
        });
    });
});
