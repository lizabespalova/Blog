// Функция для обработки формы Follow/Unfollow
function handleFollowUnfollowForm(form) {
    form.addEventListener('submit', function (event) {
        event.preventDefault(); // Останавливаем стандартное поведение формы

        const actionUrl = form.action; // Текущий action формы
        const formData = new FormData(form); // Данные формы

        // Отправляем запрос
        fetch(actionUrl, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json()) // Ожидаем JSON
            .then(data => {
                if (data.success) {
                    const button = form.querySelector('button');
                    const followersCountSpan = document.getElementById('followers-count');

                    // Изменение состояния кнопки и URL действия
                    if (button.textContent.trim() === 'Follow') {
                        button.textContent = 'Unfollow';
                        form.setAttribute('action', actionUrl.replace('/follow/', '/unfollow/'));

                        // Увеличиваем количество подписчиков
                        if (followersCountSpan) {
                            followersCountSpan.textContent = parseInt(followersCountSpan.textContent) + 1;
                        }
                    } else {
                        button.textContent = 'Follow';
                        form.setAttribute('action', actionUrl.replace('/unfollow/', '/follow/'));

                        // Уменьшаем количество подписчиков
                        if (followersCountSpan) {
                            followersCountSpan.textContent = parseInt(followersCountSpan.textContent) - 1;
                        }
                    }
                } else {
                    alert('Something went wrong!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing your request.');
            });
    });
}

// Применение функции ко всем формам Follow/Unfollow
document.querySelectorAll('form.follow-unfollow').forEach(form => handleFollowUnfollowForm(form));

function navigateTo(url) {
    window.location.href = url;
}
// Универсальная функция фильтрации
function filterSearch(inputSelector, cardSelector, textSelector) {
    const input = document.querySelector(inputSelector).value.toLowerCase();
    const cards = document.querySelectorAll(cardSelector);
    cards.forEach(card => {
        const text = card.querySelector(textSelector).textContent.toLowerCase();
        card.style.display = text.includes(input) ? "flex" : "none";
    });
}

// Проверяем, существует ли кнопка поиска и поле ввода
const searchButton = document.getElementById('searchButton');
const searchInput = document.querySelector('.searching-input');

// Связываем с кнопкой и полем ввода
if (searchButton && searchInput) {
    // Связываем с кнопкой
    searchButton.addEventListener('click', () => {
        filterSearch('.searching-input', '.follower-card', '.follower-login');
    });

    // Связываем с вводом текста
    searchInput.addEventListener('input', () => {
        filterSearch('.searching-input', '.follower-card', '.follower-login');
    });
} else {
    console.warn('Search button or input not found on the page.');
}

