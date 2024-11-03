document.addEventListener('DOMContentLoaded', function () {
    const commentForm = document.querySelector('.add-comment-form');
    const commentsContainer = document.querySelector('.comments-container');
    const maxVisibleComments = 3;
    const maxVisibleReplies = 3;
    const articleSlug = commentForm.querySelector('.article-slug').value;
    const commentInput = document.querySelector('.comment-input');
    // const charCountDisplay = document.querySelector('.char-count');
    // const maxLengthLimit = 500; // Лимит на символы в форме для комментариев

    loadComments(articleSlug);

    commentForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const commentText = commentInput.value;
        const userId = commentForm.querySelector('.user-id').value;

        fetch('/articles/add_comment', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ comment_text: commentText, article_slug: articleSlug, user_id: userId }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) loadComments(articleSlug);
            });
    });

    // //Проверка формы комментариев на количество символов
    // document.querySelector('.add-comment-form').addEventListener('submit', function (event) {
    //     if (commentInput.value.length > maxLengthLimit) {
    //         event.preventDefault(); // Остановите отправку формы
    //         alert(`The comment must not exceed ${maxLengthLimit} characters.`);
    //     }
    // });
    //
    // commentInput.addEventListener('input', function () {
    //     const usedChars = commentInput.value.length;
    //     charCountDisplay.textContent = `${usedChars}/500`; // Обновляем счётчик символов
    // });

    function loadComments(articleSlug) {
        fetch('/articles/get_comments?article_slug=' + articleSlug)
            .then(response => response.text())
            .then(html => {
                commentsContainer.innerHTML = html;
                initializeCommentVisibility();
                initializeReplyButtons();
                initializeToggleReplies();
            })
            .catch(error => console.error('Error loading comments:', error));
    }

    function initializeCommentVisibility() {
        const comments = Array.from(commentsContainer.querySelectorAll('.comment'));
        comments.forEach((comment, index) => comment.style.display = index < maxVisibleComments ? 'block' : 'none');
        if (comments.length > maxVisibleComments) addShowMoreButton(comments, maxVisibleComments, 'Show more', 'Hide');
    }

    function postReply(replyForm) {
        const replyText = replyForm.querySelector('.reply-input').value;
        const userId = commentForm.querySelector('.user-id').value;
        const parentId = replyForm.getAttribute('data-parent-id');

        fetch('/articles/add_comment', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ comment_text: replyText, article_slug: articleSlug, user_id: userId, parent_id: parentId }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    replyForm.reset();//не работает
                    loadComments(articleSlug);
                }
            })
            .catch(error => console.error('Error adding reply:', error));
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
                postReply(replyForm);
            });
        });
    }

    function initializeToggleReplies() {
        document.querySelectorAll('.btn-toggle-replies').forEach(button => {
            button.addEventListener('click', function () {
                const repliesContainer = this.closest('.comment').querySelector('.replies-container');
                if (repliesContainer) {
                    repliesContainer.style.display = repliesContainer.style.display === 'none' ? 'block' : 'none';
                    this.textContent = repliesContainer.style.display === 'block' ? '⮝' : '⮟';
                    showInitialReplies(repliesContainer);
                }
            });
        });
    }

    function showInitialReplies(repliesContainer) {
        const replies = Array.from(repliesContainer.querySelectorAll('.reply'));
        replies.forEach((reply, index) => reply.style.display = index < maxVisibleReplies ? 'block' : 'none');
        addShowMoreRepliesButton(replies, repliesContainer);
    }
    function addShowMoreRepliesButton(replies, repliesContainer) {
        let showMoreButton = repliesContainer.querySelector('.btn-show-more-replies');
        console.log(replies)
        if (!showMoreButton) {
            showMoreButton = document.createElement('button');
            showMoreButton.classList.add('btn-show-more-replies');
            showMoreButton.textContent = 'Show more replies';
            repliesContainer.appendChild(showMoreButton);
        }

        showMoreButton.addEventListener('click', function () {
            const currentlyHiddenReplies = replies.filter(reply => reply.style.display === 'none');

            if (showMoreButton.textContent === 'Show more replies') {
                console.log('Все комментарии:', replies);
                console.log('Скрытые комментарии перед показом:', currentlyHiddenReplies);

                currentlyHiddenReplies.slice(0, maxVisibleReplies).forEach(reply => {
                    reply.style.display = 'block';
                });

                if (currentlyHiddenReplies.length <= maxVisibleReplies) {
                    showMoreButton.textContent = 'Hide';

                    // console.log('Удаляем кнопку, так как больше нет скрытых ответов');
                    // showMoreButton.remove();
                }
            } else {
                // Скрыть все ответы
                replies.forEach(reply => reply.style.display = 'none');
                showMoreButton.textContent = 'Show more replies'; // Вернуть текст кнопки
            }
        });
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
});
