document.addEventListener('DOMContentLoaded', function () {
    const likeButtons = document.querySelectorAll('.btn-like');
    const dislikeButtons = document.querySelectorAll('.btn-dislike');
    console.log(likeButtons)
    console.log(dislikeButtons)

    // Обработчики клика на лайк
    likeButtons.forEach(button => {
        const slug = button.dataset.slug;
        const user_id = button.dataset.user_id;

        button.addEventListener('click', function () {
            handleReaction('like', button, slug, user_id);
        });
    });

    // Обработчики клика на дизлайк
    dislikeButtons.forEach(button => {
        const slug = button.dataset.slug;
        const user_id = button.dataset.user_id;

        button.addEventListener('click', function () {
            handleReaction('dislike', button, slug, user_id);
        });
    });

    function formatNumber(num) {
        if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K'; // Преобразование 1000 в 1.0K
        }
        return num; // Возврат оригинального числа
    }

    // Функция для отправки AJAX-запроса
    function handleReaction(type, button, slug, user_id) {
        const url = button.getAttribute('data-url');
        const likeCount = button.closest('.reaction-buttons').querySelector('.like-count');
        const dislikeCount = button.closest('.reaction-buttons').querySelector('.dislike-count');
        // alert("handleReaction function")
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ slug, reaction_type: type, user_id }),
        })
            .then(response => response.json())
            .then(data => {
                console.log('Server response:', data); // Логирование ответа сервера
                if (data.success) {
                    likeCount.textContent = formatNumber(data.likes); // Обновляем количество лайков
                    dislikeCount.textContent = formatNumber(data.dislikes); // Обновляем количество дизлайков
                } else {
                    console.error('Ошибка:', data.error);
                }
            })
            .catch(error => {
                console.error('Запрос не удался:', error);
            });
    }

});
