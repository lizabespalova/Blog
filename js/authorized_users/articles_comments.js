document.addEventListener('DOMContentLoaded', function () {
    const commentForm = document.querySelector('.add-comment-form');
    const commentsContainer = document.querySelector('.comments-container');
    const maxVisibleComments = 3; // Количество видимых комментариев до нажатия "Show more"
    const maxVisibleReplies = 3; // Количество видимых ответов до нажатия "Show more replies"

    // Загружаем комментарии при открытии страницы
    const articleSlug = commentForm.querySelector('.article-slug').value;
    loadComments(articleSlug);

    commentForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const commentText = commentForm.querySelector('.comment-input').value;
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
                    loadComments(articleSlug); // Перезагружаем комментарии после добавления
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
                initializeCommentVisibility(); // Инициализируем видимость комментариев
                initializeReplyButtons();
                initializeToggleReplies();
            })
            .catch(error => {
                console.error('Ошибка загрузки комментариев:', error);
            });
    }

    function initializeCommentVisibility() {
        const comments = Array.from(commentsContainer.querySelectorAll('.comment'));

        // Показываем только первые maxVisibleComments
        comments.forEach((comment, index) => {
            comment.style.display = index < maxVisibleComments ? 'block' : 'none';
        });

        // Если есть больше комментариев, добавляем кнопку "Show more"
        if (comments.length > maxVisibleComments) {
            addShowMoreButton(comments, maxVisibleComments, 'Show more', 'Hide');
        }
    }

    function addShowMoreButton(comments, maxToShow, showText, hideText) {
        const existingButton = commentsContainer.querySelector('.btn-show-more');
        if (existingButton) {
            existingButton.remove();
        }

        const showMoreButton = document.createElement('button');
        showMoreButton.classList.add('btn-show-more');
        showMoreButton.textContent = showText;

        showMoreButton.addEventListener('click', function () {
            const currentlyHidden = comments.filter(comment => comment.style.display === 'none');

            if (currentlyHidden.length > 0) {
                currentlyHidden.slice(0, maxToShow).forEach(comment => {
                    comment.style.display = 'block';
                });

                if (currentlyHidden.length <= maxToShow) {
                    showMoreButton.textContent = hideText;
                }
            } else {
                comments.forEach((comment, index) => {
                    comment.style.display = index < maxVisibleComments ? 'block' : 'none';
                });
                showMoreButton.textContent = showText;
            }
        });

        commentsContainer.appendChild(showMoreButton);
    }

    function addReply(replyForm) {
        const replyText = replyForm.querySelector('.reply-input').value;
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
                    loadComments(articleSlug); // Перезагружаем комментарии после добавления
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    function initializeReplyButtons() {
        document.querySelectorAll('.btn-reply').forEach(button => {
            button.addEventListener('click', function () {
                const form = this.closest('.comment').querySelector('.reply-comment-form');
                form.style.display = form.style.display === 'none' ? 'block' : 'none';
            });
        });

        document.querySelectorAll('.reply-comment-form').forEach(replyForm => {
            replyForm.addEventListener('submit', function (e) {
                e.preventDefault();
                addReply(replyForm);
            });
        });
    }

    function initializeToggleReplies() {
        document.querySelectorAll('.btn-toggle-replies').forEach(button => {
            button.addEventListener('click', function () {
                const repliesContainer = this.closest('.comment').querySelector('.replies-container');
                const replies = Array.from(repliesContainer.querySelectorAll('.reply'));
                const toggleButton = this;

                if (repliesContainer.style.display === 'none' || repliesContainer.style.display === '') {
                    repliesContainer.style.display = 'block';
                    toggleButton.textContent = '⮝'; // Меняем текст кнопки
                    showInitialReplies(replies);
                } else {
                    repliesContainer.style.display = 'none';
                    toggleButton.textContent = '⮟'; // Меняем текст кнопки
                }
            });
        });
    }

    function showInitialReplies(replies) {
        replies.forEach((reply, index) => {
            reply.style.display = index < maxVisibleReplies ? 'block' : 'none';
        });

        if (replies.length > maxVisibleReplies) {
            addShowMoreRepliesButton(replies);
        }
    }

    function addShowMoreRepliesButton(replies) {
        const existingButton = commentsContainer.querySelector('.btn-show-more-replies');
        if (existingButton) {
            existingButton.remove();
        }

        const showMoreButton = document.createElement('button');
        showMoreButton.classList.add('btn-show-more-replies');
        showMoreButton.textContent = 'Show more replies';

        showMoreButton.addEventListener('click', function () {
            const currentlyHidden = replies.filter(reply => reply.style.display === 'none');

            if (currentlyHidden.length > 0) {
                currentlyHidden.slice(0, maxVisibleReplies).forEach(reply => {
                    reply.style.display = 'block';
                });

                if (currentlyHidden.length <= maxVisibleReplies) {
                    showMoreButton.textContent = 'Hide replies';
                }
            } else {
                replies.forEach((reply, index) => {
                    reply.style.display = index < maxVisibleReplies ? 'block' : 'none';
                });
                showMoreButton.textContent = 'Show more replies';
            }
        });

        commentsContainer.appendChild(showMoreButton);
    }
});
