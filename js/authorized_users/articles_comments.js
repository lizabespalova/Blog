document.addEventListener('DOMContentLoaded', function () {
    const commentForm = document.querySelector('.add-comment-form');
    const commentsContainer = document.querySelector('.comments-container');

    commentForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const commentText = commentForm.querySelector('.comment-input').value;
        const articleSlug = commentForm.querySelector('.article-slug').value;
        const userId = commentForm.querySelector('.user-id').value;

        fetch('/articles/add_comment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                comment_text: commentText,
                article_slug: articleSlug,
                user_id: userId
            }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    commentForm.reset();
                    loadComments(articleSlug);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });

    function loadComments(articleSlug) {
        fetch('/articles/get_comments?article_slug=' + articleSlug)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                commentsContainer.innerHTML = html;
                initializeReplyButtons();
                initializeToggleReplies();
            })
            .catch(error => {
                console.error('Ошибка загрузки комментариев:', error);
            });
    }


    function initializeReplyButtons() {
        document.querySelectorAll('.btn-reply').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('.comment').querySelector('.reply-comment-form');
                form.style.display = form.style.display === 'none' ? 'block' : 'none';
            });
        });

        document.querySelectorAll('.reply-comment-form').forEach(replyForm => {
            replyForm.addEventListener('submit', function(e) {
                e.preventDefault();
                addReply(replyForm);
            });
        });
    }

    function initializeToggleReplies() {
        document.querySelectorAll('.btn-toggle-replies').forEach(button => {
            button.addEventListener('click', function() {
                const repliesContainer = this.closest('.comment').querySelector('.replies-container');
                const isVisible = repliesContainer.style.display === 'block';

                if (isVisible) {
                    repliesContainer.style.maxHeight = '0'; // Скрываем с анимацией
                } else {
                    repliesContainer.style.display = 'block'; // Сначала показываем
                    repliesContainer.style.maxHeight = repliesContainer.scrollHeight + 'px'; // Затем устанавливаем max-height
                }

                this.textContent = isVisible ? '⮟' : '⮝'; // Изменяем стрелку
            });
        });
    }


    function addReply(replyForm) {
        const replyText = replyForm.querySelector('.reply-input').value;
        const articleSlug = commentForm.querySelector('.article-slug').value;
        const userId = commentForm.querySelector('.user-id').value;
        const parentId = replyForm.getAttribute('data-parent-id');

        fetch('/articles/add_comment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                comment_text: replyText,
                article_slug: articleSlug,
                user_id: userId,
                parent_id: parentId
            }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    replyForm.reset();
                    loadComments(articleSlug);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    initializeReplyButtons();
    initializeToggleReplies();
});
