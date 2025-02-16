document.addEventListener('DOMContentLoaded', function () {
    const likeButtons = document.querySelectorAll('.btn-like');
    const dislikeButtons = document.querySelectorAll('.btn-dislike');
    const commentsContainer = document.querySelector('.comments-container');

    // Делегируем событие клика на контейнере комментариев
    commentsContainer.addEventListener('click', function (event) {
        if (event.target.closest('.btn-like')) {
            const button = event.target.closest('.btn-like');
            const slug = button.dataset.slug;
            const user_id = button.dataset.user_id;
            const comment_id = button.dataset.comment_id;
            handleReaction('like', button, slug, user_id, comment_id);
        }

        if (event.target.closest('.btn-dislike')) {
            const button = event.target.closest('.btn-dislike');
            const slug = button.dataset.slug;
            const user_id = button.dataset.user_id;
            const comment_id = button.dataset.comment_id;
            handleReaction('dislike', button, slug, user_id, comment_id);
        }
    });

    // Обработчики клика на лайк
    likeButtons.forEach(button => {
        const slug = button.dataset.slug;
        const user_id = button.dataset.user_id;
        const comment_id = button.dataset.comment_id;

        console.log(slug)
        console.log(user_id)
        console.log(comment_id)

        button.addEventListener('click', function () {
            handleReaction('like', button, slug, user_id, comment_id);
        });
    });

    // Обработчики клика на дизлайк
    dislikeButtons.forEach(button => {
        const slug = button.dataset.slug;
        const user_id = button.dataset.user_id;
        const comment_id = button.dataset.comment_id;
        button.addEventListener('click', function () {

            handleReaction('dislike', button, slug, user_id, comment_id);
        });
    });

    function formatNumber(num) {
        if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K'; // Преобразование 1000 в 1.0K
        }
        return num; // Возврат оригинального числа
    }

    // Функция для отправки AJAX-запроса
    function handleReaction(type, button, slug, user_id, comment_id) {
        const url = button.getAttribute('data-url');
        const likeCount = button.closest('.reaction-buttons').querySelector('.like-count');
        const dislikeCount = button.closest('.reaction-buttons').querySelector('.dislike-count');
        // alert("handleReaction function")
        console.log(url)
        console.log(likeCount)
        console.log(dislikeCount)

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ slug, reaction_type: type, user_id, comment_id }),
        })
            .then(response => response.text())  // Чтение ответа как текст
            .then(text => {
                console.log('Server response:', text); // Логируем весь ответ от сервера
                try {
                    const data = JSON.parse(text); // Пробуем парсить как JSON
                    if (data.success) {
                        likeCount.textContent = formatNumber(data.likes);
                        dislikeCount.textContent = formatNumber(data.dislikes);
                    } else if(data.error){
                        window.location.href = "/error?message=" + data.error;
                    }
                } catch (e) {
                    console.error('Ошибка парсинга JSON:', e);
                }
            })
            .catch(error => {
                console.error('Запрос не удался:', error);
            });

    }
});
