document.addEventListener('DOMContentLoaded', function () {
    const likeButtons = document.querySelectorAll('.btn-like');
    const dislikeButtons = document.querySelectorAll('.btn-dislike');
    const commentsContainer = document.querySelector('.comments-container');

    // Если commentsContainer существует, добавляем обработчик событий
    if (commentsContainer) {
        commentsContainer.addEventListener('click', function (event) {
            if (event.target.closest('.btn-like')) {
                const button = event.target.closest('.btn-like');
                const slug = button.dataset.slug;
                const user_id = button.dataset.user_id;
                const comment_id = button.dataset.comment_id;
                const course_id = button.dataset.course_id; // Добавляем курс
                handleReaction('like', button, slug, user_id, comment_id, course_id);
            }

            if (event.target.closest('.btn-dislike')) {
                const button = event.target.closest('.btn-dislike');
                const slug = button.dataset.slug;
                const user_id = button.dataset.user_id;
                const comment_id = button.dataset.comment_id;
                const course_id = button.dataset.course_id; // Добавляем курс
                handleReaction('dislike', button, slug, user_id, comment_id, course_id);
            }
        });
    }

    // Обработчики клика на лайк
    likeButtons.forEach(button => {
        const slug = button.dataset.slug;
        const user_id = button.dataset.user_id;
        const comment_id = button.dataset.comment_id;
        const course_id = button.dataset.course_id;

        button.addEventListener('click', function () {
            handleReaction('like', button, slug, user_id, comment_id, course_id);
        });
    });

    // Обработчики клика на дизлайк
    dislikeButtons.forEach(button => {
        const slug = button.dataset.slug;
        const user_id = button.dataset.user_id;
        const comment_id = button.dataset.comment_id;
        const course_id = button.dataset.course_id;

        button.addEventListener('click', function () {
            handleReaction('dislike', button, slug, user_id, comment_id, course_id);
        });
    });

    // Функция для форматирования чисел (например, 1000 -> 1K)
    function formatNumber(num) {
        if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K'; // Преобразуем 1000 в 1.0K
        }
        return num; // Оставляем оригинальное число
    }

    // Функция для отправки AJAX-запроса
    function handleReaction(type, button, slug, user_id, comment_id, course_id) {
        const url = button.getAttribute('data-url');
        const likeCount = button.closest('.reaction-buttons').querySelector('.like-count');
        const dislikeCount = button.closest('.reaction-buttons').querySelector('.dislike-count');

        // Формируем данные для отправки в зависимости от того, есть ли course_id или comment_id
        const data = { slug, reaction_type: type, user_id };

        if (course_id) {
            data.course_id = course_id; // Если есть course_id, добавляем его
        } else if (comment_id) {
            data.comment_id = comment_id; // Если есть comment_id, добавляем его
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data), // Отправляем данные с нужным ID
        })
            .then(response => response.text())  // Чтение ответа как текст
            .then(text => {
                try {
                    const data = JSON.parse(text); // Пробуем парсить как JSON
                    if (data.success) {
                        likeCount.textContent = formatNumber(data.likes);
                        dislikeCount.textContent = formatNumber(data.dislikes);
                    } else if (data.error) {
                        console.log('ERROR DETAILS:', data.details); // <-- сюда вывод доп. данных
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
